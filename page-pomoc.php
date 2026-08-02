<?php
/**
 * Qutlet — Centrum pomocy (P-8.5 → P-11.2, port design/vanilla/pomoc.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-pomoc.php`, `wp-includes/template.php` `page_template_hierarchy()`)
 * — zero konfiguracji w adminie. Od P-11.2 treść (nagłówek + szybkie linki +
 * karty) jest PRAWDZIWĄ treścią blokową Strony (`the_content()`, patterny
 * `qutlet-theme/help-quick-links` + `qutlet-theme/card-grid-links`, P-11.1) —
 * breadcrumb i sidebar `.help-nav` zostają chromem szablonu (nawigacja, nie
 * proza do redakcji, D-8.5.1/P-11.2).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Help\Help;

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php esc_html_e( 'Pomoc', 'qutlet-theme' ); ?></span>
	</nav>

	<div class="help-layout">
		<?php Help::render_help_nav(); ?>

		<div>
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</main>
<?php
get_footer();
