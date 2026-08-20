<?php
/**
 * Blok `qutlet/header-mobile-nav` — pełny panel mobilnej nawigacji
 * `.mnav-overlay[data-mnav hidden] > .mnav-panel` (P-25.2b, D-25.2.1).
 * Zastępuje dawny `qutlet/header-categories-mnav` (usunięty) — przejmuje jego
 * logikę ({@see \Qutlet\Theme\features\HeaderMenu\CategoryMenu::pills()},
 * TEN SAM zestaw co pigułki `.subnav-band`, `widoczna_na_belce == true`) ORAZ
 * cały otaczający statyczny markup (przycisk zamknięcia, statyczne linki
 * menu), bo sam wrapper `hidden` był dawniej rozcięty na 3 fragmenty
 * `wp:html` w `parts/header.html`: `.mnav-overlay`/`.mnav-panel` otwierały
 * się w fragmencie PRZED `qutlet/header-categories-mnav`, a zamykały w
 * fragmencie PO nim. W Site Editorze każdy fragment `core/html` renderuje
 * się we WŁASNYM, piaskownicowym iframe, więc atrybut `hidden` (na tagu
 * otwierającym w pierwszym fragmencie) nie obejmował ani bloku kategorii, ani
 * końcowych statycznych linków (w kolejnych, osobnych iframe'ach) — panel
 * renderował się jako rozłączne, zawsze widoczne fragmenty zamiast jednego
 * ukrytego menu (ground-truth `docs/plan.md` D-25.2.1). Front-end BEZ ZMIAN —
 * `render_block()` i tak sklejał wszystkie fragmenty w jeden ciąg HTML.
 *
 * Brak pigułek (`CategoryMenu::pills()` puste) → panel nadal się renderuje
 * (przycisk zamknięcia i statyczne linki są od kategorii niezależne), po
 * prostu bez sekcji „Kategorie" w środku — tak jak dawniej statyczny markup
 * dookoła `qutlet/header-categories-mnav`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\CategoryMenu;

defined( 'ABSPATH' ) || exit;

$pills = CategoryMenu::pills();

echo '<div class="mnav-overlay" data-mnav hidden>
  <div class="mnav-panel">
    <button type="button" class="icon-btn mnav-close" data-close-mnav aria-label="Zamknij">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
    </button>';

if ( ! empty( $pills ) ) {
	echo '<div class="mnav-label">' . esc_html__( 'Kategorie', 'qutlet-theme' ) . '</div>';

	foreach ( $pills as $item ) {
		printf(
			'<a href="%1$s" class="mnav-link">%2$s</a>',
			esc_url( $item->url ),
			esc_html( $item->title )
		);
	}
}

echo '
    <a href="#" class="mnav-link">Wszystkie kategorie</a>
    <div class="mnav-sep"></div>
    <a href="#" class="mnav-link">Strefa okazji</a>
    <a href="/jak-to-dziala/" class="mnav-link">Jak to działa?</a>
    <a href="/blog/" class="mnav-link">Blog „Drugi obieg”</a>
    <a href="/pomoc/" class="mnav-link">Pomoc</a>
    <a href="#" class="mnav-link" data-mnav-account>Zaloguj się</a>
  </div>
</div>';
