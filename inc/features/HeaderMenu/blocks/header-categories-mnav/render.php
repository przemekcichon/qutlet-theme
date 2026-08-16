<?php
/**
 * Blok `qutlet/header-categories-mnav` — sekcja „Kategorie" mobilnego
 * `.mnav-panel` (P-16.2b). TEN SAM zestaw co pigułki `.subnav-band`
 * ({@see \Qutlet\Theme\features\HeaderMenu\CategoryMenu::pills()},
 * `widoczna_na_belce == true`) — mobile NIE pokazuje pełnych pozycji ze
 * wszystkich kolumn mega menu (ground-truth `docs/plan.md` FAZA 16). Statyczny
 * catch-all „Wszystkie kategorie" + reszta `.mnav-panel` zostają markupem
 * `parts/header.html` dookoła tego bloku — bez zmian.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\CategoryMenu;

defined( 'ABSPATH' ) || exit;

$pills = CategoryMenu::pills();

if ( empty( $pills ) ) {
	return;
}

echo '<div class="mnav-label">' . esc_html__( 'Kategorie', 'qutlet-theme' ) . '</div>';

foreach ( $pills as $item ) {
	printf(
		'<a href="%1$s" class="mnav-link">%2$s</a>',
		esc_url( $item->url ),
		esc_html( $item->title )
	);
}
