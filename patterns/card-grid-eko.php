<?php
/**
 * Title: Siatka kart — 2eko (ekonomia + ekologia)
 * Slug: qutlet-theme/card-grid-eko
 * Categories: qutlet
 * Description: Siatka 2 kart z ikoną, kickerem, nagłówkiem i opisem. Port .eko-grid (design/vanilla/jak-to-dziala.html). Ikony jako `core/spacer` + CSS `background-image` (P-11.5, ta sama poprawka co home-usp) — nie `wp:html`, patrz opis tamtego patternu.
 * Keywords: karty, siatka, eko
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
<h3 class="wp-block-heading"><?php esc_html_e( 'Sprytny zakup, nie kompromis', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Ten sam model, ta sama jakość — tylko bez dopłaty za folię i status „nówki”. Ekopragmatyk nie kupuje taniej, bo musi. Kupuje taniej, bo przepłacanie za karton to nieuwaga.', 'qutlet-theme' ); ?></p>
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
<h3 class="wp-block-heading"><?php esc_html_e( 'Bonus, który dostajesz gratis', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Każdy taki zakup przedłuża życie sprzętu, który już wyprodukowano. Nie musisz się z tym obnosić — ekologia po prostu dzieje się przy okazji, za każdym razem.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
