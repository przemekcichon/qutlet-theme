<?php
/**
 * Title: Artykuł — dymek ostrzeżenia
 * Slug: qutlet-theme/art-warn-note
 * Categories: qutlet
 * Description: Dymek ostrzeżenia z ikoną w treści artykułu (P-11.5). Port .warn-note (design/vanilla/blog-artykul.html — ta sama klasa co stały dymek gwarancyjny na stronie produktu, woocommerce/content-single-product.php, ale tu jako WŁASNY, edytowalny pattern dla redaktora treści).
 * Keywords: artykuł, blog, uwaga, ostrzeżenie
 * Viewport Width: 720
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"warn-note"} -->
<div class="wp-block-group warn-note">

<!-- wp:html -->
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
<!-- /wp:html -->

<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Uwaga.', 'qutlet-theme' ); ?></strong> <?php esc_html_e( 'Treść ostrzeżenia.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
