<?php
/**
 * Title: Siatka USP — strona główna
 * Slug: qutlet-theme/home-usp
 * Categories: qutlet
 * Description: Cztery karty „dlaczego Qutlet" (jeden egzemplarz, cena bez dopłaty za nówkę, mniej e-waste, szybka wysyłka). Port .usp-grid (design/vanilla/index.html). Ikony jako `core/spacer` + CSS `background-image` (P-11.5, zgłoszenie użytkownika) — NIE `wp:html` (poprzednia wersja): podgląd bloku Custom HTML renderuje się w edytorze w odizolowanym `<iframe sandbox>` bez `allow-same-origin`, więc CSS motywu tam nie dociera (ikona kurczyła się do ~17px zamiast 38px). Pusty `wp:group` jako alternatywa pokazuje w edytorze placeholder „wybierz layout" (sprawdzone) — `core/spacer` (blok projektowo pusty) nie ma tego problemu.
 * Keywords: usp, strona główna
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"wrap"} -->
<section class="wp-block-group wrap">
<!-- wp:group {"className":"usp-grid"} -->
<div class="wp-block-group usp-grid">

<!-- wp:group {"className":"usp-card"} -->
<div class="wp-block-group usp-card">
<!-- wp:spacer {"height":"38px","className":"usp-icon usp-icon-package"} -->
<div style="height:38px" aria-hidden="true" class="wp-block-spacer usp-icon usp-icon-package"></div>
<!-- /wp:spacer -->
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Jeden egzemplarz, jasny opis', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Każdy produkt to konkretna sztuka z opisem stanu i klasą jakości.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"usp-card"} -->
<div class="wp-block-group usp-card">
<!-- wp:spacer {"height":"38px","className":"usp-icon usp-icon-refresh"} -->
<div style="height:38px" aria-hidden="true" class="wp-block-spacer usp-icon usp-icon-refresh"></div>
<!-- /wp:spacer -->
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Nie dopłacasz za opakowanie', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Niższa cena, bo to zwrot — pełna wartość sprzętu, bez ceny „za nówkę”.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"usp-card"} -->
<div class="wp-block-group usp-card">
<!-- wp:spacer {"height":"38px","className":"usp-icon usp-icon-leaf"} -->
<div style="height:38px" aria-hidden="true" class="wp-block-spacer usp-icon usp-icon-leaf"></div>
<!-- /wp:spacer -->
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Mniej e-waste', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Kupując używane, przedłużasz życie sprzętu i ograniczasz elektrośmieci.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"usp-card"} -->
<div class="wp-block-group usp-card">
<!-- wp:spacer {"height":"38px","className":"usp-icon usp-icon-truck"} -->
<div style="height:38px" aria-hidden="true" class="wp-block-spacer usp-icon usp-icon-truck"></div>
<!-- /wp:spacer -->
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Wysyłka w 1 dzień roboczy', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Zamówienia kompletujemy i wysyłamy w najbliższy dzień roboczy.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
