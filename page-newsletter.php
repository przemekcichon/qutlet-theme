<?php
/**
 * Qutlet — Newsletter Ekołowców (P-8.5, port design/vanilla/newsletter.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-newsletter.php`). Marketingowe treści (lead, karty eko, perki,
 * cytat) są chromem szablonu (jak `home.php`, P-8.4); JEDYNY punkt osadzenia
 * wtyczki 3rd-party (D-8.G3) to `the_content()` wewnątrz `.nl-form-card` —
 * treść Strony (placeholder, patrz handoff) ma być podmieniona na
 * shortcode/blok wtyczki ESP (MailPoet/Mailchimp) po jej wyborze i
 * konfiguracji. Baner `.nlband` w stopce jest ukrywany NA tej stronie
 * (`Help::filter_body_class()`, żeby nie duplikować tej samej oferty.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap nl-page">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php esc_html_e( 'Newsletter Ekołowców', 'qutlet-theme' ); ?></span>
	</nav>

	<div class="nl-grid">
		<div>
			<span class="kicker"><?php esc_html_e( 'Newsletter Ekołowców', 'qutlet-theme' ); ?></span>
			<h1><?php esc_html_e( 'Klub', 'qutlet-theme' ); ?> <span class="accent"><?php esc_html_e( 'ekopragmatyków.', 'qutlet-theme' ); ?></span></h1>
			<p class="nl-lead">
				<?php
				echo wp_kses_post(
					__( 'Ludzi, którzy kupują z głową: <strong>płacą mniej i mniej marnują</strong> — niezależnie od tego, ile mają na koncie. Pełnowartościowy sprzęt bez przepłacania to nie oszczędzanie z konieczności. To po prostu spryt.', 'qutlet-theme' )
				);
				?>
			</p>
		</div>

		<div class="nl-form-card">
			<h3><?php esc_html_e( 'Wskakujesz na pokład', 'qutlet-theme' ); ?></h3>
			<p class="sub"><?php esc_html_e( 'Raz w tygodniu. Same dropy i okazje warte Twojej uwagi.', 'qutlet-theme' ); ?></p>
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>

	<div class="eko-grid">
		<div class="eko-card">
			<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5 5 19"></path><circle cx="7.5" cy="7.5" r="2.5"></circle><circle cx="16.5" cy="16.5" r="2.5"></circle></svg></span>
			<span class="eko-kicker"><?php esc_html_e( 'Eko #1 — Ekonomia', 'qutlet-theme' ); ?></span>
			<h3><?php esc_html_e( 'Pełna wartość, ułamek ceny', 'qutlet-theme' ); ?></h3>
			<p><?php esc_html_e( 'Płacisz za sprzęt, nie za karton i metkę „nówki". Ten sam model, ta sama jakość, nawet -50%. Dla ekopragmatyka to nie kompromis — to lepiej policzony zakup.', 'qutlet-theme' ); ?></p>
		</div>
		<div class="eko-card">
			<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path></svg></span>
			<span class="eko-kicker"><?php esc_html_e( 'Eko #2 — Ekologia', 'qutlet-theme' ); ?></span>
			<h3><?php esc_html_e( 'Mniej e-waste, realny ślad', 'qutlet-theme' ); ?></h3>
			<p><?php esc_html_e( 'Każdy kupiony egzemplarz to jeden mniej na wysypisku. Przedłużasz życie sprawnej elektroniki i realnie obniżasz swój ślad — bez wyrzeczeń i bez ściemy.', 'qutlet-theme' ); ?></p>
		</div>
	</div>

	<div class="perks3-section">
		<h2><?php esc_html_e( 'Co dostajesz jako Ekołowca', 'qutlet-theme' ); ?></h2>
		<div class="perks3">
			<div class="perk3">
				<span class="perk3-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg></span>
				<h4><?php esc_html_e( 'Świeże dropy', 'qutlet-theme' ); ?></h4>
				<p><?php esc_html_e( 'Nowe sztuki lądują na Twojej skrzynce, zanim rozejdą się z magazynu.', 'qutlet-theme' ); ?></p>
			</div>
			<div class="perk3">
				<span class="perk3-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
				<h4><?php esc_html_e( 'Zamknięte okazje', 'qutlet-theme' ); ?></h4>
				<p><?php esc_html_e( 'Ceny i kody dostępne tylko dla subskrybentów newslettera.', 'qutlet-theme' ); ?></p>
			</div>
			<div class="perk3">
				<span class="perk3-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"></path></svg></span>
				<h4><?php esc_html_e( 'Tech bez ściemy', 'qutlet-theme' ); ?></h4>
				<p><?php esc_html_e( 'Krótkie newsy i porady, które naprawdę pomogą Ci kupić mądrze.', 'qutlet-theme' ); ?></p>
			</div>
		</div>
	</div>

	<div class="quote-band">
		<blockquote><?php esc_html_e( 'Stać Cię na nówkę? Tym lepiej.', 'qutlet-theme' ); ?> <span class="lime"><?php esc_html_e( 'Ekopragmatyk', 'qutlet-theme' ); ?></span> <?php esc_html_e( 'i tak wybierze Qutlet — bo przepłacanie za karton to nie luksus, to nieuwaga.', 'qutlet-theme' ); ?></blockquote>
		<div class="attribution"><?php esc_html_e( 'Manifest Ekołowców', 'qutlet-theme' ); ?></div>
	</div>
</main>
<?php
get_footer();
