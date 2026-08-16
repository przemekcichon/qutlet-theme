<?php
/**
 * Slice HeaderMenu — wspólny odczyt pozycji menu kategorii (P-16.2b).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\HeaderMenu;

use Qutlet\Core\HeaderMenu\MegaMenuGroupTaxonomy;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Odczyt + grupowanie pozycji menu lokalizacji `kategorie` — jeden odczyt
 * `wp_get_nav_menu_items()` per request (memoizowany), dzielony między trzy
 * bloki dynamiczne (`qutlet/header-categories-band`, `qutlet/header-mega-grid`,
 * `qutlet/header-categories-mnav`) osadzone w `parts/header.html` (D-16.G4).
 */
final class CategoryMenu {

	/**
	 * Memoizowana lista pozycji menu (`null` = jeszcze nie czytano). Typ
	 * elementu celowo `object`, NIE `WP_Post` — `url`/`title` to właściwości
	 * dostawiane w locie przez `wp_setup_nav_menu_item()`, których stub
	 * `WP_Post` nie zna (ten sam powód, dla którego `qutlet/header-nav`,
	 * P-16.1, czyta przez nieotypowaną pętlę zamiast `Walker_Nav_Menu`).
	 *
	 * @var array<int, object>|null
	 */
	private static ?array $items = null;

	/**
	 * Czyta (i cache'uje na czas requestu) pozycje menu przypisanego do
	 * lokalizacji `kategorie` — rozwiązanie PRZEZ LOKALIZACJĘ
	 * (`get_nav_menu_locations()`), wzorem `Help::render_help_nav()` (P-1.5)
	 * i `qutlet/header-nav` (P-16.1). Brak menu przypisanego do lokalizacji
	 * (świeże środowisko przed seedem, D-16.G5) → `[]`, bloki nic nie
	 * renderują, nagłówek nie wywraca się bez menu.
	 *
	 * @return array<int, object>
	 */
	private static function items(): array {
		if ( null !== self::$items ) {
			return self::$items;
		}

		$locations = get_nav_menu_locations();

		if ( ! isset( $locations[ HeaderMenu::MENU_LOCATION_KATEGORIE ] ) ) {
			self::$items = array();

			return self::$items;
		}

		$items = wp_get_nav_menu_items( $locations[ HeaderMenu::MENU_LOCATION_KATEGORIE ] );

		self::$items = is_array( $items ) ? $items : array();

		return self::$items;
	}

	/**
	 * Odczyt pola ACF z fallbackiem na `get_post_meta()` — motyw nie ma
	 * twardej zależności na ACF Pro (tylko WooCommerce + qutlet-core,
	 * `functions.php::dependencies_met()`), wzorem `ProductPage::acf_field()`.
	 *
	 * @param string $key     Nazwa pola.
	 * @param int    $post_id Id pozycji menu (`nav_menu_item`).
	 * @return mixed
	 */
	private static function acf_field( string $key, int $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $key, $post_id );
		}

		return get_post_meta( $post_id, $key, true );
	}

	/**
	 * Pozycje z `widoczna_na_belce == true`, w natywnym porządku pozycji menu
	 * (drag-drop w Wygląd → Menu) — zasila `.subnav-band` (pigułki) i sekcję
	 * „Kategorie" w `.mnav-panel` (mobile, TEN SAM zestaw, kontrakt §14).
	 *
	 * @return array<int, object>
	 */
	public static function pills(): array {
		return array_values(
			array_filter(
				self::items(),
				static function ( $item ) {
					return (bool) self::acf_field( 'widoczna_na_belce', (int) $item->ID );
				}
			)
		);
	}

	/**
	 * WSZYSTKIE pozycje zgrupowane po `grupa_mega_menu` (relacja taxonomy,
	 * §14.2), kolumny posortowane po term-meta `kolejnosc` (§14.3), pozycje
	 * wewnątrz kolumny w natywnym porządku menu. Pozycja bez relacji (pole
	 * jest WYMAGANE w ACF — brak oznacza niekompletne dane) jest pomijana,
	 * nie wywraca renderu.
	 *
	 * @return array<int, array{term_id: int, label: string, order: int, items: array<int, object>}>
	 */
	public static function columns(): array {
		$columns = array();

		foreach ( self::items() as $item ) {
			$term_id = (int) self::acf_field( 'grupa_mega_menu', (int) $item->ID );

			if ( $term_id <= 0 ) {
				continue;
			}

			if ( ! isset( $columns[ $term_id ] ) ) {
				$term = get_term( $term_id, MegaMenuGroupTaxonomy::TAXONOMY );

				if ( ! ( $term instanceof WP_Term ) ) {
					continue;
				}

				$columns[ $term_id ] = array(
					'term_id' => $term_id,
					'label'   => $term->name,
					'order'   => (int) get_term_meta( $term_id, 'kolejnosc', true ),
					'items'   => array(),
				);
			}

			$columns[ $term_id ]['items'][] = $item;
		}

		usort(
			$columns,
			static function ( array $a, array $b ): int {
				return $a['order'] <=> $b['order'];
			}
		);

		return $columns;
	}
}
