<?php
/**
 * Qutlet — Newsletter Ekołowców (P-8.5 → P-11.2, port design/vanilla/newsletter.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-newsletter.php`). Od P-11.2 cała treść (lead, karta zapisu, karty
 * eko, perki, cytat) jest PRAWDZIWĄ treścią blokową Strony (`the_content()`,
 * patterny `qutlet-theme/newsletter-intro`, `eko-grid-newsletter`, `perks3`,
 * `quote-band` — P-11.1/P-11.2), nie chromem szablonu. Punkt wpięcia wtyczki
 * 3rd-party (D-8.G3/D-11.G4) jest teraz WEWNĄTRZ treści Strony (akapit-
 * -placeholder w karcie `.nl-form-card`, część `the_content()`), nie osobnym
 * wywołaniem w PHP. Baner `.nlband` w stopce jest ukrywany NA tej stronie
 * (`Help::filter_body_class()`), żeby nie duplikować tej samej oferty.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap nl-page">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php esc_html_e( 'Newsletter Ekołowców', 'qutlet-theme' ); ?></span>
	</nav>

	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
