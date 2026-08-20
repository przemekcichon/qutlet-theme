<?php
/**
 * Blok `qutlet/footer-nav` — pełna kolumna `.footer-col` stopki (P-23.1,
 * rozszerzone P-25.2b/D-25.2.1). Parametryzowany atrybutem `menuLocation`
 * (jedna z 3 lokalizacji `FooterMenu::MENU_LOCATION_*`), osadzany 3× w
 * `parts/footer.html`, po jednym na kolumnę. Renderuje WŁASNY wrapper
 * `<div class="footer-col">` razem z nagłówkiem `<h5>` (wyprowadzonym niżej z
 * mapy `menuLocation → nagłówek` — TE SAME 3 literały co
 * `docs/kontrakt-danych.md` §14.4 — bez osobnego atrybutu bloku, żeby nie
 * dublować tego samego faktu w dwóch miejscach) ORAZ płaską listą linków
 * `<a>`. Nagłówek dawniej żył jako statyczny markup PRZED tym blokiem w
 * `parts/footer.html`, a `</div>` zamykający kolumnę PO nim — w Site
 * Editorze każdy fragment `core/html` renderuje się we WŁASNYM,
 * piaskownicowym iframe, więc `.footer-col` otwarty w jednym fragmencie i
 * zamknięty w kolejnym nie miał jak się scalić z blokiem pomiędzy (ground-truth
 * `docs/plan.md` D-25.2.1). Front-end BEZ ZMIAN — `render_block()` i tak
 * sklejał wszystkie fragmenty w jeden ciąg HTML.
 *
 * Czyta menu przez lokalizację (`get_nav_menu_locations()`), wzorem
 * `qutlet/header-nav` (P-16.1) i `Help::render_help_nav()` (P-1.5): pętla po
 * `wp_get_nav_menu_items()`, NIE `wp_nav_menu()` — domyślny `Walker_Nav_Menu`
 * opakowuje pozycje w `<ul>/<li>`, niezgodne z płaskim markupem `.footer-col`
 * (linki bezpośrednio wewnątrz `<div class="footer-col">`, `style.css:423`).
 * Brak menu przypisanego do lokalizacji (świeże środowisko przed seedem, albo
 * celowo pusta kolumna „Sklep") → `.footer-col` i nagłówek `<h5>` nadal się
 * renderują (tak jak dawniej statyczny markup dookoła bloku), po prostu bez
 * linków w środku.
 *
 * @package Qutlet\Theme
 *
 * @var array{menuLocation?: string} $attributes
 */

declare( strict_types=1 );

use Qutlet\Theme\features\FooterMenu\FooterMenu;

defined( 'ABSPATH' ) || exit;

$menu_location = isset( $attributes['menuLocation'] ) ? (string) $attributes['menuLocation'] : '';

$headings = array(
	FooterMenu::MENU_LOCATION_SKLEP       => __( 'Sklep', 'qutlet-theme' ),
	FooterMenu::MENU_LOCATION_INFORMACJE  => __( 'Informacje', 'qutlet-theme' ),
	FooterMenu::MENU_LOCATION_POMOC       => __( 'Pomoc', 'qutlet-theme' ),
);

$locations  = get_nav_menu_locations();
$menu_items = isset( $locations[ $menu_location ] )
	? wp_get_nav_menu_items( $locations[ $menu_location ] )
	: false;

echo '<div class="footer-col">';
printf( '<h5>%s</h5>', esc_html( $headings[ $menu_location ] ?? '' ) );

if ( is_array( $menu_items ) ) {
	foreach ( $menu_items as $item ) {
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $item->url ),
			esc_html( $item->title )
		);
	}
}

echo '</div>';
