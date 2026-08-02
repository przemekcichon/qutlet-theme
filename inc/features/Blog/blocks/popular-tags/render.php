<?php
/**
 * Blok `qutlet/popular-tags` — sekcja „Popularne tagi:" na dole archiwum
 * kategorii, port `Blog::popular_tags_in_category()` (P-8.4) z usuniętego
 * `category.php`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

if ( ! is_category() ) {
	return;
}

$term = get_queried_object();

if ( ! ( $term instanceof WP_Term ) ) {
	return;
}

$popular_tags = Blog::popular_tags_in_category( $term->term_id );

if ( empty( $popular_tags ) ) {
	return;
}
?>
<div class="term-related" style="margin-top: 34px; margin-bottom: 50px;">
	<span><?php esc_html_e( 'Popularne tagi:', 'qutlet-theme' ); ?></span>
	<?php foreach ( $popular_tags as $tag ) : ?>
		<a href="<?php echo esc_url( (string) get_tag_link( $tag ) ); ?>" class="art-tag">#<?php echo esc_html( $tag->slug ); ?></a>
	<?php endforeach; ?>
</div>
