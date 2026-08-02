<?php
/**
 * Title: Siatka kart — 2eko (newsletter)
 * Slug: qutlet-theme/eko-grid-newsletter
 * Categories: qutlet
 * Description: Siatka 2 kart eko z treścią specyficzną dla newslettera (inna niż qutlet-theme/card-grid-eko, które porcjuje treść jak-to-dziala.html — te same klasy CSS, inny tekst/ikony źródłowe). Port .eko-grid (design/vanilla/newsletter.html).
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
<!-- wp:html -->
<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5 5 19"></path><circle cx="7.5" cy="7.5" r="2.5"></circle><circle cx="16.5" cy="16.5" r="2.5"></circle></svg></span>
<!-- /wp:html -->
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
<!-- wp:html -->
<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path></svg></span>
<!-- /wp:html -->
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
