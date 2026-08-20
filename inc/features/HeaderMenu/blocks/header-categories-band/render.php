<?php
/**
 * Blok `qutlet/header-categories-band` — pełny pasek `.subnav-band` pod
 * nagłówkiem (P-25.2b, D-25.2.1). Renderuje WŁASNY wrapper
 * `.subnav-band > .wrap.subnav` razem z pigułkami `.subnav-link` (pozycje
 * menu `kategorie` z `widoczna_na_belce == true`, w natywnym porządku menu,
 * {@see \Qutlet\Theme\features\HeaderMenu\CategoryMenu::pills()}) ORAZ
 * statycznym przyciskiem „Więcej" (otwiera mega menu). Przycisk dawniej żył w
 * OSOBNYM fragmencie `wp:html` PO tym bloku w `parts/header.html` — w Site
 * Editorze każdy fragment `core/html` renderuje się we WŁASNYM,
 * piaskownicowym iframe, więc `.wrap.subnav` otwarty w jednym fragmencie i
 * zamknięty w kolejnym nie miał jak się scalić: przycisk „Więcej" renderował
 * się rozłącznie od pigułek (ground-truth `docs/plan.md` D-25.2.1). Front-end
 * BEZ ZMIAN — `render_block()` i tak sklejał oba fragmenty w jeden ciąg HTML.
 *
 * Brak pigułek (`CategoryMenu::pills()` puste) → pasek nadal się renderuje
 * (przycisk „Więcej" jest od kategorii niezależny), po prostu bez pigułek w
 * środku.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\HeaderMenu\CategoryMenu;

defined( 'ABSPATH' ) || exit;

echo '<div class="subnav-band">
    <div class="wrap subnav">';

foreach ( CategoryMenu::pills() as $item ) {
	printf(
		'<a href="%1$s" class="subnav-link">%2$s</a>',
		esc_url( $item->url ),
		esc_html( $item->title )
	);
}

echo '
      <button type="button" class="more-btn" data-toggle-mega>
        Więcej
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg>
      </button>
    </div>
  </div>';
