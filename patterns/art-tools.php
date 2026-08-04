<?php
/**
 * Title: Artykuł — czego potrzebujesz
 * Slug: qutlet-theme/art-tools
 * Categories: qutlet
 * Description: Ramka z checklistą narzędzi/materiałów potrzebnych do instrukcji (P-11.5). Port .art-tools (design/vanilla/blog-artykul.html). Lista jako blok Custom HTML (ikony checkmarków SVG nie mieszczą się w edytowalnym core/list) — nagłówek osobnym, edytowalnym blokiem.
 * Keywords: artykuł, blog, narzędzia, checklista
 * Viewport Width: 720
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"art-tools"} -->
<div class="wp-block-group art-tools">

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Czego potrzebujesz', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->

<!-- wp:html -->
<ul>
	<li><span class="check-ico"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 13 4 4L19 7"></path></svg></span><span><?php esc_html_e( 'Narzędzie/materiał', 'qutlet-theme' ); ?> <small><?php esc_html_e( '— szacunkowy koszt', 'qutlet-theme' ); ?></small></span></li>
	<li><span class="check-ico"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 13 4 4L19 7"></path></svg></span><span><?php esc_html_e( 'Narzędzie/materiał', 'qutlet-theme' ); ?> <small><?php esc_html_e( '— szacunkowy koszt', 'qutlet-theme' ); ?></small></span></li>
</ul>
<!-- /wp:html -->

</div>
<!-- /wp:group -->
