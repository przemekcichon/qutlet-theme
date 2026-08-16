<?php
/**
 * Blok `qutlet/header-mega-grid` — kolumny `.mega-col` mega menu kategorii
 * (P-16.2b). Renderuje TAKŻE samo `.wrap.mega-grid` (nie tylko kolumny w
 * środku) — to jedyne miejsce, które zna faktyczną liczbę UŻYTYCH kolumn,
 * potrzebną do ustawienia `--mega-cols` inline (D-16.G3, CSS elastyczny w
 * `style.css`); `.mega[data-mega]` dookoła zostaje statycznym markupem
 * `parts/header.html`. Kolumny = {@see
 * \Qutlet\Theme\features\HeaderMenu\CategoryMenu::columns()} (grupowanie po
 * `grupa_mega_menu`, sortowanie po term-meta `kolejnosc`).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\CategoryMenu;

defined( 'ABSPATH' ) || exit;

$columns = CategoryMenu::columns();

if ( empty( $columns ) ) {
	return;
}

printf( '<div class="wrap mega-grid" style="--mega-cols:%d">', count( $columns ) );

foreach ( $columns as $column ) {
	echo '<div class="mega-col">';
	printf( '<h6>%s</h6>', esc_html( $column['label'] ) );

	foreach ( $column['items'] as $item ) {
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $item->url ),
			esc_html( $item->title )
		);
	}

	echo '</div>';
}

echo '</div>';
