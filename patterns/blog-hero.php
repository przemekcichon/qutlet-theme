<?php
/**
 * Title: Hero — archiwum bloga
 * Slug: qutlet-theme/blog-hero
 * Categories: qutlet
 * Description: Nagłówek archiwum bloga „Drugi obieg" (tytuł + lead) — statyczny marketing copy, przeniesiony z usuniętego home.php (P-11.3). Port .page-head (design/vanilla/blog.html).
 * Keywords: blog, hero
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"header","className":"page-head"} -->
<header class="wp-block-group page-head">

<!-- wp:heading {"level":1,"className":"page-title"} -->
<h1 class="wp-block-heading page-title"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"page-lead"} -->
<p class="page-lead"><?php esc_html_e( 'Blog Qutlet o elektronice, która nie kończy na wysypisku: naprawy, testy sprzętu z drugiej ręki i twarde liczby o e-waste.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

</header>
<!-- /wp:group -->
