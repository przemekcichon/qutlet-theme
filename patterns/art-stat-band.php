<?php
/**
 * Title: Artykuł — rząd liczb-highlightów
 * Slug: qutlet-theme/art-stat-band
 * Categories: qutlet
 * Description: Rząd 2–3 liczb-highlightów obok siebie (P-11.5), np. rachunek kosztów w instrukcji. Port .art-stat-band/.art-stat (design/vanilla/blog-artykul.html).
 * Keywords: artykuł, blog, statystyki, liczby
 * Viewport Width: 720
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"art-stat-band"} -->
<div class="wp-block-group art-stat-band">

<!-- wp:group {"className":"art-stat"} -->
<div class="wp-block-group art-stat">
<!-- wp:paragraph -->
<p><b><?php esc_html_e( '339 zł', 'qutlet-theme' ); ?></b><span><?php esc_html_e( 'opis liczby', 'qutlet-theme' ); ?></span></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"art-stat"} -->
<div class="wp-block-group art-stat">
<!-- wp:paragraph -->
<p><b><?php esc_html_e( '~32 zł', 'qutlet-theme' ); ?></b><span><?php esc_html_e( 'opis liczby', 'qutlet-theme' ); ?></span></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"art-stat"} -->
<div class="wp-block-group art-stat">
<!-- wp:paragraph -->
<p><b><?php esc_html_e( '250 g', 'qutlet-theme' ); ?></b><span><?php esc_html_e( 'opis liczby', 'qutlet-theme' ); ?></span></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
