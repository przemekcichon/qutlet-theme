<?php
/**
 * Title: Newsletter — nagłówek + karta zapisu
 * Slug: qutlet-theme/newsletter-intro
 * Categories: qutlet
 * Description: Siatka 2 kolumn — lead „Klub ekopragmatyków” i karta zapisu (D-8.G3: treść karty to placeholder do podmiany na shortcode/blok wtyczki ESP). Port .nl-grid (design/vanilla/newsletter.html).
 * Keywords: newsletter, zapis
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"nl-grid"} -->
<div class="wp-block-group nl-grid">

<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:paragraph {"className":"kicker"} -->
<p class="kicker"><?php esc_html_e( 'Newsletter Ekołowców', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e( 'Klub', 'qutlet-theme' ); ?> <span class="accent"><?php esc_html_e( 'ekopragmatyków.', 'qutlet-theme' ); ?></span></h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"nl-lead"} -->
<p class="nl-lead">
<?php
echo wp_kses_post(
	__( 'Ludzi, którzy kupują z głową: <strong>płacą mniej i mniej marnują</strong> — niezależnie od tego, ile mają na koncie. Pełnowartościowy sprzęt bez przepłacania to nie oszczędzanie z konieczności. To po prostu spryt.', 'qutlet-theme' )
);
?>
</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"nl-form-card"} -->
<div class="wp-block-group nl-form-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Wskakujesz na pokład', 'qutlet-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"sub"} -->
<p class="sub"><?php esc_html_e( 'Raz w tygodniu. Same dropy i okazje warte Twojej uwagi.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em><?php esc_html_e( 'Miejsce na formularz zapisu do newslettera.', 'qutlet-theme' ); ?></em> <?php esc_html_e( 'Zainstaluj i skonfiguruj wtyczkę ESP (np. MailPoet / Mailchimp), a następnie wklej tutaj jej shortcode lub blok — zastąpi ten tekst. Motyw tylko osadza formularz w tym miejscu — logikę zapisu i wysyłki dostarcza wtyczka.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
