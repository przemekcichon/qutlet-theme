<?php
/**
 * Slice ProductFilters — filtry (marka / klasa stanu / cena) + sortowanie na
 * archiwum produktów (port toolbara + szuflady `.drawer` z
 * design/vanilla/strefa-okazji.html, P-8.3b).
 *
 * Mechanizm [D-8.3b.1, decyzja sesji P-8.3b]: klasyczny GET + WP_Query
 * (przeładowanie strony), NIE JS/AJAX/REST. Marka (taksonomia natywna
 * `product_brand`) i cena (`min_price`/`max_price`) filtrują się SAME przez
 * mechanizmy WordPressa/WooCommerce — zero własnego zapytania:
 * - dowolna publiczna taksonomia z `query_var` (product_brand ma domyślny
 *   `query_var = 'product_brand'`, `WC_Brands::init_taxonomy()`) dostaje
 *   automatyczny tax_query z GET, zweryfikowane w
 *   `wp-includes/class-wp-query.php::parse_tax_query()`;
 * - `min_price`/`max_price` obsługuje bezwarunkowo `WC_Query::price_filter_post_clauses()`
 *   (hook na `posts_clauses` głównego zapytania, `class-wc-query.php`).
 * Klasa stanu (pole ACF, brak odpowiednika natywnego) i sortowanie
 * „Największy rabat" (wartość LICZONA — kontrakt §6, nie pole) wymagają
 * własnego hooka — dokładnie tym samym wzorcem, jakim samo Woo dokłada
 * sortowanie po cenie/popularności/ocenie (`WC_Query::order_by_*_post_clauses()`).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\ProductFilters;

use Qutlet\Theme\features\ProductPage\ProductPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filtry (marka/klasa stanu/cena) + sortowanie archiwum produktów.
 */
final class ProductFilters {

	/** Literały klasy stanu (kontrakt §2, pole ACF `klasa_stanu` — A/B/C/D). */
	private const CONDITION_CODES = array( 'A', 'B', 'C', 'D' );

	/** Nazwa GET param = literał pola ACF klasy stanu (kontrakt §2, VERBATIM). */
	private const CONDITION_PARAM = 'klasa_stanu';

	/** ACF pole ceny rynkowej nowego (kontrakt §2, VERBATIM) — baza rabatu. */
	private const MARKET_PRICE_META_KEY = 'cena_rynkowa_nowego';

	/** GET param taksonomii marki (kontrakt §3, `product_brand` — natywny query_var Woo). */
	private const BRAND_PARAM = 'product_brand';

	/** Wartość `orderby` dla sortowania „Największy rabat" (custom — brak odpowiednika w Woo). */
	private const DISCOUNT_ORDERBY = 'save';

	/** Dozwolone wartości `orderby` (natywne Woo `price`/`price-desc` + nasz `save`; '' = domyślne). */
	private const SORT_VALUES = array( '', 'price', 'price-desc', self::DISCOUNT_ORDERBY );

	/**
	 * Podpina hooki zapytania i renderu.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'woocommerce_product_query', array( self::class, 'apply_condition_filter' ) );
		add_action( 'woocommerce_product_query', array( self::class, 'apply_discount_sort' ) );
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
	 * Czy bieżące żądanie to archiwum, na którym renderujemy filtry (P-8.3a:
	 * tylko sklep + kategoria mają realny szablon w tym motywie).
	 *
	 * @return bool
	 */
	private static function is_supported_archive(): bool {
		return function_exists( 'is_shop' ) && ( is_shop() || is_product_category() );
	}

	/* ---------------------------------------------------------------------
	 * Modyfikacje głównego zapytania: klasa stanu (meta_query) + sortowanie
	 * „Największy rabat" (posts_clauses) — jedyne dwa elementy bez natywnego
	 * wsparcia Woo.
	 * ------------------------------------------------------------------ */

	/**
	 * Zaznaczone kody klasy stanu z GET (`?klasa_stanu[]=A&klasa_stanu[]=B`),
	 * zwalidowane przeciw literałom kontraktu.
	 *
	 * @return array<int, string>
	 */
	public static function selected_conditions(): array {
		if ( ! isset( $_GET[ self::CONDITION_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtr do wyświetlenia (GET), nie mutuje stanu.
			return array();
		}

		$raw = array_map( 'sanitize_text_field', (array) wp_unslash( $_GET[ self::CONDITION_PARAM ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return array_values( array_intersect( self::CONDITION_CODES, array_map( 'strtoupper', $raw ) ) );
	}

	/**
	 * Dokłada `meta_query` dla klasy stanu do głównego zapytania archiwum,
	 * gdy GET niesie choć jeden zaznaczony kod. Wiersz oznaczony
	 * `qutlet_condition_filter`, żeby liczniki facetu klasy stanu mogły go
	 * wykluczyć przy liczeniu WŁASNYCH liczników (patrz `condition_facets()`).
	 *
	 * @param \WP_Query $q Główne zapytanie archiwum (hook `woocommerce_product_query`).
	 * @return void
	 */
	public static function apply_condition_filter( \WP_Query $q ): void {
		$codes = self::selected_conditions();

		if ( ! $codes ) {
			return;
		}

		$meta_query   = (array) $q->get( 'meta_query' );
		$meta_query[] = array(
			'key'                     => self::CONDITION_PARAM,
			'value'                   => $codes,
			'compare'                 => 'IN',
			'qutlet_condition_filter' => true,
		);
		$q->set( 'meta_query', $meta_query );
	}

	/**
	 * Bieżąca wartość sortowania z GET, zwalidowana przeciw dozwolonej liście
	 * (natywne klucze Woo `price`/`price-desc` + nasz `save`).
	 *
	 * @return string
	 */
	public static function current_sort(): string {
		if ( ! isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return '';
		}

		$raw = sanitize_text_field( wp_unslash( $_GET['orderby'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return in_array( $raw, self::SORT_VALUES, true ) ? $raw : '';
	}

	/**
	 * Gdy wybrane sortowanie to „Największy rabat" (`save`), neutralizuje
	 * `orderby` ustawione przez `WC_Query::get_catalog_ordering_args()`
	 * (zostałoby dosłownie `ORDER BY save` — nierozpoznana kolumna) i dokłada
	 * własny `posts_clauses` liczący rabat, tym samym wzorcem co natywne
	 * `order_by_price_*_post_clauses()` w `WC_Query`.
	 *
	 * @param \WP_Query $q Główne zapytanie archiwum (hook `woocommerce_product_query`).
	 * @return void
	 */
	public static function apply_discount_sort( \WP_Query $q ): void {
		if ( self::DISCOUNT_ORDERBY !== self::current_sort() ) {
			return;
		}

		$q->set( 'orderby', 'ID' );
		$q->set( 'order', 'DESC' );
		add_filter( 'posts_clauses', array( self::class, 'discount_order_clauses' ) );
	}

	/**
	 * `posts_clauses`: sortuje malejąco po procencie rabatu
	 * `1 − cena_sprzedaży / cena_rynkowa_nowego` (kontrakt §6, `QT.savePct`).
	 * Produkty bez `cena_rynkowa_nowego` (brak odniesienia — rabat
	 * niepoliczalny) trafiają na koniec listy.
	 *
	 * @param array<string, string> $args Klauzule zapytania (join/where/orderby/...).
	 * @return array<string, string>
	 */
	public static function discount_order_clauses( array $args ): array {
		global $wpdb;

		$args['join'] .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} qutlet_price_lookup ON {$wpdb->posts}.ID = qutlet_price_lookup.product_id";
		$args['join'] .= " LEFT JOIN {$wpdb->postmeta} qutlet_market_price ON {$wpdb->posts}.ID = qutlet_market_price.post_id AND qutlet_market_price.meta_key = '" . esc_sql( self::MARKET_PRICE_META_KEY ) . "'";

		$args['orderby'] = '
			( CASE
				WHEN qutlet_market_price.meta_value + 0 > 0
				THEN ( 1 - qutlet_price_lookup.min_price / ( qutlet_market_price.meta_value + 0 ) )
				ELSE -1
			END ) DESC,
			' . $wpdb->posts . '.ID DESC
		';

		return $args;
	}

	/* ---------------------------------------------------------------------
	 * Odczyt stanu głównego zapytania (do liczników facetów + granic ceny) —
	 * wzorzec `WC_Widget_Price_Filter::get_filtered_price()`.
	 * ------------------------------------------------------------------ */

	/**
	 * `tax_query`/`meta_query` głównego zapytania archiwum — zawiera już
	 * automatyczny tax_query kategorii/marki (natywny mechanizm WP_Query) i
	 * nasz `meta_query` klasy stanu, jeśli aktywny.
	 *
	 * @return array{tax_query: array<int, mixed>, meta_query: array<int, mixed>}
	 */
	private static function main_query_parts(): array {
		$main = null;

		if ( function_exists( 'WC' ) ) {
			$main = WC()->query->get_main_query();
		}

		if ( ! $main instanceof \WP_Query ) {
			$main = $GLOBALS['wp_query'];
		}

		$vars = $main->query_vars;

		return array(
			'tax_query'  => isset( $vars['tax_query'] ) && is_array( $vars['tax_query'] ) ? $vars['tax_query'] : array(),
			'meta_query' => isset( $vars['meta_query'] ) && is_array( $vars['meta_query'] ) ? $vars['meta_query'] : array(),
		);
	}

	/**
	 * Buduje fragmenty SQL (JOIN/WHERE) z tax_query/meta_query — dokładnie
	 * ten sam wzorzec, którym `WC_Widget_Price_Filter::get_filtered_price()`
	 * liczy granice ceny w bieżącym kontekście zapytania.
	 *
	 * @param array<int, mixed> $tax_query  Wiersze tax_query.
	 * @param array<int, mixed> $meta_query Wiersze meta_query.
	 * @return array{join: string, where: string}
	 */
	private static function filtered_sql( array $tax_query, array $meta_query ): array {
		global $wpdb;

		$tax_sql  = ( new \WP_Tax_Query( $tax_query ) )->get_sql( $wpdb->posts, 'ID' );
		$meta_sql = ( new \WP_Meta_Query( $meta_query ) )->get_sql( 'post', $wpdb->posts, 'ID' );

		return array(
			'join'  => $tax_sql['join'] . $meta_sql['join'],
			'where' => $tax_sql['where'] . $meta_sql['where'],
		);
	}

	/**
	 * Granice ceny (min/max) w bieżącym kontekście archiwum (kategoria +
	 * aktywne facety marki/klasy stanu — WSZYSTKIE, bez wykluczania, tak jak
	 * natywny widget ceny Woo wyklucza wyłącznie własny filtr ceny/oceny,
	 * których tu nie ma).
	 *
	 * @return array{floor: float, ceil: float}
	 */
	public static function price_bounds(): array {
		global $wpdb;

		$parts = self::main_query_parts();
		$sql   = self::filtered_sql( $parts['tax_query'], $parts['meta_query'] );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- granice ceny do renderu suwaka, liczone z tax/meta query WP_Query (bezpieczne, jak WC_Widget_Price_Filter::get_filtered_price()).
		$row = $wpdb->get_row(
			"SELECT MIN(min_price) AS floor_price, MAX(max_price) AS ceil_price
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN (
				SELECT ID FROM {$wpdb->posts}
				{$sql['join']}
				WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish'
				{$sql['where']}
			)"
		);

		return array(
			'floor' => $row && null !== $row->floor_price ? (float) $row->floor_price : 0.0,
			'ceil'  => $row && null !== $row->ceil_price ? (float) $row->ceil_price : 0.0,
		);
	}

	/**
	 * Zaznaczony zakres ceny z GET (`min_price`/`max_price` — te same nazwy,
	 * co natywny filtr ceny Woo), sklampowany do granic bieżącego kontekstu.
	 *
	 * @param array{floor: float, ceil: float} $bounds Granice z `price_bounds()`.
	 * @return array{min: float, max: float}
	 */
	public static function selected_price_range( array $bounds ): array {
		$min = isset( $_GET['min_price'] ) ? (float) wp_unslash( $_GET['min_price'] ) : $bounds['floor']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$max = isset( $_GET['max_price'] ) ? (float) wp_unslash( $_GET['max_price'] ) : $bounds['ceil']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$min = max( $bounds['floor'], min( $min, $bounds['ceil'] ) );
		$max = min( $bounds['ceil'], max( $max, $bounds['floor'] ) );

		if ( $min > $max ) {
			$swap = $min;
			$min  = $max;
			$max  = $swap;
		}

		return array(
			'min' => $min,
			'max' => $max,
		);
	}

	/**
	 * Zaznaczone sluggi marki z GET (`?product_brand[]=sony`).
	 *
	 * @return array<int, string>
	 */
	public static function selected_brand_slugs(): array {
		if ( ! isset( $_GET[ self::BRAND_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return array();
		}

		$raw = (array) wp_unslash( $_GET[ self::BRAND_PARAM ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return array_values( array_filter( array_map( 'sanitize_title', $raw ) ) );
	}

	/**
	 * Facety marki (kontrakt §3, `product_brand`) z licznikami w bieżącym
	 * kontekście archiwum, WYKLUCZAJĄC własny filtr marki (żeby zaznaczenie
	 * jednej marki nie zerowało liczników pozostałych — cross-filtering,
	 * jak natywna warstwowa nawigacja Woo).
	 *
	 * @return array<int, array{slug: string, name: string, count: int, checked: bool}>
	 */
	public static function brand_facets(): array {
		global $wpdb;

		if ( ! taxonomy_exists( 'product_brand' ) ) {
			return array();
		}

		$parts     = self::main_query_parts();
		$tax_query = array_values(
			array_filter(
				$parts['tax_query'],
				static function ( $row ) {
					return ! ( is_array( $row ) && isset( $row['taxonomy'] ) && self::BRAND_PARAM === $row['taxonomy'] );
				}
			)
		);
		$sql = self::filtered_sql( $tax_query, $parts['meta_query'] );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- liczniki facetu do renderu szuflady, liczone z tax/meta query WP_Query.
		$rows = $wpdb->get_results(
			"SELECT tt.term_id AS term_id, COUNT(DISTINCT {$wpdb->posts}.ID) AS product_count
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->term_relationships} qutlet_brand_tr ON {$wpdb->posts}.ID = qutlet_brand_tr.object_id
			INNER JOIN {$wpdb->term_taxonomy} tt ON qutlet_brand_tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = '" . esc_sql( self::BRAND_PARAM ) . "'
			{$sql['join']}
			WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish'
			{$sql['where']}
			GROUP BY tt.term_id"
		);

		$counts = array();

		foreach ( (array) $rows as $row ) {
			$counts[ (int) $row->term_id ] = (int) $row->product_count;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => self::BRAND_PARAM,
				'hide_empty' => true,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		$selected = self::selected_brand_slugs();
		$facets   = array();

		foreach ( $terms as $term ) {
			$count   = $counts[ $term->term_id ] ?? 0;
			$checked = in_array( $term->slug, $selected, true );

			if ( 0 === $count && ! $checked ) {
				continue;
			}

			$facets[] = array(
				'slug'    => $term->slug,
				'name'    => $term->name,
				'count'   => $count,
				'checked' => $checked,
			);
		}

		return $facets;
	}

	/**
	 * Facety klasy stanu (kontrakt §2, `klasa_stanu`) z licznikami w bieżącym
	 * kontekście archiwum, WYKLUCZAJĄC własny filtr klasy stanu (patrz
	 * `apply_condition_filter()` — wiersz oznaczony `qutlet_condition_filter`).
	 *
	 * @return array<int, array{code: string, label: string, count: int, checked: bool}>
	 */
	public static function condition_facets(): array {
		global $wpdb;

		$parts      = self::main_query_parts();
		$meta_query = array_values(
			array_filter(
				$parts['meta_query'],
				static function ( $row ) {
					return ! ( is_array( $row ) && ! empty( $row['qutlet_condition_filter'] ) );
				}
			)
		);
		$sql = self::filtered_sql( $parts['tax_query'], $meta_query );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- liczniki facetu do renderu szuflady, liczone z tax/meta query WP_Query.
		$rows = $wpdb->get_results(
			"SELECT qutlet_condition.meta_value AS code, COUNT(DISTINCT {$wpdb->posts}.ID) AS product_count
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} qutlet_condition ON {$wpdb->posts}.ID = qutlet_condition.post_id AND qutlet_condition.meta_key = '" . esc_sql( self::CONDITION_PARAM ) . "'
			{$sql['join']}
			WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish'
			{$sql['where']}
			GROUP BY qutlet_condition.meta_value"
		);

		$counts = array();

		foreach ( (array) $rows as $row ) {
			$counts[ strtoupper( (string) $row->code ) ] = (int) $row->product_count;
		}

		$selected = self::selected_conditions();
		$facets   = array();

		foreach ( self::CONDITION_CODES as $code ) {
			$count   = $counts[ $code ] ?? 0;
			$checked = in_array( $code, $selected, true );

			if ( 0 === $count && ! $checked ) {
				continue;
			}

			$facets[] = array(
				'code'    => $code,
				'label'   => ProductPage::condition_label( $code ),
				'count'   => $count,
				'checked' => $checked,
			);
		}

		return $facets;
	}

	/* ---------------------------------------------------------------------
	 * Render (toolbar + chips + szuflada) — `woocommerce/loop/filters-and-sort.php`.
	 * ------------------------------------------------------------------ */

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
	 * linków „usuń" na chipach aktywnych filtrów.
	 *
	 * @param string                    $base      Adres bazowy (`archive_base_url()`).
	 * @param array<string, mixed>      $overrides Nadpisania/usunięcia parametrów.
	 * @return string
	 */
	private static function build_url( string $base, array $overrides ): string {
		$args = array();

		foreach ( array( self::BRAND_PARAM, self::CONDITION_PARAM, 'min_price', 'max_price', 'orderby' ) as $key ) {
			if ( isset( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
	 * @param array<int, array{slug: string, name: string, count: int, checked: bool}> $brand_facets     Z `brand_facets()`.
	 * @param array<int, array{code: string, label: string, count: int, checked: bool}> $condition_facets Z `condition_facets()`.
	 * @param array{floor: float, ceil: float}                                          $price_bounds     Z `price_bounds()`.
	 * @param array{min: float, max: float}                                             $price_range      Z `selected_price_range()`.
	 * @return array<int, array{label: string, url: string}>
	 */
	public static function active_chips( array $brand_facets, array $condition_facets, array $price_bounds, array $price_range ): array {
		$chips = array();
		$base  = self::archive_base_url();

		foreach ( $brand_facets as $facet ) {
			if ( ! $facet['checked'] ) {
				continue;
			}

			$remaining = array_values( array_diff( self::selected_brand_slugs(), array( $facet['slug'] ) ) );
			$chips[]   = array(
				'label' => $facet['name'],
				'url'   => self::build_url( $base, array( self::BRAND_PARAM => $remaining ) ),
			);
		}

		foreach ( $condition_facets as $facet ) {
			if ( ! $facet['checked'] ) {
				continue;
			}

			$remaining = array_values( array_diff( self::selected_conditions(), array( $facet['code'] ) ) );
			$chips[]   = array(
				/* translators: %s: kod klasy stanu (A-D). */
				'label' => sprintf( __( 'Klasa %s', 'qutlet-theme' ), $facet['code'] ),
				'url'   => self::build_url( $base, array( self::CONDITION_PARAM => $remaining ) ),
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
	 * @param array<int, array{checked: bool}> $brand_facets     Z `brand_facets()`.
	 * @param array<int, array{checked: bool}> $condition_facets Z `condition_facets()`.
	 * @param array{floor: float, ceil: float} $price_bounds     Z `price_bounds()`.
	 * @param array{min: float, max: float}    $price_range      Z `selected_price_range()`.
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
	 *
	 * @return void
	 */
	public static function render(): void {
		if ( ! self::is_supported_archive() || ! function_exists( 'wc_get_template' ) ) {
			return;
		}

		$bounds     = self::price_bounds();
		$range      = self::selected_price_range( $bounds );
		$brands     = self::brand_facets();
		$conditions = self::condition_facets();

		wc_get_template(
			'loop/filters-and-sort.php',
			array(
				'form_action'      => self::archive_base_url(),
				'current_sort'     => self::current_sort(),
				'brand_facets'     => $brands,
				'condition_facets' => $conditions,
				'price_bounds'     => $bounds,
				'price_range'      => $range,
				'active_chips'     => self::active_chips( $brands, $conditions, $bounds, $range ),
				'filter_count'     => self::active_filter_count( $brands, $conditions, $bounds, $range ),
				'result_count'     => isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->found_posts : 0,
			)
		);
	}
}
