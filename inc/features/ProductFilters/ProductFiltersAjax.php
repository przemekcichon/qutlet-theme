<?php
/**
 * Slice ProductFilters — AJAX progressive enhancement nad P-8.3b/D-8.3b.1
 * (P-8.3d, punkt wielorepowy → P-8.3d-meta + P-8.3d-theme; ta klasa jest
 * P-8.3d-theme). Podmienia klasyczne przeładowanie strony na `fetch()` DO
 * TEGO SAMEGO URL-a (D-8.3d.1) — żadnego dedykowanego REST endpointu, żeby
 * nie musieć symulować kontekstu głównego zapytania, na którym operują hooki
 * `Qutlet\Core\ProductFilters\ProductFilterQuery` (`is_main_query()`).
 *
 * Mechanizm (D-8.3d.2/D-8.3d.3): dla żądania oznaczonego nagłówkiem
 * `X-Qutlet-Ajax-Filters`, na obsługiwanym archiwum
 * (`ProductFilters::is_supported_archive()`), przechwytujemy DOKŁADNIE
 * fragment między `woocommerce_before_shop_loop` a `woocommerce_after_shop_loop`
 * (toolbar/chipy/szuflada + siatka + paginacja — rdzeń
 * `woocommerce/templates/archive-product.php`) przez podwójny output buffer:
 * zewnętrzny (od `template_redirect`, połyka header/breadcrumb/
 * `woocommerce_shop_loop_header`) i wewnętrzny (dokładnie ten fragment, bez
 * potrzeby parsowania HTML-a). Odpowiedź to JSON z gotowym HTML-em fragmentu
 * — reszta strony (`woocommerce_after_main_content`/sidebar/`get_footer()`)
 * nigdy się nie renderuje.
 *
 * `<div id="qutlet-archive-results">` (echo na `woocommerce_before_shop_loop`/
 * `_after_shop_loop`, priorytety -10/1000) to punkt zaczepienia w DOM na OBIE
 * ścieżki (zwykłe przeładowanie i AJAX) — jeden stabilny węzeł do podmiany
 * `outerHTML` po stronie JS (`assets/js/product-filters-ajax.js`). WYJĄTEK
 * (odkryty niezależną recenzją PR-a, sesja 2026-08-16): gdy filtr zwraca ZERO
 * wyników, `archive-product.php` (rdzeń WooCommerce) w ogóle nie odpala
 * `before/after_shop_loop` — idzie gałęzią `woocommerce_no_products_found`
 * zamiast nią (`woocommerce_product_loop()` = `have_posts() || …`, fałsz przy
 * zerze trafień, nie tylko gdy cały sklep jest pusty). Klasyczne przeładowanie
 * w tej gałęzi NIE dostaje więc żadnego wrappera (pre-existing luka z
 * P-8.3b/P-8.3c, formularz filtrów znika razem z siatką — zgłoszone do planu,
 * NIE naprawiane w P-8.3d). AJAX-owa gałąź (`send_no_products_fragment_and_exit()`)
 * buduje wrapper ręcznie wokół realnego komunikatu WooCommerce, żeby przy
 * zero wynikach nie zwracać pustego, martwego panelu.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\ProductFilters;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX progressive enhancement nad klasycznym GET+WP_Query filtrem archiwum.
 */
final class ProductFiltersAjax {

	/** Nazwa nagłówka HTTP, którym JS oznacza żądanie AJAX (D-8.3d.1). */
	public const AJAX_HEADER_SERVER_KEY = 'HTTP_X_QUTLET_AJAX_FILTERS';

	/** ID węzła DOM podmienianego przez JS (D-8.3d.2) — trwały na obie ścieżki. */
	public const RESULTS_WRAPPER_ID = 'qutlet-archive-results';

	/**
	 * Podpina wrapper-div (obie ścieżki) + przechwycenie AJAX (tylko żądania
	 * oznaczone nagłówkiem) + enqueue JS.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'woocommerce_before_shop_loop', array( self::class, 'open_results_wrapper' ), -10 );
		add_action( 'woocommerce_after_shop_loop', array( self::class, 'close_results_wrapper' ), 1000 );

		add_action( 'template_redirect', array( self::class, 'maybe_intercept' ) );

		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
	}

	/**
	 * Czy bieżące żądanie deklaruje się jako AJAX-owe wywołanie filtra
	 * (nagłówek `X-Qutlet-Ajax-Filters`, ustawiany WYŁĄCZNIE przez
	 * `product-filters-ajax.js`).
	 *
	 * @return bool
	 */
	private static function is_ajax_filter_request(): bool {
		return ! empty( $_SERVER[ self::AJAX_HEADER_SERVER_KEY ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- odczyt obecności nagłówka (bool), wartość nieużywana.
	}

	/**
	 * Echo otwierający `<div id="qutlet-archive-results">` — na
	 * `woocommerce_before_shop_loop`, priorytet -10 (PRZED notices/toolbarem
	 * z `ProductFilters::render()`, priorytet domyślny 10).
	 *
	 * @return void
	 */
	public static function open_results_wrapper(): void {
		printf( '<div id="%s">', esc_attr( self::RESULTS_WRAPPER_ID ) );
	}

	/**
	 * Echo zamykający `</div>` — na `woocommerce_after_shop_loop`, priorytet
	 * 1000 (PO paginacji, `woocommerce_pagination()` ma priorytet domyślny 10).
	 *
	 * @return void
	 */
	public static function close_results_wrapper(): void {
		echo '</div>';
	}

	/**
	 * Na obsługiwanym archiwum, dla żądania oznaczonego nagłówkiem AJAX,
	 * podpina podwójny output buffer (D-8.3d.3) i wysyła JSON zamiast
	 * pełnej strony.
	 *
	 * @return void
	 */
	public static function maybe_intercept(): void {
		if ( ! self::is_ajax_filter_request() || ! ProductFilters::is_supported_archive() ) {
			return;
		}

		// Zewnętrzny bufor: połyka header/breadcrumb/`woocommerce_shop_loop_header`
		// — nic z tego nie trafia do odpowiedzi JSON.
		ob_start();

		// Wewnętrzny bufor: startuje PRZED echo otwierającego wrappera
		// (priorytet -20, czyli przed -10), więc złapie DOKŁADNIE fragment
		// z D-8.3d.2 (wrapper + toolbar + siatka + paginacja).
		add_action( 'woocommerce_before_shop_loop', array( self::class, 'start_inner_buffer' ), -20 );
		add_action( 'woocommerce_after_shop_loop', array( self::class, 'send_fragment_and_exit' ), 1001 );

		// `woocommerce_product_loop()` (rdzeń WooCommerce, wc-template-functions.php)
		// to `have_posts() || 'products' !== woocommerce_get_loop_display_mode()`
		// — czyli fałsz przy KAŻDYM zestawie filtrów bez trafień, nie tylko gdy
		// cały sklep jest pusty. `archive-product.php` idzie wtedy gałęzią
		// `woocommerce_no_products_found` zamiast `before/after_shop_loop` —
		// nasze hooki wrapper-diva (-10/1000) i wewnętrzny bufor (-20) w ogóle
		// się nie odpalają. Łapiemy więc realny komunikat WooCommerce
		// (`wc_no_products_found()`, priorytet 10 na TYM SAMYM hooku) własnym
		// buforem PRZED nim (priorytet 0) i domykamy PO nim (priorytet 1001) —
		// bez tego AJAX zwracałby pusty, martwy fragment zamiast komunikatu,
		// który klasyczne przeładowanie i tak pokazuje (regres wykryty
		// niezależną recenzją PR-a, sesja 2026-08-16).
		add_action( 'woocommerce_no_products_found', array( self::class, 'start_inner_buffer' ), 0 );
		add_action( 'woocommerce_no_products_found', array( self::class, 'send_no_products_fragment_and_exit' ), 1001 );
	}

	/**
	 * Startuje wewnętrzny bufor — owinięte we własną metodę (zamiast
	 * `add_action(..., 'ob_start', ...)` wprost), bo `ob_start()` zwraca
	 * `bool`, a callback akcji nie powinien nic zwracać.
	 *
	 * @return void
	 */
	public static function start_inner_buffer(): void {
		ob_start();
	}

	/**
	 * Kończy wewnętrzny bufor (dokładnie fragment D-8.3d.2), odrzuca
	 * zewnętrzny (header/breadcrumb), wysyła JSON i kończy żądanie.
	 *
	 * @return void
	 */
	public static function send_fragment_and_exit(): void {
		$fragment = ob_get_clean(); // wewnętrzny bufor.
		ob_end_clean(); // zewnętrzny bufor — odrzucony.

		self::send_json( (string) $fragment );
	}

	/**
	 * Wariant dla gałęzi `woocommerce_no_products_found` (patrz komentarz w
	 * `maybe_intercept()`) — `open_results_wrapper()`/`close_results_wrapper()`
	 * nie odpaliły się w tej gałęzi (żyją na `before/after_shop_loop`), więc
	 * wrapper budujemy tu ręcznie WOKÓŁ realnego komunikatu WooCommerce
	 * (`wc_no_products_found()`), złapanego wewnętrznym buforem.
	 *
	 * @return void
	 */
	public static function send_no_products_fragment_and_exit(): void {
		$message = ob_get_clean(); // wewnętrzny bufor (komunikat „brak produktów").
		ob_end_clean(); // zewnętrzny bufor — odrzucony.

		self::send_json( sprintf( '<div id="%s">%s</div>', esc_attr( self::RESULTS_WRAPPER_ID ), $message ) );
	}

	/**
	 * Wysyła `{success:true, data:{html: "..."}}` i kończy żądanie —
	 * ręcznie, zamiast `wp_send_json_success()`, żeby nie wchodzić w
	 * zależność od `wp_die()`/`doing_ajax` (to zwykłe żądanie strony, nie
	 * `admin-ajax.php`).
	 *
	 * @param string $html Fragment HTML (D-8.3d.2).
	 * @return void
	 */
	private static function send_json( string $html ): void {
		if ( ! headers_sent() ) {
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		}

		echo wp_json_encode(
			array(
				'success' => true,
				'data'    => array( 'html' => $html ),
			)
		);

		exit;
	}

	/**
	 * Ładuje JS AJAX-a WYŁĄCZNIE na obsługiwanych archiwach, PO
	 * `product-filters.js` (zależność — reużywa jego wzorca delegacji, nie
	 * duplikuje drawer/readout).
	 *
	 * @return void
	 */
	public static function enqueue(): void {
		if ( ! ProductFilters::is_supported_archive() ) {
			return;
		}

		wp_enqueue_script(
			'qutlet-theme-product-filters-ajax',
			get_theme_file_uri( 'assets/js/product-filters-ajax.js' ),
			array( 'qutlet-theme-product-filters' ),
			\Qutlet\Theme\VERSION,
			true
		);

		wp_localize_script(
			'qutlet-theme-product-filters-ajax',
			'qutletFiltersAjax',
			array(
				'headerName' => 'X-Qutlet-Ajax-Filters',
				'resultsId'  => self::RESULTS_WRAPPER_ID,
			)
		);
	}
}
