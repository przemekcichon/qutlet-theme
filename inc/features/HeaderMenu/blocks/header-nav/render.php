<?php
/**
 * Blok `qutlet/header-nav` — 4 proste linki `.header-nav` (Strefa okazji /
 * Jak to działa? / Blog / Pomoc), P-16.1. Czyta menu przypisane do
 * lokalizacji `nawigacja` (`HeaderMenu::MENU_LOCATION`) i renderuje płaskie
 * `<a class="nav-link">` — wzorem `Help::render_help_nav()` (P-1.5, pętla po
 * `wp_get_nav_menu_items()`, NIE `wp_nav_menu()`): domyślny
 * `Walker_Nav_Menu` zawsze opakowuje pozycje w `<ul>`/`<li>`, niezgodnie z
 * markupem `.header-nav` (linki bezpośrednio wewnątrz `<nav>`, flex z `gap`,
 * `style.css:286`) — własny walker rozwiązujący to inline koliduje z
 * odziedziczonym typowaniem `WP_Post` w sygnaturze
 * `Walker_Nav_Menu::start_el()` w stubach PHPStan (`url`/`title` to
 * właściwości dostawiane w locie przez `wp_setup_nav_menu_item()`, których
 * `final class WP_Post` w stubie nie zna), a `wp_nav_menu(['container' =>
 * false, ...])` koliduje z typem `container?: string` w tych samych stubach
 * (nie dopuszcza `false`, mimo że WP-core realnie to wspiera). Pętla po
 * nieotypowanym `array` z `wp_get_nav_menu_items()` obu problemów unika.
 * Brak menu przypisanego do lokalizacji (świeże środowisko przed seedem,
 * D-16.G5) → nic nie renderuje, nagłówek nie wywraca się bez menu.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\HeaderMenu;

defined( 'ABSPATH' ) || exit;

$locations = get_nav_menu_locations();

if ( ! isset( $locations[ HeaderMenu::MENU_LOCATION ] ) ) {
	return;
}

$items = wp_get_nav_menu_items( $locations[ HeaderMenu::MENU_LOCATION ] );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

foreach ( $items as $item ) {
	printf(
		'<a href="%1$s" class="nav-link">%2$s</a>',
		esc_url( $item->url ),
		esc_html( $item->title )
	);
}
