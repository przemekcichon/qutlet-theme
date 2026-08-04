<?php
/**
 * Title: Kontakt — dane + karta formularza
 * Slug: qutlet-theme/contact-intro
 * Categories: qutlet
 * Description: Siatka 2 kolumn — nagłówek z listą kontaktową i karta formularza (D-8.G3: treść karty to placeholder do podmiany na shortcode/blok wtyczki formularzy). Port .contact-layout (design/vanilla/kontakt.html). Ikony jako `core/spacer` + CSS `background-image`, klasa `.contact-item-icon` (P-11.5, ta sama poprawka co home-usp — nowa klasa, bo oryginalny CSS celował gołe `<svg>` selektorem elementowym `.contact-item svg`, niemożliwym do odtworzenia bez zagnieżdżonego elementu) — nie `wp:html`, patrz opis home-usp.
 * Keywords: kontakt, formularz
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"contact-layout"} -->
<div class="wp-block-group contact-layout">

<!-- wp:group {"className":"contact-head"} -->
<div class="wp-block-group contact-head">
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e( 'Kontakt', 'qutlet-theme' ); ?></h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Pytanie o stan konkretnego egzemplarza, zwrot albo zamówienie? Napisz — odpowiadamy w dzień roboczy.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"contact-list"} -->
<div class="wp-block-group contact-list">

<!-- wp:group {"className":"contact-item"} -->
<div class="wp-block-group contact-item">
<!-- wp:spacer {"height":"20px","className":"contact-item-icon contact-item-icon-mail"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer contact-item-icon contact-item-icon-mail"></div>
<!-- /wp:spacer -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'E-mail', 'qutlet-theme' ); ?></strong><a href="mailto:kontakt@qutlet.pl">kontakt@qutlet.pl</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"contact-item"} -->
<div class="wp-block-group contact-item">
<!-- wp:spacer {"height":"20px","className":"contact-item-icon contact-item-icon-clock"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer contact-item-icon contact-item-icon-clock"></div>
<!-- /wp:spacer -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Godziny odpowiedzi', 'qutlet-theme' ); ?></strong><?php esc_html_e( 'pon.–pt. 9:00–17:00', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"contact-item"} -->
<div class="wp-block-group contact-item">
<!-- wp:spacer {"height":"20px","className":"contact-item-icon contact-item-icon-message"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer contact-item-icon contact-item-icon-message"></div>
<!-- /wp:spacer -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Allegro', 'qutlet-theme' ); ?></strong><a href="https://allegro.pl/uzytkownik/Qutlet" target="_blank" rel="noopener"><?php esc_html_e( 'Wiadomość przez Allegro', 'qutlet-theme' ); ?></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"form-card contact-form-card"} -->
<div class="wp-block-group form-card contact-form-card">
<!-- wp:heading {"level":3,"className":"step-title"} -->
<h3 class="wp-block-heading step-title"><span class="step-num">✉</span><?php esc_html_e( 'Napisz do nas', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><em><?php esc_html_e( 'Miejsce na formularz kontaktowy.', 'qutlet-theme' ); ?></em> <?php esc_html_e( 'Zainstaluj i skonfiguruj wtyczkę formularzy (np. Contact Form 7 / WPForms), a następnie wklej tutaj jej shortcode lub blok — zastąpi ten tekst. Motyw tylko osadza formularz w tym miejscu — logikę wysyłki dostarcza wtyczka.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
