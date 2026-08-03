<?php
/**
 * Blok `qutlet/featured-post` — karta wyróżnionego (sticky) wpisu
 * (`.featured-post`), port sekcji z usuniętego `home.php` (P-8.4). Widoczny
 * WYŁĄCZNIE na pierwszej stronie archiwum bloga, gdy istnieje wpis sticky —
 * wpis jest wykluczony z głównej pętli osobno, patrz
 * `Blog::exclude_featured_from_main_query()`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

if ( ! is_home() || is_paged() ) {
	return;
}

$featured_id = Blog::featured_post_id();

if ( $featured_id <= 0 || ! ( get_post( $featured_id ) instanceof WP_Post ) ) {
	return;
}

$featured_categories = get_the_category( $featured_id );
$featured_category   = $featured_categories[0] ?? null;
$featured_excerpt    = get_the_excerpt( $featured_id );
?>
<a href="<?php echo esc_url( (string) get_permalink( $featured_id ) ); ?>" class="featured-post">
	<div class="featured-post-body">
		<h2><?php echo esc_html( get_the_title( $featured_id ) ); ?></h2>
		<?php if ( $featured_excerpt ) : ?>
			<p class="lead"><?php echo esc_html( $featured_excerpt ); ?></p>
		<?php endif; ?>
		<div class="featured-meta">
			<?php if ( $featured_category instanceof WP_Term ) : ?>
				<span class="post-cat"><?php echo esc_html( $featured_category->name ); ?></span>
			<?php endif; ?>
			<span><?php echo esc_html( get_the_date( 'j F Y', $featured_id ) ); ?></span>
			<span><?php echo esc_html( Blog::reading_time_label( $featured_id ) ); ?></span>
		</div>
		<span class="featured-cta">
			<?php esc_html_e( 'Czytaj artykuł', 'qutlet-theme' ); ?>
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
		</span>
	</div>
	<div class="featured-media">
		<?php if ( has_post_thumbnail( $featured_id ) ) : ?>
			<?php echo get_the_post_thumbnail( $featured_id, 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="ph-stripes ph-stripes-dark">[ <?php esc_html_e( 'brak zdjęcia', 'qutlet-theme' ); ?> ]</div>
		<?php endif; ?>
	</div>
</a>
