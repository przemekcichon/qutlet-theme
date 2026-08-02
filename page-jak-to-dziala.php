<?php
/**
 * Qutlet — „Jak to działa?" (P-8.5, port design/vanilla/jak-to-dziala.html).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP wg slugu Strony
 * (`page-jak-to-dziala.php`). Strona idei/manifestu — bez breadcrumbs i bez
 * sidebaru `.help-nav` w prototypie (nie należy do sekcji Pomoc, patrz
 * `partials/help-nav.html` — nie wymienia tej strony). Treść jest chromem
 * szablonu (jak `home.php`, P-8.4), nie `the_content()`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap">
	<section class="how-hero">
		<span class="kicker kicker-lime"><?php esc_html_e( 'Idea Qutlet', 'qutlet-theme' ); ?></span>
		<h1><?php esc_html_e( 'Elektronika zasługuje na', 'qutlet-theme' ); ?> <span class="lime"><?php esc_html_e( 'drugą rundę.', 'qutlet-theme' ); ?></span></h1>
		<p class="lead">
			<?php
			echo wp_kses_post(
				__( 'Co roku tony sprawnego sprzętu lądują w magazynach zwrotów albo — gorzej — na wysypisku, bo ktoś otworzył pudełko i zmienił zdanie. My ten sprzęt <b>sprawdzamy, klasyfikujemy i sprzedajemy dalej</b> w outletowej cenie. Ty płacisz mniej za pełnowartościowy produkt, a jedno urządzenie mniej staje się elektrośmieciem.', 'qutlet-theme' )
			);
			?>
		</p>
	</section>

	<section class="how-section">
		<h2><?php esc_html_e( 'Od zwrotu do Twojej paczki', 'qutlet-theme' ); ?></h2>
		<p class="sub"><?php esc_html_e( 'Każdy egzemplarz przechodzi tę samą drogę — dlatego dokładnie wiesz, co kupujesz.', 'qutlet-theme' ); ?></p>
		<div class="how-steps">
			<div class="how-step">
				<span class="how-step-num">1</span>
				<h3><?php esc_html_e( 'Pozyskujemy sprzęt', 'qutlet-theme' ); ?></h3>
				<p><?php esc_html_e( 'Skupujemy zwroty konsumenckie ze sklepów oraz sprawdzony sprzęt z drugiej ręki. Żadnych anonimowych palet w ciemno.', 'qutlet-theme' ); ?></p>
			</div>
			<div class="how-step">
				<span class="how-step-num">2</span>
				<h3><?php esc_html_e( 'Serwis sprawdza każdą sztukę', 'qutlet-theme' ); ?></h3>
				<p><?php esc_html_e( 'Testujemy działanie, kompletność i stan wizualny. Egzemplarz dostaje klasę stanu od A do D — bez upiększania.', 'qutlet-theme' ); ?></p>
			</div>
			<div class="how-step">
				<span class="how-step-num">3</span>
				<h3><?php esc_html_e( 'Opisujemy konkretny egzemplarz', 'qutlet-theme' ); ?></h3>
				<p><?php esc_html_e( 'W ofercie widzisz dokładnie tę sztukę, którą dostaniesz: jej zdjęcia, jej rysy, jej zawartość pudełka. Jeden egzemplarz — jedna oferta.', 'qutlet-theme' ); ?></p>
			</div>
			<div class="how-step">
				<span class="how-step-num">4</span>
				<h3><?php esc_html_e( 'Wysyłamy z gwarancją', 'qutlet-theme' ); ?></h3>
				<p><?php esc_html_e( '12 miesięcy gwarancji sprzedawcy, 14 dni na zwrot i wysyłka w najbliższy dzień roboczy. Jak w zwykłym sklepie — tylko taniej.', 'qutlet-theme' ); ?></p>
			</div>
		</div>
	</section>

	<section class="how-section" id="klasy">
		<h2><?php esc_html_e( 'Klasy stanu — nasz wspólny język', 'qutlet-theme' ); ?></h2>
		<p class="sub"><?php esc_html_e( 'Zamiast marketingowych określeń w stylu „idealny" każdy produkt ma jedną z czterech klas. Klasa opisuje stan wizualny — technicznie sprawne jest wszystko poza klasą D.', 'qutlet-theme' ); ?></p>
		<table class="class-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Klasa', 'qutlet-theme' ); ?></th>
					<th><?php esc_html_e( 'Stan wizualny', 'qutlet-theme' ); ?></th>
					<th><?php esc_html_e( 'Charakterystyka', 'qutlet-theme' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr><td><span class="class-name"><span class="dot dot-a"></span><?php esc_html_e( 'Klasa A', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Jak nowy. Mikroryski.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Zwrot konsumencki. Oryginalne pudełko.', 'qutlet-theme' ); ?></td></tr>
				<tr><td><span class="class-name"><span class="dot dot-b"></span><?php esc_html_e( 'Klasa B', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Dobry. Widoczne ryski.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Używany dłużej. Pudełko zastępcze.', 'qutlet-theme' ); ?></td></tr>
				<tr><td><span class="class-name"><span class="dot dot-c"></span><?php esc_html_e( 'Klasa C', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Mocne ślady zużycia.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Sprawny technicznie, widoczna historia użytkowania.', 'qutlet-theme' ); ?></td></tr>
				<tr><td><span class="class-name"><span class="dot dot-d"></span><?php esc_html_e( 'Klasa D', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Na części.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Niesprawny technicznie.', 'qutlet-theme' ); ?></td></tr>
			</tbody>
		</table>
	</section>

	<section class="how-why">
		<div>
			<h2><?php esc_html_e( 'Dlaczego to ma sens', 'qutlet-theme' ); ?></h2>
			<p><?php esc_html_e( 'Zwrócony sprzęt traci na papierze status „nowego", ale nie traci wartości użytkowej. Sklepy nie mogą go sprzedać w pełnej cenie, więc trafia do outletów — albo do utylizacji.', 'qutlet-theme' ); ?></p>
			<p>
				<?php
				echo wp_kses_post(
					__( 'My robimy z tego prostą wymianę: <b>Ty nie dopłacasz za folię i nienaruszone pudełko</b>, a sprzęt pracuje dalej zamiast trafić na złom. Nie nazywamy tego misją — to po prostu rozsądniejszy sposób kupowania elektroniki, przy okazji lżejszy dla planety.', 'qutlet-theme' )
				);
				?>
			</p>
		</div>
		<div class="how-why-facts">
			<div class="how-fact">
				<span class="how-fact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6h15l-1.5 9h-12z"></path><path d="M6 6 5 3H2"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle></svg></span>
				<div><h4><?php esc_html_e( 'Nawet -50% od ceny nówki', 'qutlet-theme' ); ?></h4><p><?php esc_html_e( 'Cena zależy od klasy stanu — im więcej śladów użytkowania, tym niższa.', 'qutlet-theme' ); ?></p></div>
			</div>
			<div class="how-fact">
				<span class="how-fact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"></path></svg></span>
				<div><h4><?php esc_html_e( 'Te same prawa co przy nowym', 'qutlet-theme' ); ?></h4><p><?php esc_html_e( 'Gwarancja, zwrot w 14 dni, reklamacje w naszym serwisie.', 'qutlet-theme' ); ?></p></div>
			</div>
			<div class="how-fact">
				<span class="how-fact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path></svg></span>
				<div><h4><?php esc_html_e( 'Jedno urządzenie mniej na złomie', 'qutlet-theme' ); ?></h4><p><?php esc_html_e( 'Każdy zakup przedłuża życie sprzętu, który już wyprodukowano.', 'qutlet-theme' ); ?></p></div>
			</div>
		</div>
	</section>

	<section class="how-section" id="2eko">
		<h2><?php esc_html_e( '2eko: ekonomia + ekologia', 'qutlet-theme' ); ?></h2>
		<p class="sub"><?php esc_html_e( 'Nie kupujesz w outlecie dlatego, że musisz. Kupujesz, bo umiesz liczyć — a przy okazji robisz coś dobrego. W tej kolejności. I to jest w porządku.', 'qutlet-theme' ); ?></p>
		<div class="eko-grid">
			<div class="eko-card">
				<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5 5 19"></path><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg></span>
				<span class="eko-kicker"><?php esc_html_e( 'Eko #1 — Ekonomia', 'qutlet-theme' ); ?></span>
				<h3><?php esc_html_e( 'Sprytny zakup, nie kompromis', 'qutlet-theme' ); ?></h3>
				<p><?php esc_html_e( 'Ten sam model, ta sama jakość — tylko bez dopłaty za folię i status „nówki". Ekopragmatyk nie kupuje taniej, bo musi. Kupuje taniej, bo przepłacanie za karton to nieuwaga.', 'qutlet-theme' ); ?></p>
			</div>
			<div class="eko-card">
				<span class="eko-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path></svg></span>
				<span class="eko-kicker"><?php esc_html_e( 'Eko #2 — Ekologia', 'qutlet-theme' ); ?></span>
				<h3><?php esc_html_e( 'Bonus, który dostajesz gratis', 'qutlet-theme' ); ?></h3>
				<p><?php esc_html_e( 'Każdy taki zakup przedłuża życie sprzętu, który już wyprodukowano. Nie musisz się z tym obnosić — ekologia po prostu dzieje się przy okazji, za każdym razem.', 'qutlet-theme' ); ?></p>
			</div>
		</div>
	</section>

	<?php
	$shop_page_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
	$shop_url     = $shop_page_id > 0 ? (string) get_permalink( $shop_page_id ) : home_url( '/' );
	?>
	<section class="how-cta">
		<p><?php esc_html_e( 'Większość ofert to pojedyncze egzemplarze — kto pierwszy, ten taniej.', 'qutlet-theme' ); ?></p>
		<a href="<?php echo esc_url( $shop_url ); ?>" class="hero-cta" style="margin-top: 0;">
			<?php esc_html_e( 'Zobacz strefę okazji', 'qutlet-theme' ); ?>
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
		</a>
	</section>
</main>
<?php
get_footer();
