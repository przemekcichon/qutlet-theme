<?php
/**
 * Title: Siatka kart — linki dokumentów
 * Slug: qutlet-theme/card-grid-links
 * Categories: qutlet
 * Description: Siatka 2×2 kart-linków (tytuł, opis, „czytaj dalej”). Port .help-cards (design/vanilla/pomoc.html).
 * Keywords: karty, siatka, pomoc, linki
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"help-cards"} -->
<div class="wp-block-group help-cards">

<!-- wp:group {"className":"help-card"} -->
<div class="wp-block-group help-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Regulamin', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Zasady zakupów: zamówienia, płatności, dostawa, zwroty i reklamacje.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"go"} -->
<p class="go"><a href="/regulamin/"><?php esc_html_e( 'Czytaj regulamin →', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"help-card"} -->
<div class="wp-block-group help-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Polityka prywatności', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Jakie dane zbieramy, po co i jakie masz prawa (RODO).', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"go"} -->
<p class="go"><a href="/polityka-prywatnosci/"><?php esc_html_e( 'Czytaj politykę →', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"help-card"} -->
<div class="wp-block-group help-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Polityka cookies', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Pełny wykaz cookies i trackerów z celami oraz okresami przechowywania.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"go"} -->
<p class="go"><a href="/polityka-cookies/"><?php esc_html_e( 'Zobacz wykaz →', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"help-card"} -->
<div class="wp-block-group help-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Kontakt', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Formularz, e-mail i godziny odpowiedzi. Pytania o konkretny egzemplarz mile widziane.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"go"} -->
<p class="go"><a href="/kontakt/"><?php esc_html_e( 'Napisz do nas →', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
