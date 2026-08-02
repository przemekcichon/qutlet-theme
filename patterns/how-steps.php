<?php
/**
 * Title: Kroki — od zwrotu do paczki
 * Slug: qutlet-theme/how-steps
 * Categories: qutlet
 * Description: Siatka 4 numerowanych kroków procesu. Port .how-steps (design/vanilla/jak-to-dziala.html).
 * Keywords: kroki, proces, jak to działa
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"how-steps"} -->
<div class="wp-block-group how-steps">

<!-- wp:group {"className":"how-step"} -->
<div class="wp-block-group how-step">
<!-- wp:paragraph {"className":"how-step-num"} -->
<p class="how-step-num">1</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Pozyskujemy sprzęt', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Skupujemy zwroty konsumenckie ze sklepów oraz sprawdzony sprzęt z drugiej ręki. Żadnych anonimowych palet w ciemno.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"how-step"} -->
<div class="wp-block-group how-step">
<!-- wp:paragraph {"className":"how-step-num"} -->
<p class="how-step-num">2</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Serwis sprawdza każdą sztukę', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Testujemy działanie, kompletność i stan wizualny. Egzemplarz dostaje klasę stanu od A do D — bez upiększania.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"how-step"} -->
<div class="wp-block-group how-step">
<!-- wp:paragraph {"className":"how-step-num"} -->
<p class="how-step-num">3</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Opisujemy konkretny egzemplarz', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'W ofercie widzisz dokładnie tę sztukę, którą dostaniesz: jej zdjęcia, jej rysy, jej zawartość pudełka. Jeden egzemplarz — jedna oferta.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"how-step"} -->
<div class="wp-block-group how-step">
<!-- wp:paragraph {"className":"how-step-num"} -->
<p class="how-step-num">4</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Wysyłamy z gwarancją', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( '12 miesięcy gwarancji sprzedawcy, 14 dni na zwrot i wysyłka w najbliższy dzień roboczy. Jak w zwykłym sklepie — tylko taniej.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
