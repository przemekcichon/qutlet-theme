<?php
/**
 * Title: Pomoc — szybkie linki
 * Slug: qutlet-theme/help-quick-links
 * Categories: qutlet
 * Description: Rząd pigułek-linków do najczęstszych tematów. Port .help-quick (design/vanilla/pomoc.html). Kotwice #s4-#s7 celują w nagłówki regulaminu — przy edycji zachować te same „HTML anchor” na docelowych Heading blokach strony Regulamin.
 * Keywords: pomoc, linki, kotwice
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"help-quick"} -->
<div class="wp-block-group help-quick">

<!-- wp:paragraph -->
<p><a href="/regulamin/#s6"><?php esc_html_e( 'Zwrot w 14 dni', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/regulamin/#s7"><?php esc_html_e( 'Gwarancja i reklamacje', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/regulamin/#s5"><?php esc_html_e( 'Dostawa', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/regulamin/#s4"><?php esc_html_e( 'Ceny i płatności', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/jak-to-dziala/#klasy"><?php esc_html_e( 'Klasy produktów A–D', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/jak-to-dziala/"><?php esc_html_e( 'Skąd mamy sprzęt?', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
