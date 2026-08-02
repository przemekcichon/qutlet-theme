<?php
/**
 * Title: Siatka kart — 2eko (ekonomia + ekologia)
 * Slug: qutlet-theme/card-grid-eko
 * Categories: qutlet
 * Description: Siatka 2 kart z ikoną, kickerem, nagłówkiem i opisem. Port .eko-grid (design/vanilla/jak-to-dziala.html).
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
<!-- wp:html -->
<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5 5 19"></path><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg></span>
<!-- /wp:html -->
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
<!-- wp:html -->
<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path></svg></span>
<!-- /wp:html -->
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
