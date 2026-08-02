<?php
/**
 * Title: CTA — zobacz strefę okazji
 * Slug: qutlet-theme/how-cta
 * Categories: qutlet
 * Description: Wyśrodkowany pasek CTA z linkiem do strefy okazji. Port .how-cta (design/vanilla/jak-to-dziala.html). Link do strony sklepu wpisany na sztywno (slug `strefa-okazji`, jak inne odnośniki między-stronowe w tej bibliotece patternów, np. card-grid-links) — przy zmianie sluga strony sklepu redaktor poprawia link ręcznie w tym miejscu.
 * Keywords: cta, strefa okazji
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"how-cta"} -->
<div class="wp-block-group how-cta">

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Większość ofert to pojedyncze egzemplarze — kto pierwszy, ten taniej.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/strefa-okazji/" class="hero-cta" style="margin-top:0">
	<?php esc_html_e( 'Zobacz strefę okazji', 'qutlet-theme' ); ?>
	<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
