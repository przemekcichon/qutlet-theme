<?php
/**
 * Blok `qutlet/related-posts` — sekcja „Podobne wpisy" (`.art-related`), port
 * sekcji z usuniętego `single.php` (P-8.4). Karty wewnątrz reużywają
 * BEZPOŚREDNIO `template-parts/blog/post-card.php` (jak `qutlet/post-card`),
 * nie zagnieżdżony blok — prostszy render, bez potrzeby przechodzenia przez
 * `render_block()` dla trzech znanych z góry postów.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) ) {
	return;
}

$post_id     = get_the_ID();
$categories  = get_the_category( $post_id );
$primary_cat = $categories[0] ?? null;

if ( ! ( $primary_cat instanceof WP_Term ) ) {
	return;
}

$related = get_posts(
	array(
		'category__in'        => array( $primary_cat->term_id ),
		'post__not_in'        => array( $post_id ),
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	)
);

if ( empty( $related ) ) {
	return;
}
?>
<section class="art-related">
	<div class="section-head">
		<div>
			<h2 class="section-title"><?php esc_html_e( 'Podobne wpisy', 'qutlet-theme' ); ?></h2>
			<p class="section-sub">
				<?php
				/* translators: %s: nazwa kategorii. */
				printf( esc_html__( 'Więcej z kategorii %s', 'qutlet-theme' ), esc_html( $primary_cat->name ) );
				?>
			</p>
		</div>
		<a href="<?php echo esc_url( (string) get_category_link( $primary_cat ) ); ?>" class="link-accent">
			<?php esc_html_e( 'Cała kategoria', 'qutlet-theme' ); ?>
			<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
		</a>
	</div>
	<div class="post-grid">
		<?php foreach ( $related as $related_post ) : ?>
			<?php
			get_template_part(
				'template-parts/blog/post-card',
				null,
				array(
					'post'    => $related_post,
					'variant' => 'grid',
				)
			);
			?>
		<?php endforeach; ?>
	</div>
</section>
