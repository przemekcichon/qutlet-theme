<?php
/**
 * Qutlet — Kontakt (P-8.5 → P-11.2, port design/vanilla/kontakt.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-kontakt.php`). Od P-11.2 cała treść (nagłówek, lista kontaktowa,
 * karta formularza) jest PRAWDZIWĄ treścią blokową Strony (`the_content()`,
 * pattern `qutlet-theme/contact-intro`), nie chromem szablonu — breadcrumb i
 * sidebar `.help-nav` zostają chromem (nawigacja, D-8.5.1). Punkt wpięcia
 * wtyczki 3rd-party (D-8.G3/D-11.G4) jest teraz WEWNĄTRZ treści Strony
 * (akapit-placeholder w karcie `.contact-form-card`, część `the_content()`),
 * nie osobnym wywołaniem w PHP.
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
		<a href="<?php echo esc_url( Help::page_url( 'pomoc' ) ); ?>"><?php esc_html_e( 'Pomoc', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php esc_html_e( 'Kontakt', 'qutlet-theme' ); ?></span>
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
