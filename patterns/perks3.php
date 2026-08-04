<?php
/**
 * Title: Siatka 3 perków — Ekołowiec
 * Slug: qutlet-theme/perks3
 * Categories: qutlet
 * Description: Sekcja z nagłówkiem i siatką 3 kart korzyści. Port .perks3-section/.perks3 (design/vanilla/newsletter.html). Ikony jako `core/spacer` + CSS `background-image` (P-11.5, ta sama poprawka co home-usp) — nie `wp:html`, patrz opis tamtego patternu.
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
<!-- wp:spacer {"height":"40px","className":"perk3-icon perk3-icon-bell"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer perk3-icon perk3-icon-bell"></div>
<!-- /wp:spacer -->
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
<!-- wp:spacer {"height":"40px","className":"perk3-icon perk3-icon-lock"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer perk3-icon perk3-icon-lock"></div>
<!-- /wp:spacer -->
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
<!-- wp:spacer {"height":"40px","className":"perk3-icon perk3-icon-bolt"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer perk3-icon perk3-icon-bolt"></div>
<!-- /wp:spacer -->
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
