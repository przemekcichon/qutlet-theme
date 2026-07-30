<?php
/**
 * Qutlet — archiwum kategorii bloga (P-8.4, port design/vanilla/blog-kategoria.html).
 *
 * Klasyczny szablon — patrz uzasadnienie w nagłówku `inc/features/Blog/Blog.php`.
 * Dotyczy WYŁĄCZNIE natywnej taksonomii `category` (blog stoi na natywnych
 * wpisach WP, D-1.4.1) — `product_cat` ma własny
 * `templates/taxonomy-product_cat.html` (P-8.3a), więc bez kolizji.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

get_header();

$current_term = get_queried_object();
?>
<main class="wrap page-main">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Qutlet', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<a href="<?php echo esc_url( Blog::blog_url() ); ?>"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php echo esc_html( $current_term instanceof WP_Term ? $current_term->name : '' ); ?></span>
	</nav>

	<?php if ( $current_term instanceof WP_Term ) : ?>
		<header class="term-hero">
			<div>
				<span class="kicker"><?php esc_html_e( 'Kategoria', 'qutlet-theme' ); ?></span>
				<h1><?php echo esc_html( $current_term->name ); ?></h1>
				<?php if ( $current_term->description ) : ?>
					<p><?php echo esc_html( $current_term->description ); ?></p>
				<?php endif; ?>
			</div>
			<div class="term-hero-stat">
				<b><?php echo esc_html( (string) $current_term->count ); ?></b>
				<span><?php esc_html_e( 'wpisów w kategorii', 'qutlet-theme' ); ?></span>
			</div>
		</header>
	<?php endif; ?>

	<?php Blog::render_category_chips( $current_term instanceof WP_Term ? $current_term : null ); ?>

	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part(
					'template-parts/blog/post-card',
					null,
					array(
						'post'    => get_post(),
						'variant' => 'grid',
					)
				);
			endwhile;
			?>
		</div>

		<?php Blog::render_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Brak wpisów w tej kategorii.', 'qutlet-theme' ); ?></p>
	<?php endif; ?>

	<?php
	$popular_tags = $current_term instanceof WP_Term ? Blog::popular_tags_in_category( $current_term->term_id ) : array();
	?>
	<?php if ( ! empty( $popular_tags ) ) : ?>
		<div class="term-related" style="margin-top: 34px; margin-bottom: 50px;">
			<span><?php esc_html_e( 'Popularne tagi:', 'qutlet-theme' ); ?></span>
			<?php foreach ( $popular_tags as $tag ) : ?>
				<a href="<?php echo esc_url( (string) get_tag_link( $tag ) ); ?>" class="art-tag">#<?php echo esc_html( $tag->slug ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</main>
<?php
get_footer();
