<?php
/**
 * Qutlet — „Jak to działa?" (P-8.5 → P-11.2, port design/vanilla/jak-to-dziala.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-jak-to-dziala.php`). Strona idei/manifestu — bez breadcrumbs i bez
 * sidebaru `.help-nav` w prototypie (nie należy do sekcji Pomoc, patrz
 * `partials/help-nav.html` — nie wymienia tej strony). Od P-11.2 cała treść
 * jest PRAWDZIWĄ treścią blokową Strony (`the_content()`, patterny
 * `qutlet-theme/hero-idea`, `how-steps`, `class-table`, `card-grid-eko`,
 * `how-why`, `how-cta` — P-11.1/P-11.2), nie chromem szablonu.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
