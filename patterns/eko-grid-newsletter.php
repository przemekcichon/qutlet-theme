<?php
/**
 * Title: Siatka kart — 2eko (newsletter)
 * Slug: qutlet-theme/eko-grid-newsletter
 * Categories: qutlet
 * Description: Siatka 2 kart eko z treścią specyficzną dla newslettera (inna niż qutlet-theme/card-grid-eko, które porcjuje treść jak-to-dziala.html — te same klasy CSS, inny tekst/ikony źródłowe). Port .eko-grid (design/vanilla/newsletter.html). Ikony jako `core/spacer` + CSS `background-image` (P-11.5, ta sama poprawka co home-usp) — nie `wp:html`, patrz opis tamtego patternu. Ikony wizualnie identyczne z card-grid-eko (te same klasy `.eko-icon-percent`/`.eko-icon-leaf` — drobne różnice współrzędnych w oryginalnych SVG prototypu nieistotne wizualnie).
 * Keywords: karty, siatka, eko, newsletter
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"eko-grid"} -->
<div class="wp-block-group eko-grid">

<!-- wp:group {"className":"eko-card"} -->
<div class="wp-block-group eko-card">
<!-- wp:spacer {"height":"44px","className":"eko-icon eko-icon-percent"} -->
<div style="height:44px" aria-hidden="true" class="wp-block-spacer eko-icon eko-icon-percent"></div>
<!-- /wp:spacer -->
<!-- wp:paragraph {"className":"eko-kicker"} -->
<p class="eko-kicker"><?php esc_html_e( 'Eko #1 — Ekonomia', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Pełna wartość, ułamek ceny', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Płacisz za sprzęt, nie za karton i metkę „nówki”. Ten sam model, ta sama jakość, nawet -50%. Dla ekopragmatyka to nie kompromis — to lepiej policzony zakup.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"eko-card"} -->
<div class="wp-block-group eko-card">
<!-- wp:spacer {"height":"44px","className":"eko-icon eko-icon-leaf"} -->
<div style="height:44px" aria-hidden="true" class="wp-block-spacer eko-icon eko-icon-leaf"></div>
<!-- /wp:spacer -->
<!-- wp:paragraph {"className":"eko-kicker"} -->
<p class="eko-kicker"><?php esc_html_e( 'Eko #2 — Ekologia', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Mniej e-waste, realny ślad', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Każdy kupiony egzemplarz to jeden mniej na wysypisku. Przedłużasz życie sprawnej elektroniki i realnie obniżasz swój ślad — bez wyrzeczeń i bez ściemy.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
