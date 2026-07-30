<?php
/**
 * Template-part: karta wpisu bloga (P-8.4), port `.post-card`/`.post-hcard`
 * (`blog.html:60-140`, `blog-tag.html:42-86` — struktura identyczna, różni
 * się WYŁĄCZNIE klasą kontenera, reszta CSS w `.post-hcard .post-*`).
 *
 * Argumenty (przez `get_template_part( 'template-parts/blog/post-card', null, $args )`):
 * - post    WP_Post Wpis do wyrenderowania (wymagany).
 * - variant string  'grid' (domyślnie, `.post-card`) albo 'horizontal' (`.post-hcard`, tag.php).
 *
 * @package Qutlet\Theme
 *
 * @var array{post?: WP_Post, variant?: string} $args
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

$post_obj = $args['post'] ?? null;

if ( ! $post_obj instanceof WP_Post ) {
	return;
}

$post_id     = $post_obj->ID;
$variant     = ( 'horizontal' === ( $args['variant'] ?? 'grid' ) ) ? 'horizontal' : 'grid';
$card_class  = 'horizontal' === $variant ? 'post-hcard' : 'post-card';
$categories  = get_the_category( $post_id );
$primary_cat = $categories[0] ?? null;
?>
<a href="<?php echo esc_url( (string) get_permalink( $post_id ) ); ?>" class="<?php echo esc_attr( $card_class ); ?>">
	<div class="post-media">
		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<?php echo get_the_post_thumbnail( $post_id, 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="ph-stripes">[ <?php esc_html_e( 'brak zdjęcia', 'qutlet-theme' ); ?> ]</div>
		<?php endif; ?>
	</div>
	<div class="post-body">
		<div class="post-topline">
			<?php if ( $primary_cat instanceof WP_Term ) : ?>
				<span class="post-cat"><?php echo esc_html( $primary_cat->name ); ?></span>
			<?php endif; ?>
			<span class="post-read"><?php echo esc_html( Blog::reading_time_label( $post_id, false ) ); ?></span>
		</div>
		<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
		<p><?php echo esc_html( get_the_excerpt( $post_obj ) ); ?></p>
		<div class="post-meta"><?php echo esc_html( get_the_date( 'j F Y', $post_id ) ); ?></div>
	</div>
</a>
