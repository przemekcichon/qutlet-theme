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
 * Wersja motywu (jedno źródło prawdy — używać zamiast literału).
 */
const VERSION = '0.1.0';

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

	// Motyw dostarcza własny wrapper (.wrap w woocommerce/content-single-product.php
	// i analogicznie w P-8.6a/b/c) oraz własny breadcrumb portowany z prototypu —
	// domyślne hooki Woo (na `wc-template-hooks.php`) dublowałyby oba: otwierałyby
	// nieużywany <div id="primary"><main id="main"> i drukowały nieostylowany
	// .woocommerce-breadcrumb OBOK naszego. Standardowa integracja motywu z Woo
	// (identyczna do tej w motywach domyślnych WP, np. class-wc-twenty*.php).
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

	// P-8.3a (woocommerce/archive-product.php): toolbar domyślny Woo (licznik
	// wyników + sortowanie) zdjęty świadomie — to nieostylowany markup spoza
	// zakresu tego punktu (sort/filtry = P-8.3b). Tytuł archiwum motyw buduje
	// sam (`woocommerce_page_title()` wprost w szablonie), więc nagłówek
	// taksonomii też zdjęty, żeby się nie zdublował.
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );
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
