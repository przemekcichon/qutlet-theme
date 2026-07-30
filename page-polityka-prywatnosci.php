<?php
/**
 * Qutlet — Polityka prywatności (P-8.5, port design/vanilla/polityka-prywatnosci.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-polityka-prywatnosci.php`) — patrz `page-regulamin.php` po pełne
 * uzasadnienie wzorca (`the_content()` + `Help::extract_legal_headings()`),
 * identyczny tu 1:1.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Help\Help;

defined( 'ABSPATH' ) || exit;

get_header();

$headings = array();
?>
<main class="wrap">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<a href="<?php echo esc_url( Help::page_url( 'pomoc' ) ); ?>"><?php esc_html_e( 'Pomoc', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php esc_html_e( 'Polityka prywatności', 'qutlet-theme' ); ?></span>
	</nav>

	<div class="help-layout has-toc">
		<?php Help::render_help_nav(); ?>

		<article class="terms-article">
			<?php
			while ( have_posts() ) :
				the_post();
				$headings = Help::extract_legal_headings( get_the_content() );
				the_content();
			endwhile;
			?>
		</article>

		<?php if ( ! empty( $headings ) ) : ?>
			<aside class="toc">
				<h4><?php esc_html_e( 'Spis treści', 'qutlet-theme' ); ?></h4>
				<div class="toc-list">
					<?php foreach ( $headings as $heading ) : ?>
						<a href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['text'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</aside>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
