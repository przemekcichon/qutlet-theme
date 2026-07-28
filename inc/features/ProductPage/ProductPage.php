<?php
/**
 * Slice ProductPage — render strony produktu (port design/vanilla/produkt.html).
 *
 * P-8.2a: szkielet + galeria + nagłówek. P-8.2b: przełącznik kanału zakupu
 * (taby Qutlet/Allegro + buybar + sekcja „jak-to-dziala"), pełna semantyka
 * D-8.G1. P-8.2c: sekcja treści/specyfikacji — `ship_items()` (repeater
 * `zawartosc_zestawu_pozycje`, P-9.2) i `specification_rows()` (atrybuty WC
 * + wiersz „Klasa stanu", kontrakt §9.2). Markup mieszka w woocommerce/content-single-product.php
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

		wp_enqueue_script(
			'qutlet-theme-product-content-tabs',
			get_theme_file_uri( 'assets/js/product-content-tabs.js' ),
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
	 * Pozycje zestawu z repeatera ACF `zawartosc_zestawu_pozycje` (kontrakt §2,
	 * D-9.2.1 — P-9.2). Wiersze bez `etykieta` są pomijane (repeater bez ACF Pro
	 * aktywnego zwróciłby surową liczbę wierszy z `get_post_meta()`, nie tablicę —
	 * stąd twardy wymóg `get_field()`, bez fallbacku jak w `acf_field()`).
	 *
	 * @param int $post_id Id produktu.
	 * @return array<int, array{image_id: int, label: string, included: bool}>
	 */
	public static function ship_items( int $post_id ): array {
		if ( ! function_exists( 'get_field' ) ) {
			return array();
		}

		$rows = get_field( 'zawartosc_zestawu_pozycje', $post_id );

		if ( ! is_array( $rows ) ) {
			return array();
		}

		$items = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$label = trim( (string) ( $row['etykieta'] ?? '' ) );

			if ( '' === $label ) {
				continue;
			}

			$items[] = array(
				'image_id' => (int) ( $row['zdjecie'] ?? 0 ),
				'label'    => $label,
				'included' => (bool) ( $row['w_zestawie'] ?? false ),
			);
		}

		return $items;
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
	 * Procent, o jaki `$reference_price` jest WYŻSZA od `$sale_price` —
	 * relatywnie do `$reference_price` (nie do `$sale_price`), czyli
	 * `round((1 − sale/reference) * 100)`. Dwa zastosowania w tym szablonie
	 * (ta sama formuła, inny punkt odniesienia):
	 * 1) rabat „-X%" — `$reference_price` = `cena_rynkowa_nowego` (ACF),
	 *    kontrakt §6, `data.js` QT.savePct. NIE przechowywane.
	 * 2) nota „Cena wyższa o ~X%" w panelu Allegro — `$reference_price` =
	 *    `cena_allegro` (ACF). Kontrakt §4/§6 nie precyzuje kierunku formuły;
	 *    kierunek „relatywnie do ceny wyższej" dobrany tak, by odtworzyć
	 *    dokładnie przykład z prototypu (`cena_allegro` 199,00 zł vs cena
	 *    sprzedaży 179,10 zł → ~10%, jak w `produkt.html:109`) — licząc
	 *    względem ceny sprzedaży (`(cena_allegro−sale)/sale`) wyszłoby ~11%.
	 *
	 * @param float $sale_price      Cena niższa (Woo `_price`).
	 * @param float $reference_price Cena wyższa, punkt odniesienia procentu
	 *                               (`cena_rynkowa_nowego` LUB `cena_allegro`).
	 * @return int Zaokrąglony procent; 0, gdy `$reference_price` nie jest dodatnia.
	 */
	public static function save_percent( float $sale_price, float $reference_price ): int {
		if ( $reference_price <= 0.0 ) {
			return 0;
		}

		return (int) round( ( 1 - $sale_price / $reference_price ) * 100 );
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

	/**
	 * Wiersze specyfikacji (etykieta→wartość) z NATYWNYCH atrybutów WooCommerce
	 * (kontrakt §9.2 — `$product->get_attributes()`; D-5.1.1: core NIE rejestruje
	 * dla specyfikacji własnego pola, to natywny mechanizm Woo). Dokłada na
	 * końcu jeden wiersz „Klasa stanu" (kontrakt §9.2 / `produkt.html:190` —
	 * NIE osobna tabela, jeden `spec-row` tutaj; pełna tabela klasyfikacji A/B/C/D
	 * żyje w akordeonie „Klasyfikacja produktów", zaimplementowanym w P-8.2b).
	 *
	 * @param \WC_Product $product         Produkt.
	 * @param string      $condition_code  Literał klasy stanu (`A`-`D`) lub pusty string.
	 * @param string      $condition_label Etykieta klasy stanu (`condition_label()`) lub pusty string.
	 * @return array<int, array{label: string, value: string}>
	 */
	public static function specification_rows( \WC_Product $product, string $condition_code, string $condition_label ): array {
		$rows = array();

		foreach ( $product->get_attributes() as $attribute ) {
			if ( ! $attribute instanceof \WC_Product_Attribute ) {
				continue;
			}

			if ( $attribute->is_taxonomy() ) {
				$terms = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
				$value = implode( ', ', $terms );
			} else {
				$value = implode( ', ', $attribute->get_options() );
			}

			$value = trim( $value );

			if ( '' === $value ) {
				continue;
			}

			$rows[] = array(
				'label' => wc_attribute_label( $attribute->get_name(), $product ),
				'value' => $value,
			);
		}

		if ( '' !== $condition_code && '' !== $condition_label ) {
			$rows[] = array(
				'label' => __( 'Klasa stanu', 'qutlet-theme' ),
				/* translators: 1: condition code (A-D), 2: condition label. */
				'value' => sprintf( __( '%1$s — %2$s', 'qutlet-theme' ), $condition_code, $condition_label ),
			);
		}

		return $rows;
	}
}
