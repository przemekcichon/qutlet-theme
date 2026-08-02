<?php
/**
 * Title: Cytat — quote band
 * Slug: qutlet-theme/quote-band
 * Categories: qutlet
 * Description: Ciemna, wyśrodkowana sekcja cytatu z podpisem. Port .quote-band (design/vanilla/newsletter.html).
 * Keywords: cytat, quote, manifest
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"quote-band"} -->
<div class="wp-block-group quote-band">

<!-- wp:quote -->
<blockquote class="wp-block-quote">
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Stać Cię na nówkę? Tym lepiej.', 'qutlet-theme' ); ?> <span class="lime"><?php esc_html_e( 'Ekopragmatyk', 'qutlet-theme' ); ?></span> <?php esc_html_e( 'i tak wybierze Qutlet — bo przepłacanie za karton to nie luksus, to nieuwaga.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph {"className":"attribution"} -->
<p class="attribution"><?php esc_html_e( 'Manifest Ekołowców', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
