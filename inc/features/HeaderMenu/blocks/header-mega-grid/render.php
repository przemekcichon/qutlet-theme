<?php
/**
 * Blok `qutlet/header-mega-grid` — pełny panel `.mega[data-mega hidden]`
 * mega menu kategorii (P-16.2b, rozszerzone P-25.2b/D-25.2.1). Renderuje
 * WŁASNY zewnętrzny wrapper `hidden` ORAZ `.wrap.mega-grid` z kolumnami
 * `.mega-col` w środku (grupowanie po `grupa_mega_menu`, sortowanie po
 * term-meta `kolejnosc`, {@see
 * \Qutlet\Theme\features\HeaderMenu\CategoryMenu::columns()}) — jedyne
 * miejsce, które zna faktyczną liczbę UŻYTYCH kolumn, potrzebną do
 * ustawienia `--mega-cols` inline (D-16.G3, CSS elastyczny w `style.css`).
 * Wrapper `.mega[data-mega hidden]` dawniej otwierał się i zamykał w OSOBNYCH
 * fragmentach `wp:html` wokół tego bloku w `parts/header.html` — w Site
 * Editorze każdy fragment `core/html` renderuje się we WŁASNYM,
 * piaskownicowym iframe, więc atrybut `hidden` (na tagu otwierającym w
 * jednym fragmencie) nie obejmował treści bloku (w INNYM iframe): panel
 * renderował się jako zawsze widoczny blok najwyższego poziomu zamiast
 * ukrytego menu (ground-truth `docs/plan.md` D-25.2.1). Front-end BEZ ZMIAN —
 * `render_block()` i tak sklejał wszystkie fragmenty w jeden ciąg HTML.
 *
 * Brak kolumn (`CategoryMenu::columns()` puste) → `.mega[hidden]` nadal się
 * renderuje jako pusty wrapper (tak jak dawniej statyczny markup w
 * `parts/header.html`), po prostu bez `.wrap.mega-grid` w środku.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\CategoryMenu;

defined( 'ABSPATH' ) || exit;

$columns = CategoryMenu::columns();

echo '<div class="mega" data-mega hidden>';

if ( ! empty( $columns ) ) {
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
}

echo '</div>';
