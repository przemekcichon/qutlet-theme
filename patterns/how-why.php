<?php
/**
 * Title: Dlaczego to ma sens (tekst + fakty)
 * Slug: qutlet-theme/how-why
 * Categories: qutlet
 * Description: Dwie kolumny — proza + trzy karty faktów z ikoną. Port .how-why/.how-why-facts (design/vanilla/jak-to-dziala.html). Ikony jako `core/spacer` + CSS `background-image` (P-11.5, ta sama poprawka co home-usp) — nie `wp:html`, patrz opis tamtego patternu.
 * Keywords: fakty, dlaczego, jak to działa
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"how-why"} -->
<div class="wp-block-group how-why">

<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Dlaczego to ma sens', 'qutlet-theme' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Zwrócony sprzęt traci na papierze status „nowego”, ale nie traci wartości użytkowej. Sklepy nie mogą go sprzedać w pełnej cenie, więc trafia do outletów — albo do utylizacji.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>
<?php
echo wp_kses_post(
	__( 'My robimy z tego prostą wymianę: <strong>Ty nie dopłacasz za folię i nienaruszone pudełko</strong>, a sprzęt pracuje dalej zamiast trafić na złom. Nie nazywamy tego misją — to po prostu rozsądniejszy sposób kupowania elektroniki, przy okazji lżejszy dla planety.', 'qutlet-theme' )
);
?>
</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"how-why-facts"} -->
<div class="wp-block-group how-why-facts">

<!-- wp:group {"className":"how-fact"} -->
<div class="wp-block-group how-fact">
<!-- wp:spacer {"height":"38px","className":"how-fact-icon how-fact-icon-cart"} -->
<div style="height:38px" aria-hidden="true" class="wp-block-spacer how-fact-icon how-fact-icon-cart"></div>
<!-- /wp:spacer -->
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Nawet -50% od ceny nówki', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Cena zależy od klasy stanu — im więcej śladów użytkowania, tym niższa.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"how-fact"} -->
<div class="wp-block-group how-fact">
<!-- wp:spacer {"height":"38px","className":"how-fact-icon how-fact-icon-shield"} -->
<div style="height:38px" aria-hidden="true" class="wp-block-spacer how-fact-icon how-fact-icon-shield"></div>
<!-- /wp:spacer -->
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Te same prawa co przy nowym', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Gwarancja, zwrot w 14 dni, reklamacje w naszym serwisie.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"how-fact"} -->
<div class="wp-block-group how-fact">
<!-- wp:spacer {"height":"38px","className":"how-fact-icon how-fact-icon-leaf"} -->
<div style="height:38px" aria-hidden="true" class="wp-block-spacer how-fact-icon how-fact-icon-leaf"></div>
<!-- /wp:spacer -->
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Jedno urządzenie mniej na złomie', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Każdy zakup przedłuża życie sprzętu, który już wyprodukowano.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
