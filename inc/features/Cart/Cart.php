<?php
/**
 * Slice Cart — koszyk (port design/vanilla/koszyk.html).
 *
 * P-8.6a: Strona „Koszyk" renderuje się natywnym blokiem WooCommerce
 * `wp:woocommerce/cart`, NIE klasycznym `woocommerce/cart/*.php` (D-8.6a.1
 * — ground-truth ujawnił, że ta instalacja WC 10.9.4 domyślnie stawia stronę
 * Cart na bloku, nie na shortcodzie `[woocommerce_cart]`). Dane per-wiersz,
 * których stock cart item Woo nie ma (klasa stanu, stara cena/oszczędności —
 * kontrakt §2/§6), dostarcza WooCommerce Blocks Integration: Store API
 * `woocommerce_store_api_register_endpoint_data()` (namespace `qutlet-klasa`,
 * endpointy `cart-item` i `cart`) + JS `registerCheckoutFilters()`
 * (`CartBlocksIntegration`, `assets/js/cart-block-filters.js`).
 *
 * Mini-koszyk w headerze (`.cart-badge`/`.cart-menu` w `parts/header.html`,
 * P-8.1) renderuje się przez classic `woocommerce_add_to_cart_fragments`
 * (D-8.6a.3) — niezależnie od D-8.6a.1, mechanizm już przewidziany w
 * prototypie (`design/vanilla/js/templates.js:7`).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Cart;

use Qutlet\Theme\features\ProductPage\ProductPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap + dane Cart Block i mini-koszyka headera.
 */
final class Cart {

	/**
	 * Namespace danych rozszerzających Store API (`item.extensions.<ns>`).
	 *
	 * @var string
	 */
	const EXTENSION_NAMESPACE = 'qutlet-klasa';

	/**
	 * Podpina hooki bootstrapu (D-8.6a.1, D-8.6a.3).
	 *
	 * `woocommerce_blocks_loaded` odpala się z konstruktora `Bootstrap`
	 * (`src/Blocks/Domain/Bootstrap.php`), zwykle na `plugins_loaded` —
	 * czyli PRZED tym, jak motyw w ogóle zdąży wykonać `functions.php`
	 * (kolejność bootu WP: plugins_loaded → setup_theme). `add_action()` po
	 * fakcie nigdy by się nie odpalił, więc rejestrujemy się WPROST, gdy hook
	 * już przeleciał — ten sam wzorzec obronny co natywne
	 * `woocommerce_register_additional_checkout_field()`
	 * (`src/Blocks/Domain/Services/functions.php:17-26`).
	 *
	 * @return void
	 */
	public static function boot(): void {
		if ( did_action( 'woocommerce_blocks_loaded' ) ) {
			self::register_store_api_data();
		} else {
			add_action( 'woocommerce_blocks_loaded', array( self::class, 'register_store_api_data' ) );
		}

		add_action( 'woocommerce_blocks_cart_block_registration', array( self::class, 'register_blocks_integration' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( self::class, 'cart_fragments' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_cart_fragments' ) );
	}

	/**
	 * Ładuje `wc-cart-fragments` (D-8.6a.3) — WooCommerce od czasu Cart/Checkout
	 * Blocks TYLKO rejestruje ten handle (`WC_Frontend_Scripts::register_scripts()`),
	 * już go nie enqueue'uje automatycznie (zweryfikowane w źródle: brak
	 * `wp_enqueue_script('wc-cart-fragments')` w całym pliku) — bez tego
	 * `.cart-badge`/`.cart-menu` w headerze nigdy nie odświeżą się po dodaniu
	 * do koszyka.
	 *
	 * @return void
	 */
	public static function enqueue_cart_fragments(): void {
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	/**
	 * Rejestruje CartBlocksIntegration na `IntegrationRegistry` bloku Cart.
	 *
	 * @param \Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry $registry Rejestr integracji bloku.
	 * @return void
	 */
	public static function register_blocks_integration( $registry ): void {
		$registry->register( new CartBlocksIntegration() );
	}

	/**
	 * Rejestruje rozszerzenia schematu Store API dla `cart-item` i `cart`.
	 *
	 * @return void
	 */
	public static function register_store_api_data(): void {
		if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			return;
		}

		woocommerce_store_api_register_endpoint_data(
			array(
				// Literał 'cart-item', nie CartItemSchema::IDENTIFIER — klasa żyje w
				// src/StoreApi (WooCommerce), poza zasięgiem woocommerce-stubs (PHPStan).
				'endpoint'        => 'cart-item',
				'namespace'       => self::EXTENSION_NAMESPACE,
				'data_callback'   => array( self::class, 'cart_item_data' ),
				'schema_callback' => array( self::class, 'cart_item_schema' ),
				'schema_type'     => ARRAY_A,
			)
		);

		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => 'cart',
				'namespace'       => self::EXTENSION_NAMESPACE,
				'data_callback'   => array( self::class, 'cart_totals_data' ),
				'schema_callback' => array( self::class, 'cart_totals_schema' ),
				'schema_type'     => ARRAY_A,
			)
		);
	}

	/**
	 * Dane per-wiersz koszyka: klasa stanu + stara cena (kontrakt §2).
	 *
	 * Formatowane przez `wc_price()` PO STRONIE PHP (nie w JS) — Store API
	 * zwraca gotowy HTML, JS tylko wkleja string, bez liczenia walut.
	 *
	 * @param array $cart_item Wiersz koszyka (`WC_Cart::get_cart()`).
	 * @return array<string, string>
	 */
	public static function cart_item_data( array $cart_item ): array {
		$product = $cart_item['data'] ?? null;

		if ( ! $product instanceof \WC_Product ) {
			return array();
		}

		$product_id     = $product->get_id();
		$condition_code = (string) ProductPage::acf_field( 'klasa_stanu', $product_id );
		$market_price   = (float) ProductPage::acf_field( 'cena_rynkowa_nowego', $product_id );
		$sale_price     = (float) $product->get_price();

		return array(
			'klasa_stanu'         => $condition_code,
			'old_price_formatted' => $market_price > $sale_price ? wp_kses_post( wc_price( $market_price ) ) : '',
		);
	}

	/**
	 * Schemat pól z `cart_item_data()` (wymagany przez Store API).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function cart_item_schema(): array {
		return array(
			'klasa_stanu'         => array(
				'description' => __( 'Kod klasy stanu (A-D).', 'qutlet-theme' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'old_price_formatted' => array(
				'description' => __( 'Sformatowana cena rynkowa nowego (tylko gdy wyższa od ceny sprzedaży).', 'qutlet-theme' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);
	}

	/**
	 * Suma oszczędności całego koszyka vs. ceny rynkowe nowych produktów
	 * (kontrakt §6, odpowiednik `data-cart-savings-row` z prototypu).
	 *
	 * @return array<string, string>
	 */
	public static function cart_totals_data(): array {
		$cart          = WC()->cart;
		$total_savings = 0.0;

		foreach ( $cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'] ?? null;

			if ( ! $product instanceof \WC_Product ) {
				continue;
			}

			$market_price = (float) ProductPage::acf_field( 'cena_rynkowa_nowego', $product->get_id() );
			$sale_price   = (float) $product->get_price();

			if ( $market_price > $sale_price ) {
				$total_savings += ( $market_price - $sale_price ) * (int) $cart_item['quantity'];
			}
		}

		return array(
			// Zwykły tekst, nie wc_price() HTML: `totalValue` (JS) renderuje
			// zwracany string jako czysty tekst (bez interpretacji znaczników —
			// zweryfikowane runtime, w przeciwieństwie do `itemName`), więc HTML
			// wyszedłby na ekranie dosłownie jako escapowane `&lt;span&gt;`.
			'total_savings_text' => $total_savings > 0.0
				? html_entity_decode( wp_strip_all_tags( wc_price( $total_savings ) ), ENT_QUOTES, 'UTF-8' )
				: '',
		);
	}

	/**
	 * Schemat pola z `cart_totals_data()`.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function cart_totals_schema(): array {
		return array(
			'total_savings_text' => array(
				'description' => __( 'Suma oszczędności koszyka vs. ceny rynkowe nowych produktów (zwykły tekst).', 'qutlet-theme' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);
	}

	/**
	 * Mini-koszyk w headerze (D-8.6a.3) — fragment podmieniany przez natywny
	 * `wc-cart-fragments` po każdej zmianie koszyka.
	 *
	 * @param array<string, string> $fragments Fragmenty Woo (selector => HTML).
	 * @return array<string, string>
	 */
	public static function cart_fragments( array $fragments ): array {
		$cart  = WC()->cart;
		$count = $cart->get_cart_contents_count();

		ob_start();
		?>
		<span class="cart-badge" data-cart-count<?php echo 0 === $count ? ' hidden' : ''; ?>><?php echo esc_html( (string) $count ); ?></span>
		<?php
		$fragments['[data-cart-count]'] = trim( (string) ob_get_clean() );

		ob_start();
		self::render_cart_menu( $cart );
		$fragments['.cart-menu[data-menu="cart"]'] = trim( (string) ob_get_clean() );

		return $fragments;
	}

	/**
	 * Zawartość dropdownu `.cart-menu` (port `QT.tpl.cartMenu`, `templates.js:66`).
	 *
	 * @param \WC_Cart $cart Bieżący koszyk.
	 * @return void
	 */
	private static function render_cart_menu( \WC_Cart $cart ): void {
		$items = $cart->get_cart();
		?>
		<div class="dropdown cart-menu" data-menu="cart" hidden>
			<?php if ( empty( $items ) ) : ?>
				<div class="cart-menu-empty">
					<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 2-1.58l1.65-7.42H5.12"></path></svg>
					<div><?php esc_html_e( 'Koszyk jest pusty', 'qutlet-theme' ); ?></div>
				</div>
			<?php else : ?>
				<h4 class="cart-menu-head">
					<?php
					printf(
						/* translators: %d: liczba sztuk w koszyku. */
						esc_html__( 'Koszyk · %d', 'qutlet-theme' ),
						(int) $cart->get_cart_contents_count()
					);
					?>
				</h4>
				<div class="cart-menu-list">
					<?php foreach ( $items as $cart_item_key => $cart_item ) : ?>
						<?php
						$product = $cart_item['data'] ?? null;

						if ( ! $product instanceof \WC_Product ) {
							continue;
						}

						$condition_code = (string) ProductPage::acf_field( 'klasa_stanu', $product->get_id() );
						$image_id       = $product->get_image_id();
						$image_url      = $image_id ? wp_get_attachment_image_url( (int) $image_id, 'thumbnail' ) : '';
						?>
						<div class="cart-menu-item">
							<div class="cart-thumb cart-thumb-sm"<?php echo $image_url ? ' style="background-image:url(' . esc_url( $image_url ) . ')"' : ''; ?>></div>
							<div class="cart-menu-item-info">
								<div class="cart-menu-item-title" title="<?php echo esc_attr( $product->get_name() ); ?>"><?php echo esc_html( $product->get_name() ); ?></div>
								<div class="cart-menu-item-meta">
									<?php
									if ( '' !== $condition_code ) {
										printf(
											/* translators: 1: kod klasy stanu (A-D), 2: ilość sztuk. */
											esc_html__( 'Klasa %1$s · %2$d szt.', 'qutlet-theme' ),
											esc_html( $condition_code ),
											(int) $cart_item['quantity']
										);
									} else {
										printf(
											/* translators: %d: ilość sztuk. */
											esc_html__( '%d szt.', 'qutlet-theme' ),
											(int) $cart_item['quantity']
										);
									}
									?>
								</div>
							</div>
							<span class="cart-menu-item-price"><?php echo wp_kses_post( wc_price( (float) $product->get_price() * (int) $cart_item['quantity'] ) ); ?></span>
							<a
								class="cart-menu-item-remove"
								href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
								aria-label="<?php echo esc_attr( sprintf( /* translators: %s: nazwa produktu. */ __( 'Usuń %s', 'qutlet-theme' ), $product->get_name() ) ); ?>"
							><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg></a>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="cart-menu-foot">
					<div class="cart-menu-total">
						<span><?php esc_html_e( 'Razem', 'qutlet-theme' ); ?></span>
						<span class="cart-menu-total-val"><?php echo wp_kses_post( wc_price( (float) $cart->get_subtotal() ) ); ?></span>
					</div>
					<div class="cart-menu-actions">
						<a class="btn btn-outline" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Zobacz koszyk', 'qutlet-theme' ); ?></a>
						<a class="btn btn-primary" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Do kasy', 'qutlet-theme' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
