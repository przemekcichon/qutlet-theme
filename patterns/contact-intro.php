<?php
/**
 * Title: Kontakt — dane + karta formularza
 * Slug: qutlet-theme/contact-intro
 * Categories: qutlet
 * Description: Siatka 2 kolumn — nagłówek z listą kontaktową i karta formularza (D-8.G3: treść karty to placeholder do podmiany na shortcode/blok wtyczki formularzy). Port .contact-layout (design/vanilla/kontakt.html).
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
<!-- wp:html -->
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 6L2 7"></path></svg>
<!-- /wp:html -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'E-mail', 'qutlet-theme' ); ?></strong><a href="mailto:kontakt@qutlet.pl">kontakt@qutlet.pl</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"contact-item"} -->
<div class="wp-block-group contact-item">
<!-- wp:html -->
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
<!-- /wp:html -->
<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Godziny odpowiedzi', 'qutlet-theme' ); ?></strong><?php esc_html_e( 'pon.–pt. 9:00–17:00', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"contact-item"} -->
<div class="wp-block-group contact-item">
<!-- wp:html -->
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
<!-- /wp:html -->
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
