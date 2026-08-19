<?php
/**
 * Blok `qutlet/footer-nav` — płaska lista linków `.footer-col` (P-23.1).
 * Parametryzowany atrybutem `menuLocation` (jedna z 3 lokalizacji
 * `FooterMenu::MENU_LOCATION_*`), osadzany 3× w `parts/footer.html`, po
 * jednym na kolumnę — kolumny są strukturalnie identyczne (nagłówek `<h5>`
 * statyczny w `parts/footer.html` + płaska lista `<a>` z tego bloku), więc
 * jeden parametryzowany blok zamiast 3 osobnych (wzorem `qutlet/post-card`,
 * `inc/features/Blog/blocks/post-card`, atrybut `variant`).
 *
 * Czyta menu przez lokalizację (`get_nav_menu_locations()`), wzorem
 * `qutlet/header-nav` (P-16.1) i `Help::render_help_nav()` (P-1.5): pętla po
 * `wp_get_nav_menu_items()`, NIE `wp_nav_menu()` — domyślny `Walker_Nav_Menu`
 * opakowuje pozycje w `<ul>/<li>`, niezgodne z płaskim markupem `.footer-col`
 * (linki bezpośrednio wewnątrz `<div class="footer-col">`, `style.css:423`).
 * Brak menu przypisanego do lokalizacji (świeże środowisko przed seedem, albo
 * celowo pusta kolumna „Sklep") → nic nie renderuje, stopka nie wywraca się
 * bez menu.
 *
 * @package Qutlet\Theme
 *
 * @var array{menuLocation?: string} $attributes
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$menu_location = isset( $attributes['menuLocation'] ) ? (string) $attributes['menuLocation'] : '';

if ( '' === $menu_location ) {
	return;
}

$locations = get_nav_menu_locations();

if ( ! isset( $locations[ $menu_location ] ) ) {
	return;
}

$items = wp_get_nav_menu_items( $locations[ $menu_location ] );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

foreach ( $items as $item ) {
	printf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $item->url ),
		esc_html( $item->title )
	);
}
