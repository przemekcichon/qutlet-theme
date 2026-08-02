<?php
/**
 * Title: Dlaczego to ma sens (tekst + fakty)
 * Slug: qutlet-theme/how-why
 * Categories: qutlet
 * Description: Dwie kolumny — proza + trzy karty faktów z ikoną. Port .how-why/.how-why-facts (design/vanilla/jak-to-dziala.html).
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
<!-- wp:html -->
<span class="how-fact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6h15l-1.5 9h-12z"></path><path d="M6 6 5 3H2"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle></svg></span>
<!-- /wp:html -->
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
<!-- wp:html -->
<span class="how-fact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"></path></svg></span>
<!-- /wp:html -->
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
<!-- wp:html -->
<span class="how-fact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path></svg></span>
<!-- /wp:html -->
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
