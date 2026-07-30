<?php
/**
 * Qutlet — archiwum bloga „Drugi obieg" (P-8.4, port design/vanilla/blog.html).
 *
 * Klasyczny szablon (nie `templates/home.html`) — patrz uzasadnienie w
 * nagłówku `inc/features/Blog/Blog.php`. Główne zapytanie to NATYWNA pętla WP
 * dla `is_home()` (WP_Query zbudowane przez rdzeń, respektuje Ustawienia →
 * Czytanie: „Strona wpisów" = strona „Blog", `/blog/` — decyzja sesji
 * 2026-07-30, patrz handoff P-8.4) — theme niczego tu nie modyfikuje
 * (D-8.G1, inaczej niż `ProductFilters` na archiwum produktów).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

get_header();

// Wyróżniony (sticky) wpis renderujemy TYLKO na pierwszej stronie — WP i tak
// wyklucza sticky z wyników na kolejnych stronach głównego zapytania, ale
// nie chcemy nawet próbować budować dla niego osobnego bloku tam, gdzie WP
// mógłby (w innej konfiguracji) go jeszcze zwrócić.
$featured_id = ( ! is_paged() ) ? Blog::featured_post_id() : 0;
?>
<main class="wrap page-main">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Qutlet', 'qutlet-theme' ); ?></a><span class="sep">/</span><span class="current"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></span>
	</nav>

	<header class="page-head">
		<h1 class="page-title"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></h1>
		<p class="page-lead"><?php esc_html_e( 'Blog Qutlet o elektronice, która nie kończy na wysypisku: naprawy, testy sprzętu z drugiej ręki i twarde liczby o e-waste.', 'qutlet-theme' ); ?></p>
	</header>

	<?php Blog::render_category_chips( null ); ?>

	<?php if ( $featured_id > 0 && get_post( $featured_id ) instanceof WP_Post ) : ?>
		<?php
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
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( get_the_ID() === $featured_id ) {
					continue;
				}

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
		<p><?php esc_html_e( 'Brak wpisów do wyświetlenia.', 'qutlet-theme' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
