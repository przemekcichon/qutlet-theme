<?php
/**
 * Slice Account — moje konto + logowanie (port design/vanilla/moje-konto.html,
 * design/vanilla/logowanie.html).
 *
 * P-8.6c: w przeciwieństwie do Koszyka/Kasy (D-8.6a.1/D-8.6b.1 — bloki),
 * Strona „Moje konto" (`wc_get_page_id('myaccount')` → post 10) na tej
 * instalacji renderuje się przez CLASSIC shortcode `[woocommerce_my_account]`
 * (`post_content` dosłownie tym shortcode'em) — komentarz prototypu
 * (`moje-konto.html:13` → `woocommerce/myaccount/my-account.php`) był tym
 * razem TRAFNY. Realizacja więc klasycznymi nadpisaniami w
 * `woocommerce/myaccount/*.php`, NIE WooCommerce Blocks.
 *
 * Brak osobnej strony WP dla `logowanie.html` (D-8.6c.2): `WC_Shortcode_My_Account::output()`
 * (WooCommerce core) sam sprawdza `is_user_logged_in()` PRZED wywołaniem
 * `my-account.php` — gdy false, renderuje WYŁĄCZNIE `form-login.php` i wraca
 * (`my-account.php`/`navigation.php` w ogóle się nie ładują). Jeden URL
 * (`/moje-konto/`) obsługuje więc oba stany prototypu; osobny plik logowania
 * nie ma odpowiednika w architekturze WC.
 *
 * Metody płatności (`payment-methods` endpoint) świadomie POMINIĘTE — WC
 * chowa je natywnie (`wc_get_account_menu_items()`, `$support_payment_methods`),
 * bo żadna aktywna bramka (bacs/cheque/cod) nie wspiera
 * `PaymentGatewayFeature::ADD_PAYMENT_METHOD`/`TOKENIZATION`; pigułka
 * „Metody płatności" z prototypu to UI dla realnej tokenizacji kart, której
 * ta instalacja nie ma — budowanie fasady bez danych byłoby fikcją, nie
 * portem (znany brak, fast-follow jeśli dojdzie bramka z tokenizacją).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Account;

use Qutlet\Theme\features\Blog\Blog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap + dane pomocnicze dla nadpisań `woocommerce/myaccount/*.php`
 * i dropdownu konta w headerze.
 */
final class Account {

	/**
	 * Podpina hooki bootstrapu.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_filter( 'woocommerce_account_menu_items', array( self::class, 'menu_items' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( self::class, 'header_fragments' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_auth_tabs' ) );
	}

	/**
	 * Etykiety/kolejność nawigacji konta (port `moje-konto.html:32-37`):
	 * Pulpit, Zamówienia, Dane konta, Adres dostawy, (Metody płatności —
	 * tylko gdy WC ją zostawił), Wyloguj się. `downloads` (natywny WC, brak
	 * w prototypie — produkty outletowe nie są plikami do pobrania) świadomie
	 * pominięty przez NIE przepisanie go do wyniku.
	 *
	 * @param array<string, string> $items Natywne pozycje WC (`wc_get_account_menu_items()`).
	 * @return array<string, string>
	 */
	public static function menu_items( array $items ): array {
		$order    = array( 'dashboard', 'orders', 'edit-account', 'edit-address', 'payment-methods', 'customer-logout' );
		$labels   = array(
			'dashboard'    => __( 'Pulpit', 'qutlet-theme' ),
			'edit-account' => __( 'Dane konta', 'qutlet-theme' ),
			'edit-address' => __( 'Adres dostawy', 'qutlet-theme' ),
		);
		$reordered = array();

		foreach ( $order as $endpoint ) {
			if ( isset( $items[ $endpoint ] ) ) {
				$reordered[ $endpoint ] = $labels[ $endpoint ] ?? $items[ $endpoint ];
			}
		}

		return $reordered;
	}

	/**
	 * Kolor/etykieta pigułki statusu zamówienia (port `.status-pill`,
	 * `design/vanilla/js/templates.js:136`, `QT.STATUS_LABEL`). Rozszerzony o
	 * REALNE statusy tej instalacji spoza demo trójki (done/ship/proc):
	 * `shipped` to własny status `qutlet-core`
	 * (`\Qutlet\Core\OrderSync\OrderStatuses::STATUS_UNPREFIXED`, etykieta
	 * „Wysłane" już zarejestrowana we `wc_order_statuses` — stąd tu tylko
	 * KOLOR, etykietę bierzemy natywnie z `wc_get_order_status_name()`, nie
	 * duplikujemy tłumaczenia).
	 *
	 * @param string $status Status zamówienia BEZ prefiksu `wc-` (`WC_Order::get_status()`).
	 * @return string
	 */
	public static function status_pill_class( string $status ): string {
		switch ( $status ) {
			case 'completed':
				return 'status-done';
			case 'shipped':
				return 'status-ship';
			case 'processing':
			case 'pending':
			case 'on-hold':
				return 'status-proc';
			default:
				// cancelled/refunded/failed.
				return 'status-cancel';
		}
	}

	/**
	 * Krótka etykieta adresu dostawy na kafelku Pulpitu (port `data-addr-label`,
	 * `moje-konto.html:54`) — kod pocztowy + miasto z meta `shipping_*`, z
	 * fallbackiem na `billing_*` (adres wysyłkowy bywa pusty, gdy klient nigdy
	 * nie odróżnił go od rozliczeniowego — patrz `form-edit-address.php`).
	 *
	 * @param int $user_id ID klienta.
	 * @return string
	 */
	public static function address_short_label( int $user_id ): string {
		$city     = (string) get_user_meta( $user_id, 'shipping_city', true );
		$postcode = (string) get_user_meta( $user_id, 'shipping_postcode', true );

		if ( '' === $city && '' === $postcode ) {
			$city     = (string) get_user_meta( $user_id, 'billing_city', true );
			$postcode = (string) get_user_meta( $user_id, 'billing_postcode', true );
		}

		if ( '' === $city && '' === $postcode ) {
			return __( 'Nie ustawiono', 'qutlet-theme' );
		}

		return trim( $postcode . ' ' . $city );
	}

	/**
	 * Enqueue przełącznika zakładek Logowanie/Rejestracja (`form-login.php`) —
	 * WYŁĄCZNIE na stronie „Moje konto" (`is_account_page()`), niezależnie od
	 * stanu zalogowania (skrypt jest no-opem, gdy `.auth-tabs` nie istnieje w
	 * DOM — czyli gdy klient jest zalogowany i widzi pulpit, nie formularz).
	 *
	 * @return void
	 */
	public static function enqueue_auth_tabs(): void {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}

		wp_enqueue_script(
			'qutlet-theme-account-auth-tabs',
			get_theme_file_uri( 'assets/js/account-auth-tabs.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);
	}

	/**
	 * Dropdown konta w headerze (`.acct-menu[data-menu="account"]`,
	 * `parts/header.html:40`) + link mobilny (`[data-mnav-account]`,
	 * `parts/header.html:115`) — jak `Cart::cart_fragments()` (D-8.6a.3),
	 * bo `parts/header.html` to statyczny blok `wp:html` (FSE template part)
	 * bez PHP; jedyny kanał na dynamiczną treść to fragmenty
	 * `wc-cart-fragments` (już enqueue'owane przez `Cart::enqueue_cart_fragments()`),
	 * które odświeżają się AJAX-em zaraz po `DOMContentLoaded` — nie tylko po
	 * zmianie koszyka, WC odpytuje fragmenty na KAŻDYM ładowaniu strony.
	 *
	 * @param array<string, string> $fragments Fragmenty Woo (selector => HTML).
	 * @return array<string, string>
	 */
	public static function header_fragments( array $fragments ): array {
		ob_start();
		self::render_account_menu();
		$fragments['.acct-menu[data-menu="account"]'] = trim( (string) ob_get_clean() );

		ob_start();
		self::render_mobile_account_link();
		$fragments['[data-mnav-account]'] = trim( (string) ob_get_clean() );

		return $fragments;
	}

	/**
	 * Zawartość `.acct-menu` (port `QT.account.renderAccountMenu`,
	 * `design/vanilla/js/app.js:141-160`).
	 *
	 * @return void
	 */
	private static function render_account_menu(): void {
		$myaccount_url = wc_get_page_permalink( 'myaccount' );
		?>
		<div class="dropdown acct-menu" data-menu="account" hidden>
			<?php if ( is_user_logged_in() ) : ?>
				<?php $user = wp_get_current_user(); ?>
				<div class="acct-menu-user">
					<div class="acct-avatar"><?php echo esc_html( Blog::author_initials( (int) $user->ID ) ); ?></div>
					<div class="acct-menu-user-info">
						<div class="acct-menu-user-name"><?php echo esc_html( $user->display_name ); ?></div>
						<div class="acct-menu-user-email"><?php echo esc_html( $user->user_email ); ?></div>
					</div>
				</div>
				<a class="acct-menu-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"><?php esc_html_e( 'Pulpit', 'qutlet-theme' ); ?></a>
				<a class="acct-menu-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'Zamówienia', 'qutlet-theme' ); ?></a>
				<a class="acct-menu-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>"><?php esc_html_e( 'Dane konta', 'qutlet-theme' ); ?></a>
				<a class="acct-menu-link" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'shipping', $myaccount_url ) ); ?>"><?php esc_html_e( 'Adres dostawy', 'qutlet-theme' ); ?></a>
				<a class="acct-menu-link danger" href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>"><?php esc_html_e( 'Wyloguj się', 'qutlet-theme' ); ?></a>
			<?php else : ?>
				<a class="acct-menu-link primary" href="<?php echo esc_url( $myaccount_url ); ?>"><?php esc_html_e( 'Zaloguj się', 'qutlet-theme' ); ?></a>
				<a class="acct-menu-link" href="<?php echo esc_url( $myaccount_url . '#register' ); ?>"><?php esc_html_e( 'Zarejestruj się', 'qutlet-theme' ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Link „Moje konto"/„Zaloguj się" w mobilnym menu (port `app.js:204-205`,
	 * `data-mnav-account`).
	 *
	 * @return void
	 */
	private static function render_mobile_account_link(): void {
		$myaccount_url = wc_get_page_permalink( 'myaccount' );
		$label         = is_user_logged_in() ? __( 'Moje konto', 'qutlet-theme' ) : __( 'Zaloguj się', 'qutlet-theme' );
		?>
		<a href="<?php echo esc_url( $myaccount_url ); ?>" class="mnav-link" data-mnav-account><?php echo esc_html( $label ); ?></a>
		<?php
	}
}
