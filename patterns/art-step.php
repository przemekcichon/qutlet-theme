<?php
/**
 * Title: Artykuł — numerowany krok
 * Slug: qutlet-theme/art-step
 * Categories: qutlet
 * Description: Pojedynczy numerowany krok instrukcji (P-11.5) — redaktor duplikuje blok i zmienia numer/treść dla kolejnych kroków wariantu. Port .art-step/.art-step-num (design/vanilla/blog-artykul.html).
 * Keywords: artykuł, blog, krok, instrukcja
 * Viewport Width: 720
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"art-step"} -->
<div class="wp-block-group art-step">

<!-- wp:paragraph {"className":"art-step-num"} -->
<p class="art-step-num">1</p>
<!-- /wp:paragraph -->

<!-- wp:group -->
<div class="wp-block-group">

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Tytuł kroku', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Opis kroku.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
