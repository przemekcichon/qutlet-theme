<?php
/**
 * Slice Search — wyszukiwarka (P-23.4, Relevanssi wersja darmowa). Nagłówek
 * (`parts/header.html`, `.search`) wysyła natywny GET `?s=` bez zawężenia
 * `post_type` — WP domyślnie przeszukałby wtedy WSZYSTKIE publiczne typy
 * treści (w tym `page`: Kontakt, Regulamin itd.), a decyzja użytkownika
 * (sesja 2026-08-20) zawęża zakres do produktów ORAZ wpisów bloga. Ta klasa
 * TYLKO zawęża `post_type` głównego zapytania wyszukiwania — Relevanssi
 * (aktywny, skonfigurowany osobno w DB) podpina się pod TEN SAM
 * `pre_get_posts`/`WP_Query` i respektuje `query_vars['post_type']`
 * (`relevanssi/lib/common.php`), więc theme nie zna/nie duplikuje logiki
 * samego wyszukiwania (D-8.G1 — render + wiring, nie silnik search).
 *
 * `search.php` (klasyczny szablon, wzorem `page-{slug}.php` z P-8.5 — patrz
 * `locate_block_template()` w `header.php`/`footer.php` po uzasadnienie
 * fallbacku na klasyczną hierarchię w tym motywie blokowym) grupuje wyniki
 * TEJ SAMEJ pętli w dwie sekcje (Produkty / Wpisy blog, decyzja użytkownika
 * sesja 2026-08-20) zamiast jednej wspólnej listy.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Search;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zawężenie zakresu wyszukiwarki frontendowej do produktów i wpisów bloga.
 */
final class Search {

	/**
	 * Typy treści wyszukiwane przez `.search` w nagłówku (kontrakt UI —
	 * placeholder inputu sugeruje produkty, decyzja użytkownika dokłada
	 * wpisy bloga; `page` świadomie WYŁĄCZONE z wyników, mimo że Relevanssi
	 * ma je nadal w indeksie — patrz nagłówek klasy).
	 */
	private const SEARCHABLE_POST_TYPES = array( 'product', 'post' );

	/**
	 * Podpina zawężenie głównego zapytania wyszukiwania.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'pre_get_posts', array( self::class, 'restrict_query' ) );
	}

	/**
	 * Zawęża `post_type` GŁÓWNEGO zapytania wyszukiwania na froncie do
	 * {@see SEARCHABLE_POST_TYPES}. Admin (wyszukiwarka w adminie przeszukuje
	 * inne typy, np. zamówienia) i zapytania poboczne (widgety, REST) zostają
	 * bez zmian.
	 *
	 * @param WP_Query $query Zapytanie (hook `pre_get_posts`).
	 * @return void
	 */
	public static function restrict_query( WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
			return;
		}

		$query->set( 'post_type', self::SEARCHABLE_POST_TYPES );
	}

	/**
	 * Polska odmiana liczebnika (1 / 2-4 poza 12-14 / pozostałe, w tym 0) —
	 * PORT `QT.plural` (`design/vanilla/js/data.js:104`), zwraca gotowe
	 * „N słowo" (`.search-summary`/`.results-section-count`, search.php).
	 * Konkatenacja liczby + przetłumaczonej formy bez dodatkowego owijania w
	 * `__()` — sam wzorzec placeholderów nie niesie treści do tłumaczenia
	 * (kolejność liczba→słowo jest tu stała, nie językowa), wzorem
	 * `ProductCard::qty_label()`.
	 *
	 * @param int    $count Liczba.
	 * @param string $one   Forma dla 1.
	 * @param string $few   Forma dla 2-4 (poza 12-14).
	 * @param string $many  Forma dla pozostałych (w tym 0).
	 * @return string
	 */
	public static function count_label( int $count, string $one, string $few, string $many ): string {
		if ( 1 === $count ) {
			$word = $one;
		} else {
			$mod10  = $count % 10;
			$mod100 = $count % 100;
			$word   = ( $mod10 >= 2 && $mod10 <= 4 && ! ( $mod100 >= 12 && $mod100 <= 14 ) ) ? $few : $many;
		}

		return $count . ' ' . $word;
	}
}
