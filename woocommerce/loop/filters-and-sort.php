<?php
/**
 * Qutlet — toolbar (filtr/licznik/sortowanie) + chipy + szuflada filtrów
 * (port `.toolbar`/`.chips-row`/`.drawer` z design/vanilla/strefa-okazji.html,
 * P-8.3b).
 *
 * WŁASNY szablon (nie nadpisuje realnego pliku WooCommerce — Woo trzyma
 * `result-count.php`/`orderby.php` osobno; tu połączone w jeden, bo oba
 * pola muszą żyć w JEDNYM `<form>` razem z facetami szuflady — klasyczny
 * GET musi nieść cały stan naraz przy każdym submit, patrz
 * `Qutlet\Theme\features\ProductFilters\ProductFilters`, sekcja nagłówkowa
 * pliku klasy — D-8.3b.1). Ładowany przez `wc_get_template()` na hooku
 * `woocommerce_before_shop_loop` (`ProductFilters::render()`).
 *
 * Zmienne wstrzyknięte przez `wc_get_template()` (patrz `ProductFilters::render()`):
 *
 * @var string                                                                  $form_action      Adres formularza (archiwum bez `/page/N/`).
 * @var string                                                                  $current_sort     Bieżąca wartość `orderby` ('' / 'price' / 'price-desc' / 'save').
 * @var array<int, array{slug: string, name: string, count: int, checked: bool}> $brand_facets     Facety marki (z `qutlet-core`, `ProductFilterQuery::brand_facets()`).
 * @var array<int, array{code: string, count: int, checked: bool}>              $condition_facets Facety klasy stanu (z `qutlet-core`, `ProductFilterQuery::condition_facets()` — BEZ etykiety, ta żyje w theme).
 * @var array<string, string>                                                   $condition_labels Etykiety klasy stanu wg kodu (`ProductPage::condition_label()`), klucz = kod A-D.
 * @var array{floor: float, ceil: float}                                        $price_bounds     Granice ceny w bieżącym kontekście.
 * @var array{min: float, max: float}                                           $price_range      Zaznaczony zakres ceny.
 * @var array<int, array{label: string, url: string}>                           $active_chips     Aktywne chipy (z linkiem „usuń").
 * @var int                                                                     $filter_count     Liczba aktywnych filtrów.
 * @var int                                                                     $result_count     Liczba wyników bieżącego zapytania.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$price_floor = (int) round( $price_bounds['floor'] );
$price_ceil  = (int) round( $price_bounds['ceil'] );
$price_min   = (int) round( $price_range['min'] );
$price_max   = (int) round( $price_range['max'] );
?>
<form class="qutlet-filters-form wrap" method="get" action="<?php echo esc_url( $form_action ); ?>">
	<div class="toolbar">
		<button type="button" class="filter-btn" data-open-drawer>
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
			<?php esc_html_e( 'Filtry', 'qutlet-theme' ); ?>
			<span class="filter-btn-count"<?php echo 0 === $filter_count ? ' hidden' : ''; ?>><?php echo esc_html( (string) $filter_count ); ?></span>
		</button>
		<span class="result-count"><b><?php echo esc_html( (string) $result_count ); ?></b> <?php esc_html_e( 'produktów', 'qutlet-theme' ); ?></span>
		<div class="sort-wrap">
			<label for="qutlet-sort"><?php esc_html_e( 'Sortuj:', 'qutlet-theme' ); ?></label>
			<select id="qutlet-sort" name="orderby" data-sort-autosubmit>
				<option value="" <?php selected( '', $current_sort ); ?>><?php esc_html_e( 'Trafność', 'qutlet-theme' ); ?></option>
				<option value="price" <?php selected( 'price', $current_sort ); ?>><?php esc_html_e( 'Cena: rosnąco', 'qutlet-theme' ); ?></option>
				<option value="price-desc" <?php selected( 'price-desc', $current_sort ); ?>><?php esc_html_e( 'Cena: malejąco', 'qutlet-theme' ); ?></option>
				<option value="save" <?php selected( 'save', $current_sort ); ?>><?php esc_html_e( 'Największy rabat', 'qutlet-theme' ); ?></option>
			</select>
		</div>
	</div>

	<?php if ( $active_chips ) : ?>
		<div class="chips-row">
			<?php foreach ( $active_chips as $chip ) : ?>
				<span class="filter-chip">
					<?php echo esc_html( $chip['label'] ); ?>
					<a href="<?php echo esc_url( $chip['url'] ); ?>" aria-label="<?php esc_attr_e( 'Usuń filtr', 'qutlet-theme' ); ?>">
						<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
					</a>
				</span>
			<?php endforeach; ?>
			<a class="clear-filters" href="<?php echo esc_url( $form_action ); ?>"><?php esc_html_e( 'Wyczyść wszystko', 'qutlet-theme' ); ?></a>
		</div>
	<?php endif; ?>

	<div class="drawer-overlay" data-drawer-overlay hidden></div>
	<aside class="drawer" data-drawer hidden>
		<div class="drawer-head">
			<h3><?php esc_html_e( 'Filtry', 'qutlet-theme' ); ?></h3>
			<button type="button" class="icon-btn icon-btn-sm" data-close-drawer aria-label="<?php esc_attr_e( 'Zamknij filtry', 'qutlet-theme' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
			</button>
		</div>
		<div class="drawer-body">
			<?php if ( $brand_facets ) : ?>
				<div class="facet-group">
					<div class="facet-title"><?php esc_html_e( 'Marka', 'qutlet-theme' ); ?></div>
					<div class="facet-list">
						<?php foreach ( $brand_facets as $facet ) : ?>
							<label class="facet-row">
								<input type="checkbox" name="product_brand[]" value="<?php echo esc_attr( $facet['slug'] ); ?>" <?php checked( $facet['checked'] ); ?>>
								<span class="facet-label"><?php echo esc_html( $facet['name'] ); ?></span>
								<span class="facet-count"><?php echo esc_html( (string) $facet['count'] ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $condition_facets ) : ?>
				<div class="facet-group">
					<div class="facet-title"><?php esc_html_e( 'Klasa stanu', 'qutlet-theme' ); ?></div>
					<div class="facet-list">
						<?php foreach ( $condition_facets as $facet ) : ?>
							<label class="facet-row">
								<input type="checkbox" name="klasa_stanu[]" value="<?php echo esc_attr( $facet['code'] ); ?>" <?php checked( $facet['checked'] ); ?>>
								<span class="dot dot-<?php echo esc_attr( strtolower( $facet['code'] ) ); ?>"></span>
								<span class="facet-label">
									<?php
										/* translators: 1: kod klasy stanu (A-D), 2: etykieta klasy stanu. */
										echo esc_html( sprintf( __( 'Klasa %1$s — %2$s', 'qutlet-theme' ), $facet['code'], $condition_labels[ $facet['code'] ] ?? '' ) );
									?>
								</span>
								<span class="facet-count"><?php echo esc_html( (string) $facet['count'] ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $price_ceil > $price_floor ) : ?>
				<div class="facet-group">
					<div class="facet-title"><?php esc_html_e( 'Cena', 'qutlet-theme' ); ?></div>
					<div class="price-readout">
						<span data-price-min><?php echo wp_kses_post( wc_price( $price_min ) ); ?></span>
						<span data-price-max><?php echo wp_kses_post( wc_price( $price_max ) ); ?></span>
					</div>
					<div class="price-sliders">
						<input type="range" name="min_price" min="<?php echo esc_attr( (string) $price_floor ); ?>" max="<?php echo esc_attr( (string) $price_ceil ); ?>" value="<?php echo esc_attr( (string) $price_min ); ?>" data-range-min>
						<input type="range" name="max_price" min="<?php echo esc_attr( (string) $price_floor ); ?>" max="<?php echo esc_attr( (string) $price_ceil ); ?>" value="<?php echo esc_attr( (string) $price_max ); ?>" data-range-max>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="drawer-foot">
			<a class="text-btn drawer-clear" href="<?php echo esc_url( $form_action ); ?>"><?php esc_html_e( 'Wyczyść', 'qutlet-theme' ); ?></a>
			<button type="submit" class="btn btn-primary btn-lg"><?php esc_html_e( 'Pokaż wyniki', 'qutlet-theme' ); ?></button>
		</div>
	</aside>
</form>
