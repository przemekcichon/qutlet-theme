<?php
/**
 * Slice ProductPage — render strony produktu (port design/vanilla/produkt.html).
 *
 * P-8.2a: szkielet + galeria + nagłówek. P-8.2b: przełącznik kanału zakupu
 * (taby Qutlet/Allegro + buybar + sekcja „jak-to-dziala"), pełna semantyka
 * D-8.G1. Sekcja treści/specyfikacji (P-8.2c) rozbuduje ten slice w kolejnym
 * punkcie FAZY 8. Markup mieszka w woocommerce/content-single-product.php
 * (nadpisanie szablonu WooCommerce); ta klasa trzyma enqueue JS + pomocnicze
 * funkcje odczytu/prezentacji współdzielone przez ten szablon.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\ProductPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue interakcji galerii + pomocnicze funkcje prezentacyjne.
 */
final class ProductPage {

	/**
	 * Podpina enqueue pod wp_enqueue_scripts.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
		add_filter( 'body_class', array( self::class, 'body_class' ) );
	}

	/**
	 * Rejestruje i ładuje JS strony produktu — wyłącznie na stronie produktu.
	 *
	 * @return void
	 */
	public static function enqueue(): void {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		wp_enqueue_script(
			'qutlet-theme-product-gallery',
			get_theme_file_uri( 'assets/js/product-gallery.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);

		wp_enqueue_script(
			'qutlet-theme-product-buy-tabs',
			get_theme_file_uri( 'assets/js/product-buy-tabs.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);
	}

	/**
	 * Dokłada `body.allegro-off` na stronie produktu z wyłączonym kanałem
	 * Allegro (D-8.G1) — jedyny cel: CSS-owy przełącznik `.info-3col` → 2
	 * kolumny w sekcji „Dostawa i zwroty" (`docs/kontrakt-danych.md` §4,
	 * „Semantyka renderu"). Elementy `[data-allegro-only]` motyw i tak NIE
	 * renderuje przy wyłączonym kanale (patrz szablon) — ta klasa istnieje
	 * wyłącznie dla layoutu, nie dla ukrywania markupu.
	 *
	 * @param string[] $classes Klasy `<body>` zebrane przez WP.
	 * @return string[]
	 */
	public static function body_class( array $classes ): array {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return $classes;
		}

		$product_id = get_queried_object_id();

		if ( $product_id && ! self::is_allegro_enabled( $product_id ) ) {
			$classes[] = 'allegro-off';
		}

		return $classes;
	}

	/**
	 * Odczyt pola ACF z fallbackiem na post meta, gdyby ACF Pro nie było aktywne
	 * (motyw nie ma prawa fatalować — D-G5 dotyczy jego zależności, nie ACF
	 * bezpośrednio, bo to zależność core, nie theme).
	 *
	 * @param string $key     Literał pola (kontrakt-danych.md §2, VERBATIM).
	 * @param int    $post_id Id produktu.
	 * @return mixed
	 */
	public static function acf_field( string $key, int $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $key, $post_id );
		}

		return get_post_meta( $post_id, $key, true );
	}

	/**
	 * Etykieta klasy stanu — słownik A→„Jak nowy" itd. (kontrakt §6, `data.js` QT.COND).
	 *
	 * @param string $code Literał klasy stanu (`A`, `B`, `C`, `D`).
	 * @return string Pusty string, gdy kod nieznany.
	 */
	public static function condition_label( string $code ): string {
		$labels = array(
			'A' => __( 'Jak nowy', 'qutlet-theme' ),
			'B' => __( 'Dobry', 'qutlet-theme' ),
			'C' => __( 'Mocne ślady', 'qutlet-theme' ),
			'D' => __( 'Na części', 'qutlet-theme' ),
		);

		return $labels[ $code ] ?? '';
	}

	/**
	 * Rabat „-X%" liczony z ceny rynkowej nowego vs cena sprzedaży (kontrakt §6,
	 * `data.js` QT.savePct). NIE przechowywane — liczone przez motyw.
	 *
	 * @param float $sale_price   Cena sprzedaży (Woo `_price`).
	 * @param float $market_price Cena rynkowa nowego (ACF `cena_rynkowa_nowego`).
	 * @return int Zaokrąglony procent rabatu; 0, gdy cena rynkowa nie jest dodatnia.
	 */
	public static function save_percent( float $sale_price, float $market_price ): int {
		if ( $market_price <= 0.0 ) {
			return 0;
		}

		return (int) round( ( 1 - $sale_price / $market_price ) * 100 );
	}

	/**
	 * Czy kanał Allegro jest włączony dla produktu (kontrakt §4, „Semantyka
	 * renderu"): `allegro_wlaczone=false` LUB pusty `allegro_url` → kanał
	 * wyłączony, motyw nie renderuje `[data-allegro-only]`.
	 *
	 * @param int $post_id Id produktu.
	 * @return bool
	 */
	public static function is_allegro_enabled( int $post_id ): bool {
		$enabled = (bool) self::acf_field( 'allegro_wlaczone', $post_id );
		$url     = (string) self::acf_field( 'allegro_url', $post_id );

		return $enabled && '' !== trim( $url );
	}

	/**
	 * Cena jako czysty tekst (bez znaczników `wc_price()`) — potrzebne tam,
	 * gdzie cena trafia do atrybutu `data-*` czytanego przez JS
	 * (`assets/js/product-buy-tabs.js`, aktualizacja ceny w buybarze przy
	 * przełączeniu taba).
	 *
	 * @param float $price Cena w groszach/PLN (jak `wc_price()`).
	 * @return string
	 */
	public static function price_text( float $price ): string {
		if ( ! function_exists( 'wc_price' ) ) {
			return '';
		}

		return trim( wp_strip_all_tags( wc_price( $price ) ) );
	}

	/**
	 * Wstrzykuje pogrubiony fragment do przetłumaczalnego szablonu — statyczna
	 * treść informacyjna portowana z prototypu (kontrakt §4, „Brak pola
	 * perks/korzyści": korzyści kanału Allegro to treść szablonu, NIE dane
	 * produktu), więc treść jest literałem w szablonie, nie z ACF.
	 *
	 * @param string $template Tekst z JEDNYM `%s` (miejsce na pogrubienie).
	 * @param string $bold     Tekst do pogrubienia (escapowany tu).
	 * @return string Gotowy HTML — do wypisania przez `echo` bez dalszego escape'owania.
	 */
	public static function bold_text( string $template, string $bold ): string {
		return wp_kses_post( sprintf( $template, '<b>' . esc_html( $bold ) . '</b>' ) );
	}
}
