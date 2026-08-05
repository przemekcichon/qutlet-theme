<?php
/**
 * Qutlet Theme — cienki bootstrap motywu blokowego.
 *
 * Motyw to WYŁĄCZNIE warstwa graficzna: renderuje dane dostarczane przez
 * qutlet-core (WooCommerce + pola ACF). NIE rejestruje pól ACF ani CPT i NIE
 * zawiera glue do WooCommerce (granica artefaktów — patrz CLAUDE.md).
 *
 * FAZA 0 = czysty szkielet. Ten plik trzyma tylko: guard autoloadera (D-G1),
 * guard zależności (D-G5) i placeholder enqueue arkusza. Slice'y imperatywne
 * (block bindings, dynamiczne patterny, glue do renderu) z inc/features/ wpina
 * się tu w kolejnych fazach (render, FAZA 8).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme;

// Blokada bezpośredniego wywołania pliku poza WordPressem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wersja motywu (jedno źródło prawdy — używać zamiast literału). Steruje
 * cache-bustingiem `?ver=` na WSZYSTKICH enqueue'owanych assetach (style.css,
 * JS edytorów bloków) — realny błąd tej sesji: bumpowanie WYŁĄCZNIE nagłówka
 * `Version:` w style.css (który steruje TYLKO cache'em patternów, patrz
 * Patterns.php) zostawiało tę stałą bez zmian, więc przeglądarki nadal
 * dostawały `style.css?ver=0.1.0` i serwowały stary, scache'owany plik mimo
 * realnych zmian w CSS na dysku. Bumpować OBIE wartości razem.
 */
const VERSION = '0.1.50';

/*
 * Autoloader Composera (D-G1): ładowany z guardem. Brak `vendor/autoload.php`
 * NIE jest fatal errorem i — inaczej niż w pluginach — NIE przerywa motywu
 * ("bez bail"): pokazujemy notice w adminie i pomijamy WYŁĄCZNIE ładowanie
 * slice'ów imperatywnych. Motyw dalej renderuje szablony i wystawia arkusz.
 */
$qutlet_theme_autoload = __DIR__ . '/vendor/autoload.php';

if ( is_readable( $qutlet_theme_autoload ) ) {
	require_once $qutlet_theme_autoload;

	\Qutlet\Theme\features\HeaderNav\HeaderNav::boot();
	\Qutlet\Theme\features\ProductPage\ProductPage::boot();
	\Qutlet\Theme\features\ProductFilters\ProductFilters::boot();
	\Qutlet\Theme\features\Blog\Blog::boot();
	\Qutlet\Theme\features\Blog\Blocks::boot();
	\Qutlet\Theme\features\Help\Help::boot();
	\Qutlet\Theme\features\Home\Blocks::boot();
	\Qutlet\Theme\features\Patterns\Patterns::boot();
	\Qutlet\Theme\features\Cart\Cart::boot();
} else {
	add_action( 'admin_notices', __NAMESPACE__ . '\\render_missing_autoloader_notice' );
}

// Zależności miękko weryfikujemy w odpowiedniku plugins_loaded dla motywu.
add_action( 'after_setup_theme', __NAMESPACE__ . '\\check_dependencies' );

// Deklaracja wsparcia WooCommerce (D-8.G1: motyw nadpisuje jego szablony —
// P-8.2a startuje od woocommerce/content-single-product.php). Bez własnej
// galerii Woo (zoom/slider/lightbox) — strona produktu ma własną, portowaną
// z prototypu (assets/js/product-gallery.js).
add_action( 'after_setup_theme', __NAMESPACE__ . '\\add_woocommerce_theme_support' );

// P-8.5: strony pomocy (page-{slug}.php, Help::boot()) renderują się przez
// klasyczną hierarchię szablonów, nie `templates/*.html` (blog — P-8.4 — od
// P-11.3 przeszedł na blokowe templates/home.html i pozostałe, patrz nagłówek
// inc/features/Blog/Blog.php; ten support zostaje WYŁĄCZNIE dla Help). Klasyczne
// szablony omijają `template-canvas.php`, który w motywach blokowych dodaje
// BEZWARUNKOWY <title> niezależnie od wsparcia motywu (`locate_block_template()`
// podmienia hook na `_block_template_render_title_tag`) — bez jawnego
// `title-tag` support klasyczne strony pomocy zostałyby bez <title> w <head>.
add_action( 'after_setup_theme', __NAMESPACE__ . '\\add_classic_template_support' );

// P-11.1b: canvas edytora blokowego (Site Editor + edytor Strony/wpisu) ma
// ładować TEN SAM arkusz co produkcja — bez tego patterny z P-11.1
// (.how-hero, .eko-card, .quote-band itd.) widać w edytorze tylko z paletą
// theme.json, bez custom CSS z P-8.5.
add_action( 'after_setup_theme', __NAMESPACE__ . '\\add_editor_style_support' );

// Placeholder enqueue: rejestrujemy arkusz motywu (na razie pusty — FAZA 8).
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );

/**
 * Guard zależności motywu (D-G5): WooCommerce + qutlet-core.
 *
 * Dla motywu robimy NOTICE bez bail — brak zależności nie może wywrócić
 * renderu (motyw zawsze musi coś wyświetlić). Wpięte w `after_setup_theme`,
 * bo `functions.php` ładuje się już PO `plugins_loaded` — to jest jego
 * odpowiednik dla motywu.
 *
 * @return void
 */
function check_dependencies(): void {
	if ( dependencies_met() ) {
		return;
	}

	add_action( 'admin_notices', __NAMESPACE__ . '\\render_missing_dependencies_notice' );
}

/**
 * Sprawdza obecność zależności motywu (D-G5): WooCommerce + qutlet-core.
 *
 * Literały wykrywania sprawdzone w realnym kodzie: WooCommerce definiuje klasę
 * `WooCommerce`; qutlet-core definiuje stałą `Qutlet\Core\VERSION` bezwarunkowo
 * na początku pliku głównego (przed guardem autoloadera), więc jest ustawiona,
 * gdy wtyczka jest wczytana. Oba testy to literały — nie wymagają stubów.
 *
 * @return bool True, gdy oba wymagania są obecne.
 */
function dependencies_met(): bool {
	return class_exists( 'WooCommerce' ) && defined( 'Qutlet\\Core\\VERSION' );
}

/**
 * Placeholder enqueue arkusza motywu.
 *
 * FAZA 0: arkusz `style.css` jest pusty (poza nagłówkiem). Rejestrujemy go już
 * teraz, żeby wersjonowanie i punkt zaczepienia istniały od bootstrapu; reguły
 * stylu i realne assety dochodzą w fazie renderu (FAZA 8).
 *
 * @return void
 */
function enqueue_assets(): void {
	wp_enqueue_style(
		'qutlet-theme',
		get_stylesheet_uri(),
		array(),
		VERSION
	);
}

/**
 * Deklaruje wsparcie WooCommerce (dokumentowana konwencja motywu przejmującego
 * nadpisania szablonów — `woocommerce/` w korzeniu motywu, P-8.2a).
 *
 * Bez `wc-product-gallery-zoom`/`-slider`/`-lightbox`: strona produktu nie
 * korzysta z natywnego renderu galerii Woo — własny markup + JS portowane
 * z prototypu (woocommerce/content-single-product.php, assets/js/product-gallery.js).
 *
 * @return void
 */
function add_woocommerce_theme_support(): void {
	add_theme_support( 'woocommerce' );

	// Motyw dostarcza własny wrapper (.wrap w woocommerce/content-single-product.php)
	// oraz własny breadcrumb portowany z prototypu — domyślne hooki Woo (na
	// `wc-template-hooks.php`) dublowałyby oba: otwierałyby nieużywany
	// <div id="primary"><main id="main"> i drukowały nieostylowany
	// .woocommerce-breadcrumb OBOK naszego. Standardowa integracja motywu z Woo
	// (identyczna do tej w motywach domyślnych WP, np. class-wc-twenty*.php).
	// P-8.6a (koszyk) NIE korzysta z tego wrappera — Strona „Koszyk" renderuje
	// się blokiem `wp:woocommerce/cart`, nie klasycznym szablonem (D-8.6a.1).
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

	// P-8.3a: toolbar domyślny Woo na archiwum (licznik wyników + sortowanie)
	// zdjęty świadomie — to nieostylowany markup spoza zakresu tego punktu.
	// Nagłówek/tytuł archiwum jest hardkodowany przez WooCommerce Blocks
	// (ClassicTemplate::render_archive_product() — theme nie ma tam punktu
	// nadpisania, patrz woocommerce/content-product.php), więc świadomie NIE
	// zdejmujemy tu żadnego hooka nagłówkowego.
	// P-8.3b: WŁASNY toolbar (filtry marka/klasa stanu/cena + sortowanie,
	// ProductFilters::render()) zastępuje oba zdjęte hooki, spięty z powrotem
	// na tym samym `woocommerce_before_shop_loop` (patrz boot() wyżej).
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
}

/**
 * Wsparcie potrzebne WYŁĄCZNIE przez klasyczne szablony bloga (P-8.4) — patrz
 * komentarz przy rejestracji hooka wyżej.
 *
 * @return void
 */
function add_classic_template_support(): void {
	add_theme_support( 'title-tag' );
}

/**
 * P-11.1b: canvas edytora blokowego (post/page editor + Site Editor,
 * renderowany w iframe od WP 5.9+) ma ładować `style.css` — TEN SAM plik co
 * front (`enqueue_assets()` wyżej), bez duplikowania CSS w drugim arkuszu.
 * Bez tego klasy patternów z P-11.1 (`.how-hero`, `.eko-card`, `.quote-band`
 * itd.) w edytorze mają tylko wartości z `theme.json` (paleta/typografia),
 * nie custom CSS z P-8.5.
 *
 * `:root` w `style.css` jest samowystarczalny — niezależny od zmiennych
 * `--wp--preset--*` generowanych z `theme.json` (patrz nagłówek pliku) —
 * arkusz działa w edytorze bez modyfikacji, ground-truth przy starcie
 * punktu: reguły kluczowane klasami `<body>` dogrywanymi tylko na froncie
 * (`body.allegro-off` — ProductPage::body_class(), `body.qt-hide-nlband` —
 * Help::filter_body_class()) dotyczą kontekstu strony/produktu, nie stylu
 * samych patternów, więc ich brak w edytorze jest neutralny dla parytetu
 * wizualnego z P-11.1.
 *
 * @return void
 */
function add_editor_style_support(): void {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
}

/**
 * Notice w adminie: brak autoloadera Composera.
 *
 * @return void
 */
function render_missing_autoloader_notice(): void {
	$message = __(
		'Qutlet Theme: brak autoloadera Composera (vendor/autoload.php). Uruchom „composer install" w katalogu motywu. Motyw działa, ale bez kodu imperatywnego z inc/.',
		'qutlet-theme'
	);

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html( $message )
	);
}

/**
 * Notice w adminie: brak zależności motywu (WooCommerce / qutlet-core).
 *
 * @return void
 */
function render_missing_dependencies_notice(): void {
	$message = __(
		'Qutlet Theme wymaga aktywnych wtyczek WooCommerce oraz Qutlet Core. Do czasu ich aktywacji część treści może się nie renderować.',
		'qutlet-theme'
	);

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html( $message )
	);
}
