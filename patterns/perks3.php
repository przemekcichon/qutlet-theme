<?php
/**
 * Title: Siatka 3 perków — Ekołowiec
 * Slug: qutlet-theme/perks3
 * Categories: qutlet
 * Description: Sekcja z nagłówkiem i siatką 3 kart korzyści. Port .perks3-section/.perks3 (design/vanilla/newsletter.html).
 * Keywords: perki, korzyści, newsletter
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"perks3-section"} -->
<div class="wp-block-group perks3-section">
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Co dostajesz jako Ekołowca', 'qutlet-theme' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"perks3"} -->
<div class="wp-block-group perks3">

<!-- wp:group {"className":"perk3"} -->
<div class="wp-block-group perk3">
<!-- wp:html -->
<span class="perk3-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg></span>
<!-- /wp:html -->
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Świeże dropy', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Nowe sztuki lądują na Twojej skrzynce, zanim rozejdą się z magazynu.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"perk3"} -->
<div class="wp-block-group perk3">
<!-- wp:html -->
<span class="perk3-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
<!-- /wp:html -->
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Zamknięte okazje', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Ceny i kody dostępne tylko dla subskrybentów newslettera.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"perk3"} -->
<div class="wp-block-group perk3">
<!-- wp:html -->
<span class="perk3-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"></path></svg></span>
<!-- /wp:html -->
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Tech bez ściemy', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Krótkie newsy i porady, które naprawdę pomogą Ci kupić mądrze.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
