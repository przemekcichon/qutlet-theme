<?php
/**
 * Qutlet — archiwum tagu bloga (P-8.4, port design/vanilla/blog-tag.html).
 *
 * Klasyczny szablon — patrz uzasadnienie w nagłówku `inc/features/Blog/Blog.php`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

get_header();

$current_tag = get_queried_object();
?>
<main class="wrap page-main">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Qutlet', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<a href="<?php echo esc_url( Blog::blog_url() ); ?>"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<?php if ( $current_tag instanceof WP_Term ) : ?>
			<?php
			/* translators: %s: nazwa tagu. */
			$current_label = sprintf( __( 'Tag: %s', 'qutlet-theme' ), $current_tag->name );
			?>
			<span class="current"><?php echo esc_html( $current_label ); ?></span>
		<?php endif; ?>
	</nav>

	<?php if ( $current_tag instanceof WP_Term ) : ?>
		<header class="tag-head">
			<span class="pill">
				<?php
				/* translators: %d: liczba wpisów. */
				printf( esc_html( _n( 'Tag · %d wpis', 'Tag · %d wpisów', (int) $current_tag->count, 'qutlet-theme' ) ), (int) $current_tag->count );
				?>
			</span>
			<h1><span class="accent">#</span><?php echo esc_html( $current_tag->slug ); ?></h1>
			<?php if ( $current_tag->description ) : ?>
				<p><?php echo esc_html( $current_tag->description ); ?></p>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<?php
	$related_tags     = $current_tag instanceof WP_Term ? Blog::related_tags( $current_tag ) : array();
	$related_category = $current_tag instanceof WP_Term ? Blog::primary_category_for_tag( $current_tag ) : null;
	?>
	<?php if ( ! empty( $related_tags ) || $related_category instanceof WP_Term ) : ?>
		<div class="term-related">
			<span><?php esc_html_e( 'Powiązane:', 'qutlet-theme' ); ?></span>
			<?php foreach ( $related_tags as $tag ) : ?>
				<a href="<?php echo esc_url( (string) get_tag_link( $tag ) ); ?>" class="art-tag">#<?php echo esc_html( $tag->slug ); ?></a>
			<?php endforeach; ?>
			<?php if ( $related_category instanceof WP_Term ) : ?>
				<a href="<?php echo esc_url( (string) get_category_link( $related_category ) ); ?>" class="art-tag">
					<?php
					/* translators: %s: nazwa kategorii. */
					printf( esc_html__( 'Kategoria: %s', 'qutlet-theme' ), esc_html( $related_category->name ) );
					?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="post-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part(
					'template-parts/blog/post-card',
					null,
					array(
						'post'    => get_post(),
						'variant' => 'horizontal',
					)
				);
			endwhile;
			?>
		</div>

		<?php Blog::render_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Brak wpisów z tym tagiem.', 'qutlet-theme' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
