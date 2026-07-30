<?php
/**
 * Slice Help — strony pomocy + nawigacja sekcji (P-8.5), port
 * design/vanilla/pomoc.html, jak-to-dziala.html, kontakt.html,
 * newsletter.html, regulamin.html, polityka-prywatnosci.html,
 * polityka-cookies.html, partials/help-nav.html. Wszystkie siedem stron to
 * natywne WP Pages (D-1.5.1, treść P-1.5) — ta klasa TYLKO renderuje
 * (D-8.G1): rejestruje lokalizację menu „Pomoc" (treść samego menu WP
 * `pomoc` powstała już w P-1.5), buduje sidebar `.help-nav` z gotowego menu
 * i wyciąga spis treści z nagłówków `<h2>` w treści stron prawnych.
 *
 * Formularze (kontakt/newsletter, D-8.G3) NIE mają tu żadnej logiki —
 * szablony page-kontakt.php/page-newsletter.php osadzają `the_content()`
 * wewnątrz oryginalnego markupu karty formularza, więc wtyczka 3rd-party
 * (CF7/WPForms, MailPoet/Mailchimp — wybór i konfiguracja to handoff)
 * podmienia WYŁĄCZNIE treść Strony, nie szablon.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Help;

use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render + konfiguracja sekcji pomocy.
 */
final class Help {

	/**
	 * Nazwa lokalizacji menu WP renderowanej jako sidebar `.help-nav`.
	 * Menu o tej samej nazwie (`pomoc`) istnieje już w treści (P-1.5) —
	 * rejestracja lokalizacji TYLKO wpina je w render (WordPress dopasowuje
	 * menu do lokalizacji o tej samej nazwie automatycznie, jeśli menu nie
	 * ma jeszcze przypisanej innej lokalizacji).
	 */
	private const MENU_LOCATION = 'pomoc';

	/**
	 * Rejestruje boot slice'a.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'after_setup_theme', array( self::class, 'register_menu_location' ) );
		add_filter( 'body_class', array( self::class, 'filter_body_class' ) );
	}

	/**
	 * Rejestruje lokalizację menu `pomoc` (sidebar sekcji pomocy).
	 *
	 * @return void
	 */
	public static function register_menu_location(): void {
		register_nav_menu( self::MENU_LOCATION, __( 'Pomoc (sidebar sekcji pomocy)', 'qutlet-theme' ) );
	}

	/**
	 * Dokłada klasę body na Stronie „Newsletter" — ukrywa globalny baner
	 * `.nlband` (parts/footer.html), żeby nie duplikować tej samej oferty
	 * na stronie, która JEST tą ofertą (port `body[data-page="newsletter"]
	 * .nlband { display:none }`, prototyp nie ma odpowiednika `body_class()`,
	 * więc D-8.G1 wymaga tłumaczenia atrybutu statycznego HTML na filtr WP).
	 *
	 * @param array<int, string> $classes Klasy `<body>` (WP core).
	 * @return array<int, string>
	 */
	public static function filter_body_class( array $classes ): array {
		if ( is_page( 'newsletter' ) ) {
			$classes[] = 'qt-hide-nlband';
		}

		return $classes;
	}

	/**
	 * Renderuje sidebar `.help-nav` z menu WP `pomoc` — podświetla aktywną
	 * pozycję porównując `object_id` pozycji menu z bieżącą Stroną (WP-owy
	 * odpowiednik `body[data-page] .help-nav a[data-help]` z prototypu).
	 * Brak menu (niezmapowana lokalizacja / usunięte menu) → nic nie
	 * renderuje, strona nie wywraca się bez sidebaru.
	 *
	 * @return void
	 */
	public static function render_help_nav(): void {
		$items = wp_get_nav_menu_items( self::MENU_LOCATION );

		if ( ! is_array( $items ) || empty( $items ) ) {
			return;
		}

		$current_id = get_queried_object_id();

		echo '<aside class="help-nav"><h4>' . esc_html__( 'Pomoc', 'qutlet-theme' ) . '</h4>';

		foreach ( $items as $item ) {
			$is_active = $current_id > 0 && (int) $item->object_id === $current_id;

			printf(
				'<a href="%1$s"%2$s>%3$s</a>',
				esc_url( $item->url ),
				$is_active ? ' class="is-active"' : '',
				esc_html( $item->title )
			);
		}

		echo '</aside>';
	}

	/**
	 * Adres Strony po slugu (`post_name`) — do linków między stronami pomocy
	 * (np. breadcrumb „Pomoc", karty w `page-pomoc.php`), bez twardego
	 * kodowania `/slug/` (niezależne od struktury permalinków instalacji).
	 * Brak Strony o danym slugu → `#` (graceful — literówka w slugu nie
	 * wywraca renderu, tylko zostawia martwy link).
	 *
	 * @param string $slug `post_name` Strony (np. `regulamin`).
	 * @return string
	 */
	public static function page_url( string $slug ): string {
		$page = get_page_by_path( $slug );

		return $page instanceof WP_Post ? (string) get_permalink( $page ) : '#';
	}

	/**
	 * Spis treści stron prawnych (regulamin/polityka-*) — wyciąga gotowe
	 * kotwice z treści Strony. Inaczej niż blog (`Blog\ArticleHeadings`,
	 * który DOGENEROWUJE `id` przy renderze), treść tych trzech Stron jest
	 * wypełniana verbatim z prototypu — kotwica `id="…"` siedzi na
	 * `<section>` OPASUJĄCEJ `<h2>` (port `regulamin.html:34` i analogiczne:
	 * `<section id="s1"><h2>…</h2>`, NIE `<h2 id="s1">`), funkcja tylko
	 * CZYTA, nie modyfikuje. Nagłówek bez opasującego `<section id="…">`
	 * jest pomijany (spis treści ma być kompletny i klikalny, niekompletny
	 * wpis byłby mylący).
	 *
	 * @param string $content Surowa treść Strony (`get_the_content()`).
	 * @return array<int, array{id: string, text: string}>
	 */
	public static function extract_legal_headings( string $content ): array {
		$headings = array();

		if ( ! preg_match_all( '/<section\s+id="([^"]+)"[^>]*>\s*<h2[^>]*>(.*?)<\/h2>/is', $content, $matches, PREG_SET_ORDER ) ) {
			return $headings;
		}

		foreach ( $matches as $match ) {
			$inner = preg_replace( '/<span class="num">.*?<\/span>/is', '', $match[2] );
			$text  = trim( wp_strip_all_tags( is_string( $inner ) ? $inner : $match[2] ) );

			if ( '' === $text ) {
				continue;
			}

			$headings[] = array(
				'id'   => $match[1],
				'text' => $text,
			);
		}

		return $headings;
	}
}
