<?php
/**
 * Title: Hero — baner newslettera
 * Slug: qutlet-theme/hero-newsletter-band
 * Categories: qutlet
 * Description: Baner zapisu do newslettera (gradientowa karta) z nagłówkiem, lead-em i formularzem e-mail. Port .nlband (design/vanilla/partials/footer.html).
 * Keywords: hero, newsletter, baner
 * Viewport Width: 1240
 *
 * UWAGA: BEZ wewnętrznego `.wrap` (inaczej niż w parts/footer.html, gdzie
 * `.nlband` żyje POZA `<main class="wrap">`) — ten pattern trafia do
 * `the_content()`, który każdy page-*.php/single.php już renderuje WEWNĄTRZ
 * `<main class="wrap">`; dublowanie `.wrap` podwoiłoby padding poziomy
 * (recenzja P-11.1, zweryfikowane runtime).
 *
 * `.nlband-form` (P-23.6): jak w parts/footer.html — zwykły formularz GET,
 * `action="/newsletter/"`, bez własnego zapisu; realny zapis do MailerLite
 * robi formularz Gravity Forms ID 2 na Stronie Newsletter, który czyta
 * parametr `?email=…` przez natywne GF „Allow field to be populated
 * dynamically" (Parameter Name `email`).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"nlband"} -->
<div class="wp-block-group nlband">
<!-- wp:group {"className":"nlband-inner"} -->
<div class="wp-block-group nlband-inner">

<!-- wp:group -->
<div class="wp-block-group">

<!-- wp:paragraph {"className":"kicker kicker-lime"} -->
<p class="kicker kicker-lime"><?php esc_html_e( 'Newsletter Ekołowców', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"nlband-title"} -->
<h2 class="wp-block-heading nlband-title"><?php esc_html_e( 'Najlepsze sztuki', 'qutlet-theme' ); ?> <span class="lime"><?php esc_html_e( 'znikają pierwsze', 'qutlet-theme' ); ?></span>.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nlband-lead"} -->
<p class="nlband-lead"><?php esc_html_e( 'Nowości, zamknięte okazje i sprzęt z górnej półki — zanim zobaczą je wszyscy. Bo mądry zakup to nie kwestia budżetu, tylko podejścia.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:html -->
<form class="nlband-form" data-nlband-form action="/newsletter/" method="get">
	<div class="nlband-input-row">
		<input type="email" name="email" required placeholder="<?php echo esc_attr__( 'Twój adres e-mail', 'qutlet-theme' ); ?>" aria-label="<?php echo esc_attr__( 'Adres e-mail', 'qutlet-theme' ); ?>">
		<button type="submit" class="btn btn-lime"><?php echo esc_html__( 'Dołączam', 'qutlet-theme' ); ?></button>
	</div>
	<span class="nlband-note"><?php echo esc_html__( 'Zero spamu. Wypisujesz się jednym kliknięciem.', 'qutlet-theme' ); ?></span>
</form>
<!-- /wp:html -->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
