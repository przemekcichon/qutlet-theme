<?php
/**
 * Blok `qutlet/header-categories-band` — pigułki `.subnav-link` (P-16.2b).
 * Pozycje menu `kategorie` z `widoczna_na_belce == true`, w natywnym
 * porządku menu ({@see \Qutlet\Theme\features\HeaderMenu\CategoryMenu::pills()}).
 * Przycisk „Więcej" (otwiera mega menu) zostaje statycznym markupem
 * `parts/header.html` dookoła tego bloku — bez zmian.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\CategoryMenu;

defined( 'ABSPATH' ) || exit;

foreach ( CategoryMenu::pills() as $item ) {
	printf(
		'<a href="%1$s" class="subnav-link">%2$s</a>',
		esc_url( $item->url ),
		esc_html( $item->title )
	);
}
