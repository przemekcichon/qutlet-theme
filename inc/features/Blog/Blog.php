<?php
/**
 * Slice Blog — render archiwum/artykułu/kategorii/tagu bloga (P-8.4), port
 * design/vanilla/blog.html, blog-artykul.html, blog-kategoria.html,
 * blog-tag.html. Blog stoi na natywnych wpisach WP + natywnych
 * `category`/`post_tag` (D-1.4.1, qutlet-core NIE rejestruje CPT/taksonomii
 * własnej) — ta klasa jest CZYSTYM renderem (D-8.G1): woła gotowe dane
 * (główne zapytanie WP, get_post_meta) i buduje z nich markup; nie
 * rejestruje pól ani glue do Woo/WP.
 *
 * Szablony klasyczne (home.php/single.php/category.php/tag.php) zamiast
 * blokowych `templates/*.html` — świadomy wybór (doprecyzowanie otwartego
 * D-8.G2 na tym punkcie): layout bloga (karty, TOC, powiązane wpisy,
 * nawigacja prev/next, karuzela wyróżnionego wpisu) wymaga precyzyjnego
 * markupu 1:1 z prototypem, którego natywne bloki (Query Loop/Post Template)
 * nie odwzorują bez dużej liczby własnych bloków dynamicznych. WP core
 * wspiera klasyczne pliki jako fallback w motywach blokowych, gdy dla danego
 * typu NIE istnieje żaden plik w `templates/`
 * (`wp-includes/block-template.php` `locate_block_template()`, zweryfikowane
 * w tej instalacji WP 7.0.2) — ten sam mechanizm, z którego już korzysta
 * `woocommerce/content-product.php` (WooCommerce ma własny odpowiednik przez
 * `wp:woocommerce/legacy-template`). Nagłówek/stopka bloga renderują part
 * `parts/header.html`/`parts/footer.html` przez `block_header_area()`/
 * `block_footer_area()` (WP core, `wp-includes/block-template-utils.php`) z
 * klasycznych `header.php`/`footer.php` w korzeniu motywu — patrz komentarze
 * w tych plikach.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Blog;

use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pomocnicze funkcje prezentacyjne bloga.
 */
final class Blog {

	/**
	 * `meta_key` czasu czytania — literał z kontraktu §5 (VERBATIM), zapisywany
	 * przez `qutlet-core` (`Qutlet\Core\ReadingTime\ReadingTimeMeta`, P-1.4).
	 * Theme czyta GOTOWĄ wartość — nie zna/nie importuje sposobu jej
	 * wyliczenia w core (granica artefaktów, D-8.G1).
	 */
	private const READING_TIME_META_KEY = '_qutlet_reading_time';

	/**
	 * Prędkość czytania w słowach na minutę — TA SAMA stała co core
	 * (D-1.4.2), potrzebna WYŁĄCZNIE do fallbacku, gdy meta jest pusta
	 * (kontrakt §5, „opcjonalność": wpisy sprzed aktywacji wtyczki / nigdy
	 * nie zapisane ponownie na `save_post` nie mają tej meta — motyw MUSI
	 * policzyć w locie albo ukryć etykietę; wybraliśmy przeliczenie, żeby
	 * etykieta zawsze się pokazywała klientowi).
	 */
	private const WORDS_PER_MINUTE = 200;

	/**
	 * Rejestruje boot slice'a.
	 *
	 * @return void
	 */
	public static function boot(): void {
		// Jedyny hook globalny slice'a — reszta to statyczne helpery wołane wprost
		// z szablonów klasycznych (home.php i pozostałe).
		add_filter( 'document_title_parts', array( self::class, 'filter_document_title_parts' ) );
	}

	/**
	 * Dopasowuje `<title>` widoków bloga do prototypu (`blog.html:6`,
	 * `blog-artykul.html:6`, `blog-kategoria.html:6`, `blog-tag.html:6` — każdy
	 * bez nazwy strony na końcu, tylko „… — Drugi obieg[, blog Qutlet]").
	 * Usuwa WYŁĄCZNIE fragment `site` (nazwa strony) na widokach bloga — separator
	 * i reszta zachowania `wp_get_document_title()` zostają domyślne (globalny
	 * `document_title_separator` poza zakresem tego punktu, dotyczyłby też
	 * produktu/stron innych niż blog).
	 *
	 * @param array<string, string> $title Części tytułu (WP core).
	 * @return array<string, string>
	 */
	public static function filter_document_title_parts( array $title ): array {
		if ( is_singular( 'post' ) ) {
			$title['title']   = single_post_title( '', false );
			$title['tagline'] = __( 'Drugi obieg', 'qutlet-theme' );
			unset( $title['site'] );
		} elseif ( is_category() ) {
			$term = get_queried_object();

			if ( $term instanceof WP_Term ) {
				$title['title'] = $term->name;
			}

			$title['tagline'] = __( 'Drugi obieg, blog Qutlet', 'qutlet-theme' );
			unset( $title['site'] );
		} elseif ( is_tag() ) {
			$term = get_queried_object();

			if ( $term instanceof WP_Term ) {
				/* translators: %s: slug tagu. */
				$title['title'] = sprintf( __( 'Tag: #%s', 'qutlet-theme' ), $term->slug );
			}

			$title['tagline'] = __( 'Drugi obieg, blog Qutlet', 'qutlet-theme' );
			unset( $title['site'] );
		} elseif ( is_home() ) {
			$title['title']   = __( 'Drugi obieg', 'qutlet-theme' );
			$title['tagline'] = __( 'blog Qutlet', 'qutlet-theme' );
			unset( $title['site'] );
		}

		return $title;
	}

	/**
	 * Czas czytania w minutach: gotowa meta z core, a przy jej braku — fallback
	 * liczony w locie tą samą formułą (kontrakt §5, D-1.4.2).
	 *
	 * @param int $post_id ID wpisu.
	 * @return int Minuty (minimum 1).
	 */
	public static function reading_minutes( int $post_id ): int {
		$stored = get_post_meta( $post_id, self::READING_TIME_META_KEY, true );

		if ( '' !== $stored ) {
			return max( 1, (int) $stored );
		}

		$content = (string) get_post_field( 'post_content', $post_id );
		$words   = self::count_words( trim( wp_strip_all_tags( strip_shortcodes( $content ) ) ) );

		return max( 1, (int) ceil( $words / self::WORDS_PER_MINUTE ) );
	}

	/**
	 * Etykieta czasu czytania.
	 *
	 * @param int  $post_id   ID wpisu.
	 * @param bool $with_word Czy dołączyć słowo „czytania" (karta = nie, artykuł = tak).
	 * @return string
	 */
	public static function reading_time_label( int $post_id, bool $with_word = true ): string {
		$minutes = self::reading_minutes( $post_id );

		if ( $with_word ) {
			/* translators: %d: liczba minut. */
			return sprintf( __( '%d min czytania', 'qutlet-theme' ), $minutes );
		}

		/* translators: %d: liczba minut. */
		return sprintf( __( '%d min', 'qutlet-theme' ), $minutes );
	}

	/**
	 * Liczy tokeny rozdzielone białymi znakami — TEN SAM algorytm co core
	 * (`Qutlet\Core\ReadingTime\ReadingTimeMeta::count_words()`), NIE
	 * `str_word_count()`, który gubi polskie znaki diakrytyczne.
	 *
	 * @param string $text Oczyszczony tekst (bez shortcode'ów/znaczników HTML).
	 * @return int
	 */
	private static function count_words( string $text ): int {
		if ( '' === $text ) {
			return 0;
		}

		$parts = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );

		return is_array( $parts ) ? count( $parts ) : 0;
	}

	/**
	 * URL archiwum bloga — strona przypisana jako „Strona wpisów" (Ustawienia →
	 * Czytanie, `page_for_posts`; na tej instalacji: strona „Blog", `/blog/`),
	 * a gdyby nie była ustawiona — root (`show_on_front = posts`).
	 *
	 * @return string
	 */
	public static function blog_url(): string {
		$page_for_posts = (int) get_option( 'page_for_posts' );

		return $page_for_posts > 0 ? (string) get_permalink( $page_for_posts ) : home_url( '/' );
	}

	/**
	 * Pasek kategorii bloga („Wszystkie" + realne, niepuste kategorie WP) —
	 * port `.blog-cats`/`.blog-cat-chip` (`blog.html:29-36`, komentarz
	 * `→ wp_list_categories()`). Realne kategorie z instalacji, NIE przykładowe
	 * nazwy z prototypu (te nie istnieją jako termy w tej instalacji).
	 *
	 * @param WP_Term|null $current Aktywna kategoria (null = archiwum główne/„Wszystkie").
	 * @return void
	 */
	public static function render_category_chips( ?WP_Term $current = null ): void {
		$categories = get_categories( array( 'hide_empty' => true ) );

		if ( empty( $categories ) ) {
			return;
		}

		echo '<div class="blog-cats">';

		printf(
			'<a href="%1$s" class="blog-cat-chip%2$s">%3$s</a>',
			esc_url( self::blog_url() ),
			null === $current ? ' active' : '',
			esc_html__( 'Wszystkie', 'qutlet-theme' )
		);

		foreach ( $categories as $category ) {
			$is_active = ( null !== $current && $current->term_id === $category->term_id );

			printf(
				'<a href="%1$s" class="blog-cat-chip%2$s">%3$s</a>',
				esc_url( (string) get_category_link( $category ) ),
				$is_active ? ' active' : '',
				esc_html( $category->name )
			);
		}

		echo '</div>';
	}

	/**
	 * ID wyróżnionego (sticky) wpisu bloga do sekcji `.featured-post` na
	 * archiwum głównym — port `blog.html:38-56` (komentarz „→ sticky post").
	 * Brak wpisu sticky → 0 (sekcja się nie renderuje — wyróżnienie to
	 * świadoma decyzja redakcyjna, nie każdy blog musi jej mieć).
	 *
	 * @return int
	 */
	public static function featured_post_id(): int {
		$sticky = get_option( 'sticky_posts' );

		if ( ! is_array( $sticky ) || empty( $sticky ) ) {
			return 0;
		}

		return (int) $sticky[0];
	}

	/**
	 * Inicjały autora do awatara `.acct-avatar` (2 pierwsze słowa nazwy
	 * wyświetlanej) — port stałego wzorca z prototypu (np. „MW").
	 *
	 * @param int $user_id ID autora.
	 * @return string
	 */
	public static function author_initials( int $user_id ): string {
		$name  = (string) get_the_author_meta( 'display_name', $user_id );
		$words = preg_split( '/\s+/u', trim( $name ), -1, PREG_SPLIT_NO_EMPTY );

		if ( ! is_array( $words ) || empty( $words ) ) {
			return '';
		}

		$letters = array_map(
			static function ( string $word ): string {
				return mb_strtoupper( mb_substr( $word, 0, 1 ) );
			},
			array_slice( $words, 0, 2 )
		);

		return implode( '', $letters );
	}

	/**
	 * Paginacja archiwów bloga (home/category/tag) na GŁÓWNYM zapytaniu —
	 * ten sam wzorzec `.pager`/`.page-btn` co
	 * `woocommerce/loop/pagination.php` (P-8.3a/P-9.4), tu jednak na
	 * natywnym `$wp_query`, nie na loop props WooCommerce.
	 *
	 * @return void
	 */
	public static function render_pagination(): void {
		global $wp_query;

		$total = (int) $wp_query->max_num_pages;

		if ( $total <= 1 ) {
			return;
		}

		$current    = max( 1, (int) get_query_var( 'paged' ) );
		$chev_left  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>';
		$chev_right = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>';

		$links = paginate_links(
			array(
				'current'   => $current,
				'total'     => $total,
				'prev_text' => $chev_left,
				'next_text' => $chev_right,
				'type'      => 'array',
				'end_size'  => 3,
				'mid_size'  => 3,
			)
		);

		if ( ! $links ) {
			return;
		}

		$class_map = array(
			'class="page-numbers current"' => 'class="page-btn active"',
			'class="page-numbers dots"'    => 'class="pager-dots"',
			'class="prev page-numbers"'    => 'class="page-btn"',
			'class="next page-numbers"'    => 'class="page-btn"',
			'class="page-numbers"'         => 'class="page-btn"',
		);
		?>
		<nav class="pager" aria-label="<?php esc_attr_e( 'Paginacja wpisów', 'qutlet-theme' ); ?>">
			<?php if ( 1 === $current ) : ?>
				<span class="page-btn" aria-disabled="true"><?php echo $chev_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literał SVG zdefiniowany wyżej w tej metodzie, nie dane. ?></span>
			<?php endif; ?>
			<?php foreach ( $links as $link ) : ?>
				<?php echo str_replace( array_keys( $class_map ), array_values( $class_map ), $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output paginate_links() (WP core), tylko podmiana klas. ?>
			<?php endforeach; ?>
			<?php if ( $current === $total ) : ?>
				<span class="page-btn" aria-disabled="true"><?php echo $chev_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literał SVG zdefiniowany wyżej w tej metodzie, nie dane. ?></span>
			<?php endif; ?>
		</nav>
		<?php
	}

	/**
	 * ID wpisów (`post`) przypisanych do danego tagu — pomocnicze dla
	 * `related_tags()`/`primary_category_for_tag()` (współdzielone zapytanie).
	 *
	 * @param WP_Term $tag   Tag.
	 * @param int     $limit Maksymalna liczba wpisów do przejrzenia.
	 * @return int[]
	 */
	private static function post_ids_for_tag( WP_Term $tag, int $limit ): array {
		return get_posts(
			array(
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'post_tag',
						'field'    => 'term_id',
						'terms'    => $tag->term_id,
					),
				),
				'post_type'      => 'post',
				'posts_per_page' => $limit,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
	}

	/**
	 * Tagi WYSTĘPUJĄCE w danej kategorii (realne, nie przykładowe z
	 * prototypu), posortowane wg popularności — port „popularne tagi" na
	 * `category.php` (`blog-kategoria.html:119-127`).
	 *
	 * @param int $category_id ID kategorii.
	 * @param int $limit       Maksymalna liczba tagów.
	 * @return WP_Term[]
	 */
	public static function popular_tags_in_category( int $category_id, int $limit = 5 ): array {
		$post_ids = get_posts(
			array(
				'category'       => $category_id,
				'post_type'      => 'post',
				'posts_per_page' => 50,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		if ( empty( $post_ids ) ) {
			return array();
		}

		$terms = wp_get_object_terms( $post_ids, 'post_tag', array( 'orderby' => 'count', 'order' => 'DESC' ) );

		if ( ! is_array( $terms ) ) {
			return array();
		}

		return array_slice( $terms, 0, $limit );
	}

	/**
	 * Tagi współwystępujące z danym tagiem (te same wpisy) — port
	 * „Powiązane" na `tag.php` (`blog-tag.html:32-38`). Wyklucza sam tag.
	 *
	 * @param WP_Term $tag   Bieżący tag.
	 * @param int     $limit Maksymalna liczba tagów.
	 * @return WP_Term[]
	 */
	public static function related_tags( WP_Term $tag, int $limit = 4 ): array {
		$post_ids = self::post_ids_for_tag( $tag, 50 );

		if ( empty( $post_ids ) ) {
			return array();
		}

		$terms = wp_get_object_terms( $post_ids, 'post_tag', array( 'orderby' => 'count', 'order' => 'DESC' ) );

		if ( ! is_array( $terms ) ) {
			return array();
		}

		$terms = array_filter(
			$terms,
			static function ( WP_Term $term ) use ( $tag ): bool {
				return $term->term_id !== $tag->term_id;
			}
		);

		return array_slice( array_values( $terms ), 0, $limit );
	}

	/**
	 * Najczęstsza kategoria wśród wpisów danego tagu — port linku
	 * „Kategoria: …" na `tag.php` (`blog-tag.html:37`).
	 *
	 * @param WP_Term $tag Bieżący tag.
	 * @return WP_Term|null
	 */
	public static function primary_category_for_tag( WP_Term $tag ): ?WP_Term {
		$post_ids = self::post_ids_for_tag( $tag, 50 );

		if ( empty( $post_ids ) ) {
			return null;
		}

		$terms = wp_get_object_terms( $post_ids, 'category', array( 'orderby' => 'count', 'order' => 'DESC' ) );

		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return null;
		}

		return $terms[0];
	}
}
