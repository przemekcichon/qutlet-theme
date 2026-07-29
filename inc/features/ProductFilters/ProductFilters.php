<?php
/**
 * Slice ProductFilters — render toolbara + szuflady `.drawer` (filtry marka /
 * klasa stanu / cena + sortowanie) na archiwum produktów, port z
 * design/vanilla/strefa-okazji.html (P-8.3b).
 *
 * Granica core↔theme (D-8.3b.2, decyzja użytkownika po niezależnej recenzji
 * P-8.3b): modyfikacja GŁÓWNEGO zapytania (meta_query klasy stanu, własny
 * `posts_clauses` dla sortowania „Największy rabat") oraz SQL liczące
 * facety/granice ceny żyją w `qutlet-core`
 * (`Qutlet\Core\ProductFilters\ProductFilterQuery`, ta sama nazwa slice'a —
 * „glue do Woo" w rozumieniu CLAUDE.md). TA klasa jest CZYSTYM renderem:
 * woła gotowe dane z core i buduje z nich formularz
 * (`woocommerce/loop/filters-and-sort.php`) — nie zna SQL-a ani struktury
 * zapytania.
 *
 * Mechanizm [D-8.3b.1]: klasyczny GET + WP_Query (przeładowanie strony), NIE
 * JS/AJAX/REST — patrz nagłówek `ProductFilterQuery` w core po pełne
 * uzasadnienie.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\ProductFilters;

use Qutlet\Core\ProductFilters\ProductFilterQuery;
use Qutlet\Theme\features\ProductPage\ProductPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render toolbara + szuflady filtrów archiwum produktów.
 */
final class ProductFilters {

	/**
	 * Podpina render + enqueue. Modyfikacja zapytania (meta_query/posts_clauses)
	 * jest wpięta niezależnie w `qutlet-core` (`ProductFilterQuery::init()`),
	 * nie stąd — patrz nagłówek pliku, D-8.3b.2.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'woocommerce_before_shop_loop', array( self::class, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
	}

	/**
	 * Ładuje JS szuflady filtrów WYŁĄCZNIE na obsługiwanych archiwach.
	 *
	 * @return void
	 */
	public static function enqueue(): void {
		if ( ! self::is_supported_archive() ) {
			return;
		}

		wp_enqueue_script(
			'qutlet-theme-product-filters',
			get_theme_file_uri( 'assets/js/product-filters.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);
	}

	/**
	 * Czy bieżące żądanie to archiwum, na którym renderujemy filtry. P-8.3a
	 * zbudowało realny szablon WYŁĄCZNIE dla `product_cat`
	 * (`templates/taxonomy-product_cat.html`) — w motywie NIE MA jeszcze
	 * `archive-product.html`, więc `is_shop()` świadomie pominięte (dodać
	 * dopiero, gdy powstanie realny szablon strony `/sklep/` — recenzja
	 * P-8.3b oznaczyła wcześniejsze objęcie `is_shop()` jako niezweryfikowane
	 * w ground-truth tego punktu).
	 *
	 * @return bool
	 */
	private static function is_supported_archive(): bool {
		return function_exists( 'is_product_category' ) && is_product_category();
	}

	/**
	 * Adres bazowy formularza filtrów — bieżące archiwum BEZ segmentu
	 * `/page/N/` (permalinki ładne paginują ścieżką, nie query stringiem;
	 * zmiana filtra ma zawsze wracać na stronę 1 wyników).
	 *
	 * @return string
	 */
	public static function archive_base_url(): string {
		if ( is_product_category() ) {
			$term = get_queried_object();

			if ( $term instanceof \WP_Term ) {
				$link = get_term_link( $term );

				if ( ! is_wp_error( $link ) ) {
					return $link;
				}
			}
		}

		if ( function_exists( 'wc_get_page_permalink' ) ) {
			return wc_get_page_permalink( 'shop' );
		}

		return home_url( '/' );
	}

	/**
	 * Cena jako czysty tekst (bez znaczników `wc_price()`).
	 *
	 * @param float $amount Kwota.
	 * @return string
	 */
	private static function format_price( float $amount ): string {
		if ( function_exists( 'wc_price' ) ) {
			return wp_strip_all_tags( wc_price( $amount ) );
		}

		return number_format( $amount, 2 ) . ' zł';
	}

	/**
	 * Adres z bieżącego stanu filtrów (GET), z nadpisaniami/usunięciami
	 * (`false`/`null`/pusta tablica w `$overrides` = usuń parametr) — do
	 * linków „usuń" na chipach aktywnych filtrów. Nazwy parametrów bierze z
	 * publicznych stałych `ProductFilterQuery` (core) — jedno źródło prawdy.
	 *
	 * @param string               $base      Adres bazowy (`archive_base_url()`).
	 * @param array<string, mixed> $overrides Nadpisania/usunięcia parametrów.
	 * @return string
	 */
	private static function build_url( string $base, array $overrides ): string {
		$args = array();

		foreach ( array( ProductFilterQuery::BRAND_PARAM, ProductFilterQuery::CONDITION_PARAM, 'min_price', 'max_price', 'orderby' ) as $key ) {
			if ( isset( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- odczyt stanu filtrów do wyświetlenia, nie mutuje stanu.
				$args[ $key ] = wp_unslash( $_GET[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}

		foreach ( $overrides as $key => $value ) {
			if ( false === $value || null === $value || array() === $value ) {
				unset( $args[ $key ] );
				continue;
			}

			$args[ $key ] = $value;
		}

		return $args ? add_query_arg( $args, $base ) : $base;
	}

	/**
	 * Chipy aktywnych filtrów (kontrakt UI — `strefa-okazji.html` `.chips-row`)
	 * z linkiem „usuń" (usuwa WYŁĄCZNIE tę jedną wartość, zachowuje resztę).
	 *
	 * @param array<int, array{slug: string, name: string, count: int, checked: bool}> $brand_facets     Z `ProductFilterQuery::brand_facets()`.
	 * @param array<int, array{code: string, count: int, checked: bool}>               $condition_facets Z `ProductFilterQuery::condition_facets()`.
	 * @param array{floor: float, ceil: float}                                         $price_bounds     Z `ProductFilterQuery::price_bounds()`.
	 * @param array{min: float, max: float}                                            $price_range      Z `ProductFilterQuery::selected_price_range()`.
	 * @return array<int, array{label: string, url: string}>
	 */
	public static function active_chips( array $brand_facets, array $condition_facets, array $price_bounds, array $price_range ): array {
		$chips = array();
		$base  = self::archive_base_url();

		foreach ( $brand_facets as $facet ) {
			if ( ! $facet['checked'] ) {
				continue;
			}

			$remaining = array_values( array_diff( ProductFilterQuery::selected_brand_slugs(), array( $facet['slug'] ) ) );
			$chips[]   = array(
				'label' => $facet['name'],
				'url'   => self::build_url( $base, array( ProductFilterQuery::BRAND_PARAM => $remaining ) ),
			);
		}

		foreach ( $condition_facets as $facet ) {
			if ( ! $facet['checked'] ) {
				continue;
			}

			$remaining = array_values( array_diff( ProductFilterQuery::selected_conditions(), array( $facet['code'] ) ) );
			$chips[]   = array(
				/* translators: %s: kod klasy stanu (A-D). */
				'label' => sprintf( __( 'Klasa %s', 'qutlet-theme' ), $facet['code'] ),
				'url'   => self::build_url( $base, array( ProductFilterQuery::CONDITION_PARAM => $remaining ) ),
			);
		}

		if ( $price_range['min'] > $price_bounds['floor'] ) {
			$chips[] = array(
				/* translators: %s: sformatowana cena. */
				'label' => sprintf( __( 'od %s', 'qutlet-theme' ), self::format_price( $price_range['min'] ) ),
				'url'   => self::build_url( $base, array( 'min_price' => false ) ),
			);
		}

		if ( $price_range['max'] < $price_bounds['ceil'] ) {
			$chips[] = array(
				/* translators: %s: sformatowana cena. */
				'label' => sprintf( __( 'do %s', 'qutlet-theme' ), self::format_price( $price_range['max'] ) ),
				'url'   => self::build_url( $base, array( 'max_price' => false ) ),
			);
		}

		return $chips;
	}

	/**
	 * Liczba aktywnych filtrów (kontrakt UI — `.filter-btn-count`).
	 *
	 * @param array<int, array{checked: bool}> $brand_facets     Z `ProductFilterQuery::brand_facets()`.
	 * @param array<int, array{checked: bool}> $condition_facets Z `ProductFilterQuery::condition_facets()`.
	 * @param array{floor: float, ceil: float} $price_bounds     Z `ProductFilterQuery::price_bounds()`.
	 * @param array{min: float, max: float}    $price_range      Z `ProductFilterQuery::selected_price_range()`.
	 * @return int
	 */
	public static function active_filter_count( array $brand_facets, array $condition_facets, array $price_bounds, array $price_range ): int {
		$count = 0;

		foreach ( $brand_facets as $facet ) {
			if ( $facet['checked'] ) {
				++$count;
			}
		}

		foreach ( $condition_facets as $facet ) {
			if ( $facet['checked'] ) {
				++$count;
			}
		}

		if ( $price_range['min'] > $price_bounds['floor'] ) {
			++$count;
		}

		if ( $price_range['max'] < $price_bounds['ceil'] ) {
			++$count;
		}

		return $count;
	}

	/**
	 * Renderuje toolbar (filtr/licznik/sortowanie) + chipy + szufladę filtrów
	 * (`woocommerce/loop/filters-and-sort.php`) na obsługiwanych archiwach.
	 * Dane facetów/granic ceny/stanu bierze z `qutlet-core`
	 * (`ProductFilterQuery`) — jeśli core jest nieaktywny (D-G5, motyw nie
	 * fataluje), po prostu nic nie renderuje.
	 *
	 * @return void
	 */
	public static function render(): void {
		if ( ! self::is_supported_archive() || ! function_exists( 'wc_get_template' ) ) {
			return;
		}

		if ( ! class_exists( ProductFilterQuery::class ) ) {
			return;
		}

		$bounds     = ProductFilterQuery::price_bounds();
		$range      = ProductFilterQuery::selected_price_range( $bounds );
		$brands     = ProductFilterQuery::brand_facets();
		$conditions = ProductFilterQuery::condition_facets();

		wc_get_template(
			'loop/filters-and-sort.php',
			array(
				'form_action'      => self::archive_base_url(),
				'current_sort'     => ProductFilterQuery::current_sort(),
				'brand_facets'     => $brands,
				'condition_facets' => $conditions,
				'condition_labels' => array_combine(
					ProductFilterQuery::CONDITION_CODES,
					array_map( array( ProductPage::class, 'condition_label' ), ProductFilterQuery::CONDITION_CODES )
				),
				'price_bounds'     => $bounds,
				'price_range'      => $range,
				'active_chips'     => self::active_chips( $brands, $conditions, $bounds, $range ),
				'filter_count'     => self::active_filter_count( $brands, $conditions, $bounds, $range ),
				'result_count'     => isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->found_posts : 0,
			)
		);
	}
}
