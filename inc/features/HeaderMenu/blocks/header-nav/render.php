<?php
/**
 * Blok `qutlet/header-nav` — pełny `<nav class="header-nav">` nagłówka
 * (P-25.2b, D-25.2.1). Renderuje WŁASNY wrapper `<nav>` razem z linkami menu
 * (lokalizacja `nawigacja`, {@see HeaderMenu::MENU_LOCATION}) ORAZ statycznymi
 * dropdownami koszyk/konto i przyciskiem hamburgera. Ten statyczny markup
 * dawniej żył w OSOBNYM fragmencie `wp:html` PO tym bloku w
 * `parts/header.html` — w Site Editorze każdy fragment `core/html` renderuje
 * się we WŁASNYM, piaskownicowym iframe (`srcdoc`), więc `<nav>` otwarty w
 * jednym fragmencie i zamknięty w kolejnym nie miał jak się scalić: przyciski
 * koszyk/konto/hamburger wyświetlały się rozłącznie od linków menu, jako
 * pionowy stos zamiast jednego paska (ground-truth `docs/plan.md` D-25.2.1).
 * Front-end BEZ ZMIAN — `render_block()` i tak sklejał oba fragmenty w jeden
 * ciąg HTML, to była wyłącznie usterka podglądu edytora. Wzorem
 * `qutlet/header-mega-grid` (P-16.2b): blok, który jest jedynym punktem
 * podziału wrappera, przejmuje CAŁY otaczający statyczny markup.
 *
 * Brak menu przypisanego do lokalizacji (świeże środowisko przed seedem,
 * D-16.G5) → `<nav>` nadal się renderuje (dropdowny/hamburger są od menu
 * niezależne), po prostu bez linków `.nav-link` w środku.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\HeaderMenu;

defined( 'ABSPATH' ) || exit;

$locations  = get_nav_menu_locations();
$menu_items = isset( $locations[ HeaderMenu::MENU_LOCATION ] )
	? wp_get_nav_menu_items( $locations[ HeaderMenu::MENU_LOCATION ] )
	: false;

echo '<nav class="header-nav">';

if ( is_array( $menu_items ) ) {
	foreach ( $menu_items as $item ) {
		printf(
			'<a href="%1$s" class="nav-link">%2$s</a>',
			esc_url( $item->url ),
			esc_html( $item->title )
		);
	}
}

echo '
      <div class="header-menu-wrap">
        <button type="button" class="icon-btn" data-toggle-menu="cart" aria-label="Koszyk">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
          <span class="cart-badge" data-cart-count hidden>0</span>
        </button>
        <div class="dropdown cart-menu" data-menu="cart" hidden></div>
      </div>

      <div class="header-menu-wrap">
        <button type="button" class="icon-btn" data-toggle-menu="account" aria-label="Moje konto">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"></path></svg>
        </button>
        <div class="dropdown acct-menu" data-menu="account" hidden></div>
      </div>

      <button type="button" class="icon-btn mnav-toggle" data-open-mnav aria-label="Menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
      </button>
    </nav>';
