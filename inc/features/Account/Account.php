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
		add_action( 'wp_head', array( self::class, 'print_login_state_marker' ) );
	}

	/**
	 * Etykiety/kolejność nawigacji konta (port `moje-konto.html:32-37`):
	 * Pulpit, Zamówienia, Dane konta, Adres dostawy, (Metody płatności —
	 * tylko gdy WC ją zostawił), Wyloguj się. `downloads` (natywny WC, brak
	 * w prototypie — produkty outletowe nie są plikami do pobrania) świadomie
	 * pominięty przez NIE przepisanie go do wyniku.
	 *
	 * Kolejność bazowa to ALLOW-LISTA (`$order`) TYLKO dla znanych endpointów,
	 * które ten port świadomie przestylowuje/przenazywa — każdy INNY endpoint
	 * (wtyczka, przyszły punkt planu) doklejany jest na końcu PRZED wylogowaniem,
	 * nie gubiony (poprawka po niezależnej recenzji PR #24 — pierwsza wersja
	 * była twardą allow-listą 6 endpointów, więc każdy nowy endpoint znikałby
	 * z nawigacji po cichu).
	 *
	 * @param array<string, string> $items Natywne pozycje WC (`wc_get_account_menu_items()`).
	 * @return array<string, string>
	 */
	public static function menu_items( array $items ): array {
		$order  = array( 'dashboard', 'orders', 'edit-account', 'edit-address', 'payment-methods' );
		$labels = array(
			'dashboard'    => __( 'Pulpit', 'qutlet-theme' ),
			'edit-account' => __( 'Dane konta', 'qutlet-theme' ),
			'edit-address' => __( 'Adres dostawy', 'qutlet-theme' ),
		);
		$known  = array_flip( array_merge( $order, array( 'customer-logout', 'downloads' ) ) );
		$reordered = array();

		foreach ( $order as $endpoint ) {
			if ( isset( $items[ $endpoint ] ) ) {
				$reordered[ $endpoint ] = $labels[ $endpoint ] ?? $items[ $endpoint ];
			}
		}

		foreach ( $items as $endpoint => $label ) {
			if ( ! isset( $known[ $endpoint ] ) ) {
				$reordered[ $endpoint ] = $label;
			}
		}

		if ( isset( $items['customer-logout'] ) ) {
			$reordered['customer-logout'] = $items['customer-logout'];
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
	 * bez PHP; jedyny kanał na dynamiczną treść to fragmenty `wc-cart-fragments`
	 * (już enqueue'owane przez `Cart::enqueue_cart_fragments()`).
	 *
	 * UWAGA (poprawka po niezależnej recenzji PR #24): `wc-cart-fragments.js`
	 * NIE odpytuje AJAX-em na każdym ładowaniu strony — cache'uje fragmenty w
	 * `sessionStorage` i pomija sieć, gdy hash koszyka w cache zgadza się z
	 * ciasteczkiem `woocommerce_cart_hash` (`cart-fragments.js:144`). Ten hash
	 * NIE zależy od stanu zalogowania, więc przy PUSTYM koszyku (hash się nie
	 * zmienia) zalogowanie/wylogowanie w tej samej karcie potrafiło zostawić w
	 * DOM dane poprzedniego klienta (imię, e-mail) z cache'u — zweryfikowane
	 * runtime w recenzji. `print_login_state_marker()` wymusza odświeżenie
	 * cache'u przy KAŻDEJ zmianie stanu zalogowania, niezależnie od hasha koszyka.
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
	 * Wymusza świeże pobranie fragmentów `wc-cart-fragments`, gdy stan
	 * zalogowania różni się od ostatniego zapamiętanego w `sessionStorage`
	 * (patrz UWAGA przy `header_fragments()`) — kasuje klucze cache'u
	 * (`cart_hash_key`/`fragment_name`, TE SAME formuły co
	 * `WC_Frontend_Scripts::load_scripts()`, więc trafiają dokładnie w klucze,
	 * których czyta `cart-fragments.js:11,53`), zanim ten skrypt (ładowany w
	 * stopce) zdąży odczytać stary cache. Musi wykonać się w `wp_head`
	 * (przed jakimkolwiek skryptem w stopce, niezależnie od jQuery ready) —
	 * inline, bez zależności od `wc_cart_fragments_params` (ten obiekt
	 * lokalizuje się dopiero przy samym skrypcie w stopce).
	 *
	 * @return void
	 */
	public static function print_login_state_marker(): void {
		$blog_id      = get_current_blog_id();
		$default_seed = $blog_id . '_' . get_site_url( $blog_id, '/' ) . get_template();
		$cart_hash_key = apply_filters( 'woocommerce_cart_hash_key', 'wc_cart_hash_' . md5( $default_seed ) );
		$fragment_name = apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments_' . md5( $default_seed ) );
		$logged_in     = is_user_logged_in() ? '1' : '0';
		?>
		<script>
		( function () {
			try {
				var marker = 'qutlet_account_logged_in';
				var current = '<?php echo esc_js( $logged_in ); ?>';
				if ( sessionStorage.getItem( marker ) !== current ) {
					sessionStorage.removeItem( '<?php echo esc_js( $cart_hash_key ); ?>' );
					sessionStorage.removeItem( '<?php echo esc_js( $fragment_name ); ?>' );
					sessionStorage.setItem( marker, current );
				}
			} catch ( e ) {}
		} )();
		</script>
		<?php
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
