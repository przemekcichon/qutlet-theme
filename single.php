<?php
/**
 * Qutlet — pojedynczy wpis bloga (P-8.4, port design/vanilla/blog-artykul.html).
 *
 * Klasyczny szablon — patrz uzasadnienie w nagłówku `inc/features/Blog/Blog.php`.
 * Dotyczy WYŁĄCZNIE `post_type = post` (produkty mają własny
 * `templates/single-product.html`, P-8.2a) — WP sprawdza `single-{post_type}.php`
 * przed tym plikiem, więc bez kolizji.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\ArticleHeadings;
use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$post_id     = get_the_ID();
	$categories  = get_the_category( $post_id );
	$primary_cat = $categories[0] ?? null;
	$excerpt     = get_the_excerpt();

	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$hero_caption = $thumbnail_id ? wp_get_attachment_caption( $thumbnail_id ) : '';

	$published_ymd = get_the_date( 'Y-m-d' );
	$modified_ymd  = get_the_modified_date( 'Y-m-d' );
	$show_updated  = ( $modified_ymd !== $published_ymd );

	$byline_parts   = array();
	$byline_parts[] = get_the_date( 'j F Y' );
	$byline_parts[] = Blog::reading_time_label( $post_id );

	if ( $show_updated ) {
		/* translators: %s: miesiąc i rok aktualizacji. */
		$byline_parts[] = sprintf( __( 'aktualizacja: %s', 'qutlet-theme' ), get_the_modified_date( 'F Y' ) );
	}
	?>
	<main class="wrap page-main">
		<nav class="breadcrumbs">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Qutlet', 'qutlet-theme' ); ?></a><span class="sep">/</span>
			<a href="<?php echo esc_url( Blog::blog_url() ); ?>"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></a><span class="sep">/</span>
			<?php if ( $primary_cat instanceof WP_Term ) : ?>
				<a href="<?php echo esc_url( (string) get_category_link( $primary_cat ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a><span class="sep">/</span>
			<?php endif; ?>
			<span class="current"><?php the_title(); ?></span>
		</nav>

		<header class="art-head">
			<?php if ( $primary_cat instanceof WP_Term ) : ?>
				<span class="kicker"><?php echo esc_html( $primary_cat->name ); ?></span>
			<?php endif; ?>
			<h1 class="art-title"><?php the_title(); ?></h1>
			<?php if ( $excerpt ) : ?>
				<p class="art-lead"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>
			<div class="art-byline">
				<span class="acct-avatar"><?php echo esc_html( Blog::author_initials( (int) get_the_author_meta( 'ID' ) ) ); ?></span>
				<div>
					<div class="art-byline-name"><?php the_author(); ?></div>
					<div class="art-byline-meta"><?php echo esc_html( implode( ' · ', $byline_parts ) ); ?></div>
				</div>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="art-hero" style="margin-bottom: 0;">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
			<?php if ( $hero_caption ) : ?>
				<p class="art-hero-caption"><?php echo esc_html( $hero_caption ); ?></p>
			<?php endif; ?>
		<?php endif; ?>

		<div class="art-layout">
			<article class="art-body">
				<?php
				add_filter( 'the_content', array( ArticleHeadings::class, 'capture_and_anchor' ), 20 );
				the_content();
				remove_filter( 'the_content', array( ArticleHeadings::class, 'capture_and_anchor' ), 20 );

				$tags = get_the_tags( $post_id );
				?>

				<?php if ( is_array( $tags ) && ! empty( $tags ) ) : ?>
					<div class="art-tags">
						<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( (string) get_tag_link( $tag ) ); ?>" class="art-tag">#<?php echo esc_html( $tag->slug ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="author-box">
					<span class="acct-avatar"><?php echo esc_html( Blog::author_initials( (int) get_the_author_meta( 'ID' ) ) ); ?></span>
					<div>
						<h5><?php the_author(); ?></h5>
						<?php $bio = get_the_author_meta( 'description' ); ?>
						<?php if ( $bio ) : ?>
							<p><?php echo esc_html( $bio ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<?php
				$prev_post = get_previous_post( true, '', 'category' );
				$next_post = get_next_post( true, '', 'category' );
				?>
				<?php if ( $prev_post instanceof WP_Post || $next_post instanceof WP_Post ) : ?>
					<nav class="art-prevnext">
						<?php if ( $prev_post instanceof WP_Post ) : ?>
							<a href="<?php echo esc_url( (string) get_permalink( $prev_post ) ); ?>" class="art-pn">
								<span><?php esc_html_e( '← Poprzedni wpis', 'qutlet-theme' ); ?></span>
								<b><?php echo esc_html( get_the_title( $prev_post ) ); ?></b>
							</a>
						<?php endif; ?>
						<?php if ( $next_post instanceof WP_Post ) : ?>
							<a href="<?php echo esc_url( (string) get_permalink( $next_post ) ); ?>" class="art-pn art-pn-next">
								<span><?php esc_html_e( 'Następny wpis →', 'qutlet-theme' ); ?></span>
								<b><?php echo esc_html( get_the_title( $next_post ) ); ?></b>
							</a>
						<?php endif; ?>
					</nav>
				<?php endif; ?>
			</article>

			<aside class="art-side">
				<?php $headings = ArticleHeadings::get_captured(); ?>
				<?php if ( ! empty( $headings ) ) : ?>
					<div class="art-side-card">
						<h5><?php esc_html_e( 'W tym artykule', 'qutlet-theme' ); ?></h5>
						<nav class="art-toc">
							<?php foreach ( $headings as $heading ) : ?>
								<a href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['text'] ); ?></a>
							<?php endforeach; ?>
						</nav>
					</div>
				<?php endif; ?>

				<?php
				$shop_page_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
				$shop_url     = $shop_page_id > 0 ? (string) get_permalink( $shop_page_id ) : home_url( '/' );
				?>
				<div class="art-side-card art-side-deal">
					<h5><?php esc_html_e( 'Ze strefy okazji', 'qutlet-theme' ); ?></h5>
					<p><?php esc_html_e( 'Sprawdzony sprzęt po przeglądzie serwisowym — z klasą stanu i gwarancją.', 'qutlet-theme' ); ?></p>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-lime"><?php esc_html_e( 'Przeglądaj sklep', 'qutlet-theme' ); ?></a>
				</div>
			</aside>
		</div>

		<?php
		$related = array();

		if ( $primary_cat instanceof WP_Term ) {
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
		}
		?>
		<?php if ( ! empty( $related ) ) : ?>
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
		<?php endif; ?>
	</main>
	<?php
endwhile;

get_footer();
