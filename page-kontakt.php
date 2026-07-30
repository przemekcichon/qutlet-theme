<?php
/**
 * Qutlet — Kontakt (P-8.5, port design/vanilla/kontakt.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-kontakt.php`). Kolumna z danymi kontaktowymi jest chromem szablonu
 * (jak `home.php`, P-8.4); JEDYNY punkt osadzenia wtyczki 3rd-party (D-8.G3)
 * to `the_content()` wewnątrz `.contact-form-card` — treść Strony (obecnie
 * placeholder, patrz handoff) ma być podmieniona przez admina na
 * shortcode/blok wtyczki formularzy (CF7/WPForms) po jej wyborze i instalacji.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Help\Help;

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<a href="<?php echo esc_url( Help::page_url( 'pomoc' ) ); ?>"><?php esc_html_e( 'Pomoc', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php esc_html_e( 'Kontakt', 'qutlet-theme' ); ?></span>
	</nav>

	<div class="help-layout">
		<?php Help::render_help_nav(); ?>

		<div class="contact-layout">
			<div class="contact-head">
				<h1><?php esc_html_e( 'Kontakt', 'qutlet-theme' ); ?></h1>
				<p><?php esc_html_e( 'Pytanie o stan konkretnego egzemplarza, zwrot albo zamówienie? Napisz — odpowiadamy w dzień roboczy.', 'qutlet-theme' ); ?></p>
				<div class="contact-list">
					<div class="contact-item">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 6L2 7"></path></svg>
						<div><b><?php esc_html_e( 'E-mail', 'qutlet-theme' ); ?></b><a href="mailto:kontakt@qutlet.pl">kontakt@qutlet.pl</a></div>
					</div>
					<div class="contact-item">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
						<div><b><?php esc_html_e( 'Godziny odpowiedzi', 'qutlet-theme' ); ?></b><span><?php esc_html_e( 'pon.–pt. 9:00–17:00', 'qutlet-theme' ); ?></span></div>
					</div>
					<div class="contact-item">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
						<div><b><?php esc_html_e( 'Allegro', 'qutlet-theme' ); ?></b><a href="https://allegro.pl/uzytkownik/Qutlet" target="_blank" rel="noopener"><?php esc_html_e( 'Wiadomość przez Allegro', 'qutlet-theme' ); ?></a></div>
					</div>
				</div>
			</div>

			<div class="form-card contact-form-card">
				<h3 class="step-title"><span class="step-num">✉</span><?php esc_html_e( 'Napisz do nas', 'qutlet-theme' ); ?></h3>
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
