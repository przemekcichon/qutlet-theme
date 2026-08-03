<?php
/**
 * Title: Karta — okazja w sidebarze bloga
 * Slug: qutlet-theme/blog-deal-card
 * Categories: qutlet
 * Description: Promo-karta w sidebarze artykułu bloga („Ze strefy okazji") — statyczny marketing copy, przeniesiony z usuniętego single.php (P-11.3). Link do strony sklepu Woo liczony dynamicznie (wc_get_page_id), reszta treści edytowalna. Port .art-side-card.art-side-deal (design/vanilla/blog-artykul.html).
 * Keywords: blog, sidebar, cta
 * Viewport Width: 320
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$shop_page_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
$shop_url     = $shop_page_id > 0 ? (string) get_permalink( $shop_page_id ) : home_url( '/' );
?>
<!-- wp:group {"className":"art-side-card art-side-deal"} -->
<div class="wp-block-group art-side-card art-side-deal">

<!-- wp:heading {"level":5} -->
<h5 class="wp-block-heading"><?php esc_html_e( 'Ze strefy okazji', 'qutlet-theme' ); ?></h5>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Sprawdzony sprzęt po przeglądzie serwisowym — z klasą stanu i gwarancją.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-lime"><?php esc_html_e( 'Przeglądaj sklep', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
