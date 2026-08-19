<?php
/**
 * Slice ProductPage — render strony produktu (port design/vanilla/produkt.html).
 *
 * P-8.2a: szkielet + galeria + nagłówek. P-8.2b: przełącznik kanału zakupu
 * (taby Qutlet/Allegro + buybar + sekcja „jak-to-dziala"), pełna semantyka
 * D-8.G1. P-8.2c: sekcja treści/specyfikacji — `ship_items()` (repeater
 * `zawartosc_zestawu_pozycje`, P-9.2) i `specification_rows()` (atrybuty WC
 * + wiersz „Klasa stanu", kontrakt §9.2). Markup mieszka w woocommerce/content-single-product.php
 * (nadpisanie szablonu WooCommerce); ta klasa trzyma enqueue JS + pomocnicze
 * funkcje odczytu/prezentacji współdzielone przez ten szablon.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\ProductPage;

use Qutlet\Core\ProductCondition\ClassDefinitionsTaxonomy;
use Qutlet\Core\ProductCondition\StoreContentSettingsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue interakcji galerii + pomocnicze funkcje prezentacyjne.
 */
final class ProductPage {

	/**
	 * Podpina enqueue pod wp_enqueue_scripts.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
		add_filter( 'body_class', array( self::class, 'body_class' ) );
	}

	/**
	 * Rejestruje i ładuje JS strony produktu — wyłącznie na stronie produktu.
	 *
	 * @return void
	 */
	public static function enqueue(): void {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		wp_enqueue_script(
			'qutlet-theme-product-gallery',
			get_theme_file_uri( 'assets/js/product-gallery.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);

		wp_enqueue_script(
			'qutlet-theme-product-buy-tabs',
			get_theme_file_uri( 'assets/js/product-buy-tabs.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);

		wp_enqueue_script(
			'qutlet-theme-product-content-tabs',
			get_theme_file_uri( 'assets/js/product-content-tabs.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);

		wp_enqueue_script(
			'qutlet-theme-product-stock-stepper',
			get_theme_file_uri( 'assets/js/product-stock-stepper.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);

		wp_enqueue_script(
			'qutlet-theme-product-other-pieces',
			get_theme_file_uri( 'assets/js/product-other-pieces.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);
	}

	/**
	 * Dokłada `body.allegro-off` na stronie produktu z wyłączonym kanałem
	 * Allegro (D-8.G1) — jedyny cel: CSS-owy przełącznik `.info-3col` → 2
	 * kolumny w sekcji „Dostawa i zwroty" (`docs/kontrakt-danych.md` §4,
	 * „Semantyka renderu"). Elementy `[data-allegro-only]` motyw i tak NIE
	 * renderuje przy wyłączonym kanale (patrz szablon) — ta klasa istnieje
	 * wyłącznie dla layoutu, nie dla ukrywania markupu.
	 *
	 * @param string[] $classes Klasy `<body>` zebrane przez WP.
	 * @return string[]
	 */
	public static function body_class( array $classes ): array {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return $classes;
		}

		$product_id = get_queried_object_id();

		if ( $product_id && ! self::is_allegro_enabled( $product_id ) ) {
			$classes[] = 'allegro-off';
		}

		return $classes;
	}

	/**
	 * Odczyt pola ACF z fallbackiem na post meta, gdyby ACF Pro nie było aktywne
	 * (motyw nie ma prawa fatalować — D-G5 dotyczy jego zależności, nie ACF
	 * bezpośrednio, bo to zależność core, nie theme).
	 *
	 * @param string $key     Literał pola (kontrakt-danych.md §2, VERBATIM).
	 * @param int    $post_id Id produktu.
	 * @return mixed
	 */
	public static function acf_field( string $key, int $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $key, $post_id );
		}

		return get_post_meta( $post_id, $key, true );
	}

	/**
	 * Pozycje zestawu z repeatera ACF `zawartosc_zestawu_pozycje` (kontrakt §2,
	 * D-9.2.1 — P-9.2). Wiersze bez `etykieta` są pomijane (repeater bez ACF Pro
	 * aktywnego zwróciłby surową liczbę wierszy z `get_post_meta()`, nie tablicę —
	 * stąd twardy wymóg `get_field()`, bez fallbacku jak w `acf_field()`).
	 *
	 * @param int $post_id Id produktu.
	 * @return array<int, array{image_id: int, label: string, included: bool}>
	 */
	public static function ship_items( int $post_id ): array {
		if ( ! function_exists( 'get_field' ) ) {
			return array();
		}

		$rows = get_field( 'zawartosc_zestawu_pozycje', $post_id );

		if ( ! is_array( $rows ) ) {
			return array();
		}

		$items = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$label = trim( (string) ( $row['etykieta'] ?? '' ) );

			if ( '' === $label ) {
				continue;
			}

			$items[] = array(
				'image_id' => (int) ( $row['zdjecie'] ?? 0 ),
				'label'    => $label,
				'included' => (bool) ( $row['w_zestawie'] ?? false ),
			);
		}

		return $items;
	}

	/**
	 * Etykieta klasy stanu po `kod` — natywna `nazwa` termu z bytu {@see ClassDefinitionsTaxonomy}
	 * (P-12.1b, REWIZJA — dawniej hardkodowany słownik A→„Jak nowy" itd., kontrakt §2.2).
	 *
	 * Join po `kod` (nie po produkcie) — zostaje WYŁĄCZNIE dla konsumentów budujących
	 * słownik WSZYSTKICH klas (np. `ProductFilters::render_filters_and_sort()`,
	 * etykiety facetów po kodzie z URL). Odczyt klasy PRZYPISANEJ do konkretnego
	 * produktu idzie od P-12.2c przez {@see self::condition_for_product()} (realna
	 * relacja), NIE przez ten join.
	 *
	 * @param string $code Literał klasy stanu (join key `kod`, dziś `A`-`D`).
	 * @return string Pusty string, gdy kod pusty/nieznany (klasa nie istnieje w bycie).
	 */
	public static function condition_label( string $code ): string {
		return self::condition_definition( $code )['nazwa'] ?? '';
	}

	/**
	 * Kolor klasy stanu po `kod` — term meta `kolor` z bytu {@see ClassDefinitionsTaxonomy}
	 * (kontrakt §2.2). Zamiast statycznych klas CSS `.dot-a`…`.dot-d` (P-12.3 —
	 * byt jest rozszerzalny, D-12.G1, nowe klasy jak „Nowe" nie mają gotowej reguły
	 * `.dot-<kod>`), konsumenci wypisują kolor jako inline `style="background:…"`
	 * (ten sam wzorzec co `patterns/class-table.php`/`assets/js/cart-block-filters.js`).
	 *
	 * @param string $code Literał klasy stanu (join key `kod`).
	 * @return string Pusty string, gdy kod pusty/nieznany.
	 */
	public static function condition_color( string $code ): string {
		return self::condition_definition( $code )['kolor'] ?? '';
	}

	/**
	 * Definicja klasy stanu po `kod`, z bytu {@see ClassDefinitionsTaxonomy} (P-12.1b,
	 * kontrakt §2.2) — `null`, gdy kod pusty albo nieznany (np. taksonomia
	 * jeszcze niezaseedowana, patrz `ProductConditionFields::render_missing_class_definitions_notice()`).
	 *
	 * Join po `kod` — patrz zastrzeżenie w {@see self::condition_label()}: dla
	 * odczytu klasy KONKRETNEGO produktu użyj {@see self::condition_for_product()}.
	 *
	 * @param string $code Literał klasy stanu (join key `kod`).
	 * @return array{term_id: int, nazwa: string, kolor: string, opis_chip: string, stan_wizualny: string, charakterystyka: string, dlaczego_taniej: string, okres_gwarancji_miesiace: int, okres_reklamacji_miesiace: int}|null
	 */
	public static function condition_definition( string $code ): ?array {
		if ( '' === $code ) {
			return null;
		}

		return ClassDefinitionsTaxonomy::get( $code );
	}

	/**
	 * Definicja klasy stanu PRZYPISANEJ do produktu — czyta przez realną relację
	 * {@see ClassDefinitionsTaxonomy::for_product()} (P-12.2c, D-12.2.1 cutover),
	 * NIE przez literał + join po `kod` (dawny mechanizm P-12.1b, zostaje jako
	 * {@see self::condition_definition()} wyłącznie dla słownika WSZYSTKICH klas).
	 * Jedyny sposób odczytu klasy produktu w tym slice'u od P-12.2c.
	 *
	 * @param int $product_id Id produktu.
	 * @return array{kod: string, term_id: int, nazwa: string, kolor: string, opis_chip: string, stan_wizualny: string, charakterystyka: string, dlaczego_taniej: string, okres_gwarancji_miesiace: int, okres_reklamacji_miesiace: int, gwarancja_opis: string, reklamacja_opis: string}|null
	 */
	public static function condition_for_product( int $product_id ): ?array {
		return ClassDefinitionsTaxonomy::for_product( $product_id );
	}

	/**
	 * Wszystkie zdefiniowane klasy stanu, kluczowane po `kod` (P-12.1b) — akordeon
	 * „Klasyfikacja produktów" (`.class-table`, kontrakt §2.2), zamiast dawnej
	 * hardkodowanej tablicy `$classification_rows` w szablonie.
	 *
	 * @return array<string, array{term_id: int, nazwa: string, kolor: string, opis_chip: string, stan_wizualny: string, charakterystyka: string, dlaczego_taniej: string, okres_gwarancji_miesiace: int, okres_reklamacji_miesiace: int, gwarancja_opis: string, reklamacja_opis: string}>
	 */
	public static function all_condition_definitions(): array {
		return ClassDefinitionsTaxonomy::all();
	}

	/**
	 * Tekst polityki gwarancji/reklamacji PER KLASA stanu (P-22.5, D-22.5.1/
	 * D-22.5.2, REWIZJA D-22.5.4 — kontrakt §2.2 „Pola tekstów polityk").
	 * Od D-22.5.4 dotyczy WYŁĄCZNIE `gwarancja_opis`/`reklamacja_opis` — dziesięć
	 * pozostałych pól z pierwotnej listy PRZENIESIONO na opcje globalne
	 * ({@see self::store_text()}, `StoreContentSettingsPage`, kontrakt §19.2).
	 * Puste pole term meta (przed backfillem —
	 * {@see \Qutlet\Core\ProductCondition\BackfillPolicyTextsCommand} — albo
	 * gdy `$condition_definition` jest `null`, np. produkt bez relacji)
	 * degraduje się do `$fallback` (dzisiejszy literał), żeby te newralgiczne
	 * teksty NIGDY nie zniknęły z rendera.
	 *
	 * @param array<string, mixed>|null $condition_definition Definicja klasy z {@see self::condition_for_product()}/{@see self::all_condition_definitions()}, albo `null`.
	 * @param string                    $key      Nazwa pola term meta (kontrakt §2.2).
	 * @param string                    $fallback Dzisiejszy literał, gdy pole puste/nieustawione.
	 * @return string
	 */
	public static function policy_text( ?array $condition_definition, string $key, string $fallback ): string {
		$value = (string) ( $condition_definition[ $key ] ?? '' );

		return '' !== $value ? $value : $fallback;
	}

	/**
	 * Tekst polityki (zwrot/wysyłka/zapewnienie „używane") GLOBALNY dla całego
	 * sklepu (P-22.5, REWIZJA D-22.5.4, kontrakt §19.2) — dziesięć tekstów
	 * pierwotnie zaimplementowanych jako pola per-klasa (D-22.5.1/D-22.5.2),
	 * przeniesionych na opcje `StoreContentSettingsPage::OPTION_*` po decyzji
	 * użytkownika, że nie są związane z fizycznym stanem egzemplarza. Opcja
	 * niesie własny domyślny seed (`register_setting()` → `default`), ale
	 * `$fallback` przekazany tu jawnie działa NIEZALEŻNIE od tego, czy hook
	 * `admin_init` zdążył zarejestrować tamten filtr (nie zdąży na froncie
	 * bez wejścia w wp-admin) — ten sam mechanizm bezpieczeństwa co
	 * {@see self::policy_text()}.
	 *
	 * @param string $option_key Literał opcji (`StoreContentSettingsPage::OPTION_*`, kontrakt §19.2).
	 * @param string $fallback   Dzisiejszy literał, gdy opcja puste/nieustawiona.
	 * @return string
	 */
	public static function store_text( string $option_key, string $fallback ): string {
		$value = get_option( $option_key, '' );
		$value = is_string( $value ) ? trim( $value ) : '';

		return '' !== $value ? $value : $fallback;
	}

	/**
	 * Podstawia placeholder `{okres}` w tekście polityki gwarancji/reklamacji
	 * (pola `gwarancja_opis`/`reklamacja_opis`, kontrakt §2.2) sformatowanym
	 * okresem — zastępuje dotychczasową gałąź liczbową `$claim_months >= 24`
	 * w pełni redakcyjną treścią per klasa (D-22.5.2).
	 *
	 * @param string $text  Tekst z placeholderem `{okres}` ({@see self::policy_text()}).
	 * @param string $okres Sformatowany okres ({@see self::period_years_text()}).
	 * @return string
	 */
	public static function with_period_placeholder( string $text, string $okres ): string {
		return str_replace( '{okres}', $okres, $text );
	}

	/**
	 * Okres w miesiącach jako tekst po polskiej odmianie liczebnika, w LATACH
	 * („1 rok"/„2 lata"/„5 lat") — jednostka użyta dziś we WSZYSTKICH miejscach
	 * poza kartą „Gwarancja sprzedawcy" w akordeonie (patrz {@see self::period_months_text()}),
	 * verbatim port copy `content-single-product.php` sprzed P-12.1b (kontrakt §2.2).
	 * Degraduje do zapisu w miesiącach, gdy `$months` nie jest wielokrotnością 12
	 * (dzisiejsze klasy: 12 albo 24) — żeby nie zgadywać niepełnych lat.
	 *
	 * @param int $months Liczba miesięcy (`okres_gwarancji_miesiace`/`okres_reklamacji_miesiace`).
	 * @return string Pusty string, gdy `$months` <= 0.
	 */
	public static function period_years_text( int $months ): string {
		if ( $months <= 0 ) {
			return '';
		}

		if ( 0 !== $months % 12 ) {
			return self::period_months_text( $months );
		}

		$years = intdiv( $months, 12 );

		return sprintf(
			'%d %s',
			$years,
			self::pl_plural( $years, __( 'rok', 'qutlet-theme' ), __( 'lata', 'qutlet-theme' ), __( 'lat', 'qutlet-theme' ) )
		);
	}

	/**
	 * Okres w miesiącach jako tekst po polskiej odmianie liczebnika, w MIESIĄCACH
	 * („12 miesięcy") — patrz {@see self::period_years_text()} dla wariantu w latach.
	 *
	 * @param int $months Liczba miesięcy.
	 * @return string Pusty string, gdy `$months` <= 0.
	 */
	public static function period_months_text( int $months ): string {
		if ( $months <= 0 ) {
			return '';
		}

		return sprintf(
			'%d %s',
			$months,
			self::pl_plural( $months, __( 'miesiąc', 'qutlet-theme' ), __( 'miesiące', 'qutlet-theme' ), __( 'miesięcy', 'qutlet-theme' ) )
		);
	}

	/**
	 * Polska odmiana liczebnika (1 / 2-4 / pozostałe, z nieregularnym wyjątkiem
	 * 12-14 wyłączonym z „2-4") — PORT algorytmu `plPlural()`
	 * (`assets/js/cart-block-filters.js`, P-8.6a), zastosowany do innych słów
	 * (rok/lata/lat, miesiąc/miesiące/miesięcy zamiast sztuka/sztuki/sztuk).
	 * Osobny port do PHP, bo strona produktu renderuje się server-side
	 * (D-12.1a, kontrakt §2.2 — okres gwarancji/reklamacji).
	 *
	 * @param int    $count Liczba.
	 * @param string $one   Forma dla 1.
	 * @param string $few   Forma dla 2-4 (poza 12-14).
	 * @param string $many  Forma dla pozostałych.
	 * @return string
	 */
	private static function pl_plural( int $count, string $one, string $few, string $many ): string {
		if ( 1 === $count ) {
			return $one;
		}

		$mod10  = $count % 10;
		$mod100 = $count % 100;

		if ( $mod10 >= 2 && $mod10 <= 4 && ! ( $mod100 >= 12 && $mod100 <= 14 ) ) {
			return $few;
		}

		return $many;
	}

	/**
	 * Procent, o jaki `$reference_price` jest WYŻSZA od `$sale_price` —
	 * relatywnie do `$reference_price` (nie do `$sale_price`), czyli
	 * `round((1 − sale/reference) * 100)`. Dwa zastosowania w tym szablonie
	 * (ta sama formuła, inny punkt odniesienia):
	 * 1) rabat „-X%" — `$reference_price` = `cena_rynkowa_nowego` (ACF),
	 *    kontrakt §6, `data.js` QT.savePct. NIE przechowywane.
	 * 2) nota „Cena wyższa o ~X%" w panelu Allegro — `$reference_price` =
	 *    `cena_allegro` (ACF). Kontrakt §4/§6 nie precyzuje kierunku formuły;
	 *    kierunek „relatywnie do ceny wyższej" dobrany tak, by odtworzyć
	 *    dokładnie przykład z prototypu (`cena_allegro` 199,00 zł vs cena
	 *    sprzedaży 179,10 zł → ~10%, jak w `produkt.html:109`) — licząc
	 *    względem ceny sprzedaży (`(cena_allegro−sale)/sale`) wyszłoby ~11%.
	 *
	 * @param float $sale_price      Cena niższa (Woo `_price`).
	 * @param float $reference_price Cena wyższa, punkt odniesienia procentu
	 *                               (`cena_rynkowa_nowego` LUB `cena_allegro`).
	 * @return int Zaokrąglony procent; 0, gdy `$reference_price` nie jest dodatnia.
	 */
	public static function save_percent( float $sale_price, float $reference_price ): int {
		if ( $reference_price <= 0.0 ) {
			return 0;
		}

		return (int) round( ( 1 - $sale_price / $reference_price ) * 100 );
	}

	/**
	 * Czy kanał Allegro jest włączony dla produktu (kontrakt §4, „Semantyka
	 * renderu"): `allegro_wlaczone=false` LUB pusty `allegro_url` → kanał
	 * wyłączony, motyw nie renderuje `[data-allegro-only]`.
	 *
	 * @param int $post_id Id produktu.
	 * @return bool
	 */
	public static function is_allegro_enabled( int $post_id ): bool {
		$enabled = (bool) self::acf_field( 'allegro_wlaczone', $post_id );
		$url     = (string) self::acf_field( 'allegro_url', $post_id );

		return $enabled && '' !== trim( $url );
	}

	/**
	 * Cena jako czysty tekst (bez znaczników `wc_price()`) — potrzebne tam,
	 * gdzie cena trafia do atrybutu `data-*` czytanego przez JS
	 * (`assets/js/product-buy-tabs.js`, aktualizacja ceny w buybarze przy
	 * przełączeniu taba).
	 *
	 * @param float $price Cena w groszach/PLN (jak `wc_price()`).
	 * @return string
	 */
	public static function price_text( float $price ): string {
		if ( ! function_exists( 'wc_price' ) ) {
			return '';
		}

		return trim( wp_strip_all_tags( wc_price( $price ) ) );
	}

	/**
	 * Wstrzykuje pogrubiony fragment do przetłumaczalnego szablonu — statyczna
	 * treść informacyjna portowana z prototypu (kontrakt §4, „Brak pola
	 * perks/korzyści": korzyści kanału Allegro to treść szablonu, NIE dane
	 * produktu), więc treść jest literałem w szablonie, nie z ACF.
	 *
	 * @param string $template Tekst z JEDNYM `%s` (miejsce na pogrubienie).
	 * @param string $bold     Tekst do pogrubienia (escapowany tu).
	 * @return string Gotowy HTML — do wypisania przez `echo` bez dalszego escape'owania.
	 */
	public static function bold_text( string $template, string $bold ): string {
		return wp_kses_post( sprintf( $template, '<b>' . esc_html( $bold ) . '</b>' ) );
	}

	/**
	 * Wiersze specyfikacji (etykieta→wartość) z NATYWNYCH atrybutów WooCommerce
	 * (kontrakt §9.2 — `$product->get_attributes()`; D-5.1.1: core NIE rejestruje
	 * dla specyfikacji własnego pola, to natywny mechanizm Woo). Dokłada na
	 * końcu jeden wiersz klasy stanu — etykieta „Stan produktu" (celowo INNA
	 * niż nazwa pola ACF „Klasa stanu"/kontrakt §2, decyzja redakcyjna
	 * użytkownika, sesja 2026-08-19; pierwotnie „Klasa stanu" wzorem
	 * `produkt.html:190`, kontrakt §9.2) — NIE osobna tabela, jeden `spec-row`
	 * tutaj; pełna tabela klasyfikacji A/B/C/D żyje w akordeonie „Klasyfikacja
	 * produktów", zaimplementowanym w P-8.2b.
	 *
	 * **P-9.7 (bug, ground-truth sesja 2026-08-19):** `$condition_code` (term
	 * meta `kod`, kontrakt §2.2) NIE jest ograniczony do pojedynczej litery —
	 * to wolny tekst (od P-12.1c np. `Nowe`, pełne słowo), więc kurator MOŻE
	 * dodać klasę, której `kod` jest identyczny z `nazwa` (nic w walidacji ACF
	 * tego nie zabrania — {@see \Qutlet\Core\ProductCondition\
	 * ClassDefinitionsTaxonomy::validate_unique_kod()} pilnuje wyłącznie
	 * unikalności). Bez tego warunku wiersz renderowałby dosłowny duplikat
	 * („Po zwrocie — Po zwrocie") zamiast czytelnej etykiety.
	 *
	 * @param \WC_Product $product         Produkt.
	 * @param string      $condition_code  Literał klasy stanu (kod, wolny tekst) lub pusty string.
	 * @param string      $condition_label Etykieta klasy stanu (`condition_label()`) lub pusty string.
	 * @return array<int, array{label: string, value: string}>
	 */
	public static function specification_rows( \WC_Product $product, string $condition_code, string $condition_label ): array {
		$rows = array();

		foreach ( $product->get_attributes() as $attribute ) {
			if ( ! $attribute instanceof \WC_Product_Attribute ) {
				continue;
			}

			if ( $attribute->is_taxonomy() ) {
				$terms = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
				$value = implode( ', ', $terms );
			} else {
				$value = implode( ', ', $attribute->get_options() );
			}

			$value = trim( $value );

			if ( '' === $value ) {
				continue;
			}

			$rows[] = array(
				'label' => wc_attribute_label( $attribute->get_name(), $product ),
				'value' => $value,
			);
		}

		if ( '' !== $condition_code && '' !== $condition_label ) {
			$rows[] = array(
				// Etykieta wiersza specyfikacji — celowo INNA niż nazwa pola ACF
				// „Klasa stanu” (kontrakt §2) i tytuł metaboksu „Stan produktu”
				// (P-20.8) — czysto redakcyjna decyzja użytkownika dla tego
				// jednego wiersza widocznego na stronie produktu, sesja 2026-08-19.
				'label' => __( 'Stan produktu', 'qutlet-theme' ),
				// P-9.7: kod i nazwa bywają identyczne (patrz docblock wyżej) —
				// dublet pokazywalibyśmy dosłownie, gdyby nie ten warunek.
				'value' => $condition_code === $condition_label
					? $condition_label
					/* translators: 1: kod klasy stanu, 2: etykieta klasy stanu. */
					: sprintf( __( '%1$s — %2$s', 'qutlet-theme' ), $condition_code, $condition_label ),
			);
		}

		return $rows;
	}

	/**
	 * Sztuki tego samego modelu — widget „Inne sztuki tego modelu" (P-22.4,
	 * D-22.4.3: zapytanie+render w całości tutaj, NIE w qutlet-core — patrz
	 * docblock klasy `StoreContentSettingsPage` w core dla pełnego
	 * uzasadnienia granicy). Grupuje po natywnym Woo `global_unique_id`
	 * (meta_key `_global_unique_id`, kontrakt §10.2) — duplikat GTIN między
	 * produktami jest DOZWOLONY od P-6.7/D-6.7.1, model „1 oferta = 1
	 * produkt" (P-6.1/P-6.7) BEZ ZMIAN (D-6.7.3 ODRZUCONA dla tego zakresu,
	 * `docs/plan.md` P-22.4).
	 *
	 * Wynik WŁĄCZA bieżący produkt (żeby wyznaczyć jego miejsce w kolejności
	 * cenowej — wzorem `produkt-inne-sztuki.html:286-343`, gdzie bieżąca
	 * sztuka siedzi na swoim miejscu cenowym, nie na końcu/początku listy),
	 * posortowany rosnąco po cenie. Sztuki INNE niż bieżąca znikają z wyniku,
	 * gdy wyprzedane (D-22.4.4 — `.ism-fine` prototypu: „sztuka znika z listy
	 * po sprzedaży"); bieżąca zostaje bezwarunkowo (na stronie produktu
	 * dostępnego ma zawsze `_stock`>0 — warunek czysto defensywny, pod
	 * przyszłą integrację ze stroną „produkt wyprzedany", POZA zakresem
	 * P-22.4).
	 *
	 * Pusta tablica, gdy wynik ma mniej niż 2 sztuki (kontrakt widgetu:
	 * renderuje się TYLKO gdy jest z kim porównać) — wołający NIE musi
	 * osobno liczyć elementów przed decyzją czy renderować sekcję.
	 *
	 * @param int $product_id Id bieżącego produktu.
	 * @return array<int, array{
	 *     product_id: int,
	 *     permalink: string,
	 *     title: string,
	 *     image_id: int,
	 *     is_current: bool,
	 *     is_cheapest: bool,
	 *     condition_kod: string,
	 *     condition_label: string,
	 *     condition_color: string,
	 *     price: float,
	 *     price_text: string,
	 *     has_market_price: bool,
	 *     market_price_text: string,
	 *     save_percent: int,
	 *     contents_sentence: string,
	 * }>
	 */
	public static function other_pieces( int $product_id ): array {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return array();
		}

		$product = wc_get_product( $product_id );

		if ( ! $product instanceof \WC_Product ) {
			return array();
		}

		$gtin = trim( (string) $product->get_global_unique_id() );

		if ( '' === $gtin ) {
			return array();
		}

		$sibling_ids = self::sibling_product_ids_by_gtin( $gtin );

		$rows = array();

		foreach ( $sibling_ids as $sibling_id ) {
			$sibling = wc_get_product( $sibling_id );

			if ( ! $sibling instanceof \WC_Product ) {
				continue;
			}

			$is_current = $sibling->get_id() === $product_id;

			if ( ! $is_current && ! $sibling->is_in_stock() ) {
				continue; // D-22.4.4: tylko bieżąca sztuka zostaje mimo braku stanu.
			}

			$condition        = self::condition_for_product( $sibling_id );
			$price            = (float) $sibling->get_price();
			$market_price     = (float) self::acf_field( 'cena_rynkowa_nowego', $sibling_id );
			$has_market_price = $market_price > 0.0;

			$rows[] = array(
				'product_id'        => $sibling_id,
				'permalink'         => (string) get_permalink( $sibling_id ),
				'title'             => get_the_title( $sibling_id ),
				'image_id'          => (int) $sibling->get_image_id(),
				'is_current'        => $is_current,
				'is_cheapest'       => false, // ustalane niżej, po sortowaniu.
				'condition_kod'     => $condition['kod'] ?? '',
				'condition_label'   => $condition['nazwa'] ?? '',
				'condition_color'   => $condition['kolor'] ?? '',
				'price'             => $price,
				'price_text'        => self::price_text( $price ),
				'has_market_price'  => $has_market_price,
				'market_price_text' => $has_market_price ? self::price_text( $market_price ) : '',
				'save_percent'      => $has_market_price ? self::save_percent( $price, $market_price ) : 0,
				'contents_sentence' => self::contents_sentence( $sibling_id ),
			);
		}

		if ( count( $rows ) < 2 ) {
			return array();
		}

		usort(
			$rows,
			static function ( array $a, array $b ): int {
				return $a['price'] <=> $b['price'];
			}
		);

		$rows[0]['is_cheapest'] = true;

		return $rows;
	}

	/**
	 * Id produktów `publish` dzielących ten sam `global_unique_id` (WŁĄCZNIE
	 * z produktem, dla którego GTIN podano — wołający filtruje/oznacza
	 * bieżący sam). Odpytuje BEZPOŚREDNIO indeksowaną tabelę
	 * `wc_product_meta_lookup` (kolumna `global_unique_id`) — TEN SAM
	 * mechanizm, którym natywne `wc_get_product_id_by_global_unique_id()`
	 * znajduje JEDNO dopasowanie (`class-wc-product-data-store-cpt.php`).
	 *
	 * **Świadomie NIE `wc_get_products( array( 'meta_query' => … ) )`** —
	 * `WC_Data_Store::get_wp_query_args()` (`class-wc-data-store-wp.php`)
	 * jawnie POMIJA klucz `meta_query` (`'meta_query' === $key` → `continue`),
	 * więc taki argument jest po cichu ignorowany i zapytanie zwraca
	 * WSZYSTKIE produkty zamiast filtrowanych — znalezione i zweryfikowane
	 * runtime w tej sesji (Playwright, 501 „sztuk" zamiast 3 na testowym
	 * duplikacie GTIN), zanim trafiło do repo.
	 *
	 * @param string $gtin Wartość `global_unique_id`.
	 * @return array<int, int>
	 */
	private static function sibling_product_ids_by_gtin( string $gtin ): array {
		global $wpdb;

		if ( ! isset( $wpdb->wc_product_meta_lookup ) ) {
			return array();
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- brak natywnego API WooCommerce dla wielokrotnego dopasowania po `global_unique_id` (patrz docblock), lookup table jest indeksowana po tej kolumnie.
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"
				SELECT posts.ID
				FROM {$wpdb->posts} AS posts
				INNER JOIN {$wpdb->wc_product_meta_lookup} AS lookup ON posts.ID = lookup.product_id
				WHERE posts.post_type = 'product'
				AND posts.post_status = 'publish'
				AND lookup.global_unique_id = %s
				",
				$gtin
			)
		);

		return array_map( 'intval', $ids );
	}

	/**
	 * Jednozdaniowe „co w zestawie" dla widgetu „Inne sztuki tego modelu"
	 * (P-22.4, D-22.4.1) — złączenie etykiet pozycji repeatera
	 * `zawartosc_zestawu_pozycje` (P-9.2) oznaczonych `w_zestawie=true`.
	 * Gdy wynik pusty (repeater pusty ALBO żadna pozycja nie jest oznaczona
	 * jako dołączona) — tekst zastępczy z opcji globalnej
	 * {@see StoreContentSettingsPage::FALLBACK_OPTION} (`qutlet-core`,
	 * D-22.4.2); puste ustawienie → pusty string (wołający pomija wiersz).
	 *
	 * @param int $product_id Id produktu.
	 * @return string
	 */
	private static function contents_sentence( int $product_id ): string {
		$included_labels = array();

		foreach ( self::ship_items( $product_id ) as $item ) {
			if ( $item['included'] ) {
				$included_labels[] = $item['label'];
			}
		}

		if ( array() !== $included_labels ) {
			return implode( ', ', $included_labels );
		}

		$fallback = get_option( StoreContentSettingsPage::FALLBACK_OPTION, '' );

		return is_string( $fallback ) ? trim( $fallback ) : '';
	}
}
