<?php
/**
 * Qutlet — Centrum pomocy (P-8.5, port design/vanilla/pomoc.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-pomoc.php`, `wp-includes/template.php` `page_template_hierarchy()`)
 * — zero konfiguracji w adminie (inaczej niż „Template Name" wybierany
 * ręcznie w Atrybutach strony). Treść (nagłówek + szybkie linki + karty)
 * jest chromem szablonu, NIE `the_content()` — ten sam wzorzec co
 * `home.php` (hero bloga zakodowany wprost w PHP, P-8.4): to strona
 * nawigacyjna sekcji, nie prozy do swobodnej edycji.
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
		<span class="current"><?php esc_html_e( 'Pomoc', 'qutlet-theme' ); ?></span>
	</nav>

	<div class="help-layout">
		<?php Help::render_help_nav(); ?>

		<div>
			<header class="help-head">
				<h1><?php esc_html_e( 'Pomoc', 'qutlet-theme' ); ?></h1>
				<p><?php esc_html_e( 'Zwroty, gwarancja, dostawa i dokumenty sklepu — wszystko w jednym miejscu. Nie znajdziesz odpowiedzi? Napisz do nas, odpowiadamy w dzień roboczy.', 'qutlet-theme' ); ?></p>
			</header>

			<div class="help-sec-label"><?php esc_html_e( 'Najczęstsze tematy', 'qutlet-theme' ); ?></div>
			<div class="help-quick">
				<a href="<?php echo esc_url( Help::page_url( 'regulamin' ) . '#s6' ); ?>"><?php esc_html_e( 'Zwrot w 14 dni', 'qutlet-theme' ); ?></a>
				<a href="<?php echo esc_url( Help::page_url( 'regulamin' ) . '#s7' ); ?>"><?php esc_html_e( 'Gwarancja i reklamacje', 'qutlet-theme' ); ?></a>
				<a href="<?php echo esc_url( Help::page_url( 'regulamin' ) . '#s5' ); ?>"><?php esc_html_e( 'Dostawa', 'qutlet-theme' ); ?></a>
				<a href="<?php echo esc_url( Help::page_url( 'regulamin' ) . '#s4' ); ?>"><?php esc_html_e( 'Ceny i płatności', 'qutlet-theme' ); ?></a>
				<a href="<?php echo esc_url( Help::page_url( 'jak-to-dziala' ) . '#klasy' ); ?>"><?php esc_html_e( 'Klasy produktów A–D', 'qutlet-theme' ); ?></a>
				<a href="<?php echo esc_url( Help::page_url( 'jak-to-dziala' ) ); ?>"><?php esc_html_e( 'Skąd mamy sprzęt?', 'qutlet-theme' ); ?></a>
			</div>

			<div class="help-sec-label"><?php esc_html_e( 'Dokumenty i kontakt', 'qutlet-theme' ); ?></div>
			<div class="help-cards">
				<a href="<?php echo esc_url( Help::page_url( 'regulamin' ) ); ?>" class="help-card">
					<b><?php esc_html_e( 'Regulamin', 'qutlet-theme' ); ?></b>
					<span><?php esc_html_e( 'Zasady zakupów: zamówienia, płatności, dostawa, zwroty i reklamacje.', 'qutlet-theme' ); ?></span>
					<span class="go"><?php esc_html_e( 'Czytaj regulamin', 'qutlet-theme' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
					</span>
				</a>
				<a href="<?php echo esc_url( Help::page_url( 'polityka-prywatnosci' ) ); ?>" class="help-card">
					<b><?php esc_html_e( 'Polityka prywatności', 'qutlet-theme' ); ?></b>
					<span><?php esc_html_e( 'Jakie dane zbieramy, po co i jakie masz prawa (RODO).', 'qutlet-theme' ); ?></span>
					<span class="go"><?php esc_html_e( 'Czytaj politykę', 'qutlet-theme' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
					</span>
				</a>
				<a href="<?php echo esc_url( Help::page_url( 'polityka-cookies' ) ); ?>" class="help-card">
					<b><?php esc_html_e( 'Polityka cookies', 'qutlet-theme' ); ?></b>
					<span><?php esc_html_e( 'Pełny wykaz cookies i trackerów z celami oraz okresami przechowywania.', 'qutlet-theme' ); ?></span>
					<span class="go"><?php esc_html_e( 'Zobacz wykaz', 'qutlet-theme' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
					</span>
				</a>
				<a href="<?php echo esc_url( Help::page_url( 'kontakt' ) ); ?>" class="help-card">
					<b><?php esc_html_e( 'Kontakt', 'qutlet-theme' ); ?></b>
					<span><?php esc_html_e( 'Formularz, e-mail i godziny odpowiedzi. Pytania o konkretny egzemplarz mile widziane.', 'qutlet-theme' ); ?></span>
					<span class="go"><?php esc_html_e( 'Napisz do nas', 'qutlet-theme' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
					</span>
				</a>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
