<?php
/**
 * Title: Hero — idea (plum)
 * Slug: qutlet-theme/hero-idea
 * Categories: qutlet
 * Description: Ciemny hero band (tło plum) z kickerem, nagłówkiem i lead-em. Port .how-hero (design/vanilla/jak-to-dziala.html).
 * Keywords: hero, jak to działa, plum
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"how-hero"} -->
<div class="wp-block-group how-hero">

<!-- wp:paragraph {"className":"kicker kicker-lime"} -->
<p class="kicker kicker-lime"><?php esc_html_e( 'Idea Qutlet', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e( 'Elektronika zasługuje na', 'qutlet-theme' ); ?> <span class="lime"><?php esc_html_e( 'drugą rundę.', 'qutlet-theme' ); ?></span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lead"} -->
<p class="lead">
<?php
echo wp_kses_post(
	__( 'Co roku tony sprawnego sprzętu lądują w magazynach zwrotów albo — gorzej — na wysypisku, bo ktoś otworzył pudełko i zmienił zdanie. My ten sprzęt <strong>sprawdzamy, klasyfikujemy i sprzedajemy dalej</strong> w outletowej cenie. Ty płacisz mniej za pełnowartościowy produkt, a jedno urządzenie mniej staje się elektrośmieciem.', 'qutlet-theme' )
);
?>
</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
