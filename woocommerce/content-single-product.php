<?php
/**
 * Qutlet — treść strony produktu (port design/vanilla/produkt.html).
 *
 * Nadpisuje domyślny szablon WooCommerce
 * (woocommerce/templates/content-single-product.php) — theme owns the
 * customer-facing render (D-8.G1), core tylko dostarcza dane (ACF + Woo).
 *
 * P-8.2a: układ + galeria + nagłówek + klasa stanu + ceny (`now`/`old`,
 * rabat). P-8.2b: przełącznik kanału zakupu (taby Qutlet/Allegro + buybar),
 * pełna semantyka D-8.G1 ([data-allegro-only]/[data-allegro-off-only],
 * `.info-3col`/2-kolumnowy wariant przez `body.allegro-off`) oraz sekcja
 * „Wszystko, co warto wiedzieć" (`#jak-to-dziala`) — to jedyne miejsce w
 * prototypie, gdzie te selektory realnie żyją (kontrakt §4). P-8.2c: sekcja
 * treści/specyfikacji MIĘDZY `.pd-grid` a `.know` niżej — taby „Co w
 * przesyłce" (karuzela + checklista z repeatera ACF `zawartosc_zestawu_pozycje`,
 * P-9.2/D-9.2.1 — zastąpił WYSIWYG z P-1.2) / „Opis i specyfikacja" (natywny
 * opis produktu — `post_content`, `$product->get_description()`, P-13.3c —
 * ACF `opis` z P-5.1b wycofane w P-13.3a — + natywne atrybuty WooCommerce,
 * kontrakt §9.2). P-13.2d: podtytuł `.pd-subtitle` pod `h1.pd-title` z ACF
 * `podnazwa` (kontrakt §9.2), gdy niepuste — celowo NIE w buybarze
 * (`.buybar-title`), który zostaje samym `post_title` (buybar jest zwięzły z
 * założenia, D-8.G1).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\ProductPage\ProductPage;

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 *
 * Oryginalny szablon Woo woła to PRZED sprawdzeniem hasła — motyw go nie
 * wołał w ogóle (P-8.2a nie miało realnego <form> koszyka, więc nie było
 * czego notyfikować). P-8.2b wprowadza prawdziwy add-to-cart (patrz
 * woocommerce/single-product/add-to-cart/simple.php), więc komunikaty
 * („Dodano do koszyka", błędy) muszą się renderować — bez tego hooka
 * znikałyby w milczeniu.
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$product_id             = $product->get_id();
$podnazwa               = (string) ProductPage::acf_field( 'podnazwa', $product_id );
$condition_definition   = ProductPage::condition_for_product( $product_id ); // P-12.2c: relacja, nie literał + join po `kod`.
$condition_code         = $condition_definition['kod'] ?? '';
$condition_label        = $condition_definition['nazwa'] ?? '';
$condition_definitions  = ProductPage::all_condition_definitions();
$market_price           = (float) ProductPage::acf_field( 'cena_rynkowa_nowego', $product_id );
$sale_price             = (float) $product->get_price();
$has_market_price       = $market_price > 0.0;

$allegro_enabled = ProductPage::is_allegro_enabled( $product_id );
$allegro_url     = (string) ProductPage::acf_field( 'allegro_url', $product_id );
$cena_allegro    = (float) ProductPage::acf_field( 'cena_allegro', $product_id );

$sale_price_text    = ProductPage::price_text( $sale_price );
$allegro_price_text = $allegro_enabled ? ProductPage::price_text( $cena_allegro ) : '';
$allegro_markup_pct = $allegro_enabled ? ProductPage::save_percent( $sale_price, $cena_allegro ) : 0;

/*
 * Gwarancja + reklamacja (P-12.1b, kontrakt §2.2, D-12.G3 — dwa osobne pola
 * bytu) — używane w DWÓCH `perk-row` (panel Qutlet/Allegro, ta sama treść w
 * obu, bo warunki gwarancji nie zależą od kanału zakupu) oraz w `.pd-fine`
 * niżej. Gdy oba okresy są równe (dziś zawsze — A-D: 12/12), zostaje JEDNA
 * fraza jak w oryginalnym copy; gdyby admin kiedyś ustawił różne wartości per
 * klasa (byt to dopuszcza, D-12.G3), rozpada się na dwie osobne frazy —
 * decyzja DATA-DRIVEN (liczbowe porównanie), nie `if ($condition_code === …)`
 * (D-12.G1). Gdy tylko JEDNO z pól jest wypełnione (np. `min=>0` na polu ACF
 * dopuszcza literalnie zero), pokazujemy TEN fakt — nie chowamy całego wiersza
 * tylko bo drugi jest pusty (recenzja PR#28, sesja 2026-08-12/13).
 */
$warranty_months     = $condition_definition['okres_gwarancji_miesiace'] ?? 0;
$claim_months        = $condition_definition['okres_reklamacji_miesiace'] ?? 0;
$warranty_claim_text = '';

if ( $warranty_months > 0 && $claim_months > 0 ) {
	$warranty_claim_text = $warranty_months === $claim_months
		? sprintf(
			/* translators: %s: formatted warranty/claim period (e.g. "1 rok"). */
			__( 'Gwarancja i prawo do reklamacji: %s', 'qutlet-theme' ),
			ProductPage::period_years_text( $warranty_months )
		)
		: sprintf(
			/* translators: 1: formatted warranty period, 2: formatted claim period. */
			__( 'Gwarancja: %1$s · Reklamacja: %2$s', 'qutlet-theme' ),
			ProductPage::period_years_text( $warranty_months ),
			ProductPage::period_years_text( $claim_months )
		);
} elseif ( $warranty_months > 0 ) {
	$warranty_claim_text = sprintf(
		/* translators: %s: formatted warranty period (e.g. "1 rok"). */
		__( 'Gwarancja: %s', 'qutlet-theme' ),
		ProductPage::period_years_text( $warranty_months )
	);
} elseif ( $claim_months > 0 ) {
	$warranty_claim_text = sprintf(
		/* translators: %s: formatted claim period (e.g. "1 rok"). */
		__( 'Reklamacja: %s', 'qutlet-theme' ),
		ProductPage::period_years_text( $claim_months )
	);
}

$claim_period_text = ProductPage::period_years_text( $claim_months );

/*
 * Sekcja treści (P-8.2c): taby „Co w przesyłce" / „Opis i specyfikacja".
 * Zawartość zestawu = repeater ACF `zawartosc_zestawu_pozycje` (P-9.2,
 * D-9.2.1 — zastąpił WYSIWYG z P-1.2). Karuzela dostaje TYLKO wiersze ze
 * zdjęciem; checklista pokazuje wszystkie pozycje (patrz kontrakt §2).
 */
$ship_items          = ProductPage::ship_items( $product_id );
$ship_carousel_items = array_values(
	array_filter(
		$ship_items,
		static function ( array $item ): bool {
			return $item['image_id'] > 0;
		}
	)
);
$has_ship = array() !== $ship_items;

$description_html   = (string) $product->get_description(); // natywny post_content (P-13.3c) — ACF `opis` wycofane w P-13.3a.
$specification_rows = ProductPage::specification_rows( $product, $condition_code, $condition_label );
$has_desc           = '' !== trim( $description_html ) || array() !== $specification_rows;

$categories = wc_get_product_terms( $product_id, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );
$category   = $categories ? reset( $categories ) : null;
$category_url = $category ? get_term_link( $category ) : '';

$image_ids = array_values(
	array_unique(
		array_filter(
			array_merge(
				array( $product->get_image_id() ),
				$product->get_gallery_image_ids()
			)
		)
	)
);

/*
 * Structured data (schema.org JSON-LD): oryginalny szablon Woo generuje ją z
 * `WC_Structured_Data::generate_product_data()`, wpiętego na hook
 * `woocommerce_single_product_summary` (priorytet 60) — hook, którego ten
 * szablon świadomie NIE woła (zduplikowałby tytuł/cenę/meta, które renderujemy
 * własnym markupem). Wołamy generator wprost: wypełnia bufor `WC_Structured_Data`,
 * a globalny hook `wp_footer` (`output_structured_data`, zarejestrowany w
 * konstruktorze klasy — niezależny od tego, co usunęliśmy w functions.php) i tak
 * go wypisze. Zero dodatkowego markupu z tego wywołania — czyste uzupełnienie SEO.
 */
if ( function_exists( 'wc' ) && wc()->structured_data ) {
	wc()->structured_data->generate_product_data( $product );
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
<div class="wrap">

	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'qutlet-theme' ); ?></a>
		<?php if ( $category && ! is_wp_error( $category_url ) ) : ?>
			<span class="sep">/</span>
			<a href="<?php echo esc_url( $category_url ); ?>"><?php echo esc_html( $category->name ); ?></a>
		<?php endif; ?>
		<span class="sep">/</span>
		<span class="current"><?php echo esc_html( get_the_title() ); ?></span>
	</nav>

	<div class="pd-grid">
		<div>
			<div class="pd-main-img" data-gallery-main>
				<?php if ( $image_ids ) : ?>
					<?php echo wp_get_attachment_image( $image_ids[0], 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<div class="ph-stripes">[ <?php esc_html_e( 'brak zdjęcia', 'qutlet-theme' ); ?> ]</div>
				<?php endif; ?>
			</div>
			<?php if ( count( $image_ids ) > 1 ) : ?>
				<div class="pd-thumbs">
					<?php foreach ( $image_ids as $index => $image_id ) : ?>
						<button
							type="button"
							class="pd-thumb<?php echo 0 === $index ? ' active' : ''; ?>"
							data-gallery-thumb
							data-main-html="<?php echo esc_attr( wp_get_attachment_image( $image_id, 'large' ) ); ?>"
						>
							<?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div>
			<?php if ( null !== $condition_definition ) : ?>
				<div class="class-row">
					<span class="class-pill"><?php echo esc_html( $condition_definition['opis_chip'] ); ?></span>
					<a href="#jak-to-dziala" class="class-link"><?php esc_html_e( 'Co to znaczy?', 'qutlet-theme' ); ?></a>
				</div>
			<?php endif; ?>

			<h1 class="pd-title"><?php the_title(); ?></h1>

			<?php if ( '' !== trim( $podnazwa ) ) : ?>
				<p class="pd-subtitle"><?php echo esc_html( $podnazwa ); ?></p>
			<?php endif; ?>

			<?php if ( $allegro_enabled ) : ?>
				<div data-buy-tabs data-allegro-only>
					<div class="buy-tabs-label"><?php esc_html_e( 'Wybierz opcję zakupu', 'qutlet-theme' ); ?></div>
					<div class="buy-tabs">
						<button type="button" class="buy-tab active" data-buy-tab="qutlet" data-price-text="<?php echo esc_attr( $sale_price_text ); ?>">
							<span class="buy-tab-name"><?php esc_html_e( 'Kup przez QUTLET', 'qutlet-theme' ); ?></span>
							<span class="buy-tab-sub"><?php echo esc_html( $sale_price_text ); ?></span>
						</button>
						<button type="button" class="buy-tab buy-tab-allegro" data-buy-tab="allegro" data-price-text="<?php echo esc_attr( $allegro_price_text ); ?>">
							<span class="buy-tab-name"><?php esc_html_e( 'Kup przez Allegro', 'qutlet-theme' ); ?></span>
							<span class="buy-tab-sub"><?php
								/* translators: %s: formatted Allegro price. */
								echo esc_html( sprintf( __( '%s · korzyści Allegro', 'qutlet-theme' ), $allegro_price_text ) );
							?></span>
						</button>
					</div>
				</div>
			<?php endif; ?>

			<div data-buy-pane="qutlet">
				<div class="pd-price-row">
					<span class="pd-price"><?php echo wp_kses_post( wc_price( $sale_price ) ); ?></span>
					<?php if ( $has_market_price ) : ?>
						<span class="pd-old">
							<span class="strike"><?php
								/* translators: %s: formatted market price. */
								echo wp_kses_post( sprintf( __( 'Nowy w sklepach: %s', 'qutlet-theme' ), wc_price( $market_price ) ) );
							?></span>
							<span class="note"><?php esc_html_e( 'średnia rynkowa', 'qutlet-theme' ); ?></span>
						</span>
						<span class="pd-save"><?php
							$save_amount  = wc_price( $market_price - $sale_price );
							$save_percent = ProductPage::save_percent( $sale_price, $market_price );
							/* translators: 1: formatted saved amount, 2: percent saved. */
							echo wp_kses_post( sprintf( __( 'Oszczędzasz %1$s · -%2$d%%', 'qutlet-theme' ), $save_amount, $save_percent ) );
						?></span>
					<?php endif; ?>
				</div>

				<?php if ( null !== $condition_definition && '' !== trim( $condition_definition['dlaczego_taniej'] ) ) : ?>
				<div class="eco-note">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5a8a14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4M12 8h.01"></path></svg>
					<span><?php echo esc_html( $condition_definition['dlaczego_taniej'] ); ?></span>
				</div>
				<?php endif; ?>

				<?php woocommerce_template_single_add_to_cart(); ?>

				<p class="pd-fine"><?php
					if ( '' !== $condition_code && '' !== $claim_period_text ) {
						echo esc_html( sprintf(
							/* translators: 1: condition code (A/B/C/D), 2: formatted claim period (e.g. "1 rok"). */
							__( 'Produkt sprzedawany jako używany (Klasa %1$s) • Reklamacja: %2$s', 'qutlet-theme' ),
							$condition_code,
							$claim_period_text
						) );
					} elseif ( '' !== $claim_period_text ) {
						echo esc_html( sprintf(
							/* translators: %s: formatted claim period (e.g. "1 rok"). */
							__( 'Produkt sprzedawany jako używany • Reklamacja: %s', 'qutlet-theme' ),
							$claim_period_text
						) );
					} else {
						esc_html_e( 'Produkt sprzedawany jako używany.', 'qutlet-theme' );
					}
				?></p>

				<div class="perk-list">
					<div class="perk-row">
						<span class="perk-icon"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"></path><path d="M4 9h11a5 5 0 0 1 5 5v3"></path></svg></span>
						<?php esc_html_e( '14 dni na zwrot', 'qutlet-theme' ); ?>
						<span class="perk-tag"><?php esc_html_e( 'Koszt po Twojej stronie', 'qutlet-theme' ); ?></span>
					</div>
					<?php if ( '' !== $warranty_claim_text ) : ?>
					<div class="perk-row">
						<span class="perk-icon"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
						<?php echo esc_html( $warranty_claim_text ); ?>
					</div>
					<?php endif; ?>
					<div class="perk-row">
						<span class="perk-icon"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg></span>
						<?php esc_html_e( 'Wysyłka w 1 dzień roboczy', 'qutlet-theme' ); ?>
					</div>
				</div>

				<div class="warn-note">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8a5a00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path><path d="M14 2v6h6"></path></svg>
					<span><?php
						echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							/* translators: %s: bolded label. */
							__( '%s W razie zwrotu produktu kupionego w naszym sklepie, koszty przesyłki zwrotnej pokrywasz sam.', 'qutlet-theme' ),
							__( 'Polityka zwrotów:', 'qutlet-theme' )
						);
					?></span>
				</div>
			</div>

			<?php if ( $allegro_enabled ) : ?>
				<div data-buy-pane="allegro" data-allegro-only hidden>
					<div class="pd-price-row pd-price-row-allegro">
						<div>
							<span class="pd-price"><?php echo esc_html( $allegro_price_text ); ?></span>
							<div class="pd-price-note"><?php
								/* translators: %d: percent the Allegro price is higher. */
								echo esc_html( sprintf( __( 'Cena wyższa o ~%d%%', 'qutlet-theme' ), $allegro_markup_pct ) );
							?></div>
						</div>
						<span class="protect-pill"><?php esc_html_e( 'Ochrona kupujących', 'qutlet-theme' ); ?></span>
					</div>

					<div class="perk-list">
						<div class="perk-row">
							<span class="perk-icon perk-icon-allegro"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"></path><path d="M4 9h11a5 5 0 0 1 5 5v3"></path></svg></span>
							<?php esc_html_e( '14 dni na zwrot', 'qutlet-theme' ); ?>
							<span class="perk-tag perk-tag-green"><?php esc_html_e( 'Możliwy bezpłatny', 'qutlet-theme' ); ?></span>
						</div>
						<?php if ( '' !== $warranty_claim_text ) : ?>
						<div class="perk-row">
							<span class="perk-icon perk-icon-allegro"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
							<?php echo esc_html( $warranty_claim_text ); ?>
						</div>
						<?php endif; ?>
						<div class="perk-row">
							<span class="perk-icon perk-icon-allegro"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg></span>
							<?php esc_html_e( 'Wysyłka w 1 dzień roboczy', 'qutlet-theme' ); ?>
						</div>
					</div>

					<div class="ok-note"><?php
						echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							/* translators: %s: bolded label. */
							__( '%s Zwrot całkowicie bezpłatny przy wyborze Allegro Delivery oraz dla Allegrowiczów Smart.', 'qutlet-theme' ),
							__( 'Polityka zwrotów:', 'qutlet-theme' )
						);
					?></div>

					<a href="<?php echo esc_url( $allegro_url ); ?>" target="_blank" rel="noopener" class="btn-buy btn-buy-allegro"><?php
						/* translators: %s: formatted Allegro price. */
						echo esc_html( sprintf( __( 'Kup przez Allegro – %s', 'qutlet-theme' ), $allegro_price_text ) );
					?></a>
					<p class="pd-fine"><?php esc_html_e( 'Ochrona kupującego Allegro • Bezpłatna wysyłka i zwrot dla Allegrowiczów Smart', 'qutlet-theme' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $has_ship || $has_desc ) : ?>
	<div class="pd-tabs-section">
		<div class="pd-tabs">
			<?php if ( $has_ship ) : ?>
				<button type="button" class="pd-tab active" data-pd-tab="ship"><?php esc_html_e( 'Co w przesyłce', 'qutlet-theme' ); ?></button>
			<?php endif; ?>
			<?php if ( $has_desc ) : ?>
				<button type="button" class="pd-tab<?php echo $has_ship ? '' : ' active'; ?>" data-pd-tab="desc"><?php esc_html_e( 'Opis i specyfikacja', 'qutlet-theme' ); ?></button>
			<?php endif; ?>
		</div>

		<?php if ( $has_ship ) : ?>
			<div class="tab-pane" data-pd-pane="ship">
				<div class="ship-grid">
					<?php if ( $ship_carousel_items ) : ?>
						<div class="carousel">
							<div class="carousel-track" data-car-track>
								<?php foreach ( $ship_carousel_items as $item ) : ?>
									<div class="carousel-slide" data-car-label="<?php echo esc_attr( $item['label'] ); ?>">
										<?php echo wp_get_attachment_image( $item['image_id'], 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endforeach; ?>
							</div>
							<?php if ( count( $ship_carousel_items ) > 1 ) : ?>
								<button type="button" class="carousel-nav prev" data-car-prev aria-label="<?php esc_attr_e( 'Poprzednie', 'qutlet-theme' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg></button>
								<button type="button" class="carousel-nav next" data-car-next aria-label="<?php esc_attr_e( 'Następne', 'qutlet-theme' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg></button>
								<div class="carousel-dots">
									<?php foreach ( $ship_carousel_items as $index => $item ) : ?>
										<button
											type="button"
											class="carousel-dot<?php echo 0 === $index ? ' active' : ''; ?>"
											data-car-dot="<?php echo esc_attr( (string) $index ); ?>"
											aria-label="<?php
												/* translators: %d: photo number. */
												echo esc_attr( sprintf( __( 'Zdjęcie %d', 'qutlet-theme' ), $index + 1 ) );
											?>"
										></button>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<div class="carousel-caption" data-car-caption></div>
						</div>
					<?php endif; ?>

					<div class="included-card">
						<h3><?php esc_html_e( 'Co dokładnie otrzymasz', 'qutlet-theme' ); ?></h3>
						<ul class="included-list">
							<?php foreach ( $ship_items as $item ) : ?>
								<li>
									<?php if ( $item['included'] ) : ?>
										<span class="check-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span>
									<?php else : ?>
										<span class="cross-ico"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg></span>
									<?php endif; ?>
									<span><?php echo esc_html( $item['label'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
						<p class="included-note"><?php esc_html_e( 'Zawartość spisujemy ręcznie dla każdego egzemplarza. Jeśli czegoś brakuje na zdjęciu — nie ma tego w zestawie.', 'qutlet-theme' ); ?></p>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $has_desc ) : ?>
			<div class="tab-pane" data-pd-pane="desc"<?php echo $has_ship ? ' hidden' : ''; ?>>
				<div class="desc-grid">
					<?php if ( '' !== trim( $description_html ) ) : ?>
						<div>
							<h3><?php esc_html_e( 'O produkcie', 'qutlet-theme' ); ?></h3>
							<?php echo wp_kses_post( $description_html ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $specification_rows ) : ?>
						<div class="spec-table">
							<h4><?php esc_html_e( 'Specyfikacja', 'qutlet-theme' ); ?></h4>
							<?php foreach ( $specification_rows as $row ) : ?>
								<div class="spec-row"><span><?php echo esc_html( $row['label'] ); ?></span><span><?php echo esc_html( $row['value'] ); ?></span></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<div class="know" id="jak-to-dziala">
		<h2><?php esc_html_e( 'Wszystko, co warto wiedzieć przed zakupem', 'qutlet-theme' ); ?></h2>

		<div class="acc-item">
			<button type="button" class="acc-btn" data-acc-btn><?php esc_html_e( 'Klasyfikacja produktów', 'qutlet-theme' ); ?> <span class="sign">&minus;</span></button>
			<div class="acc-body" data-acc-body>
				<table class="class-table">
					<thead><tr>
						<th><?php esc_html_e( 'Klasa', 'qutlet-theme' ); ?></th>
						<th><?php esc_html_e( 'Stan wizualny', 'qutlet-theme' ); ?></th>
						<th><?php esc_html_e( 'Charakterystyka', 'qutlet-theme' ); ?></th>
					</tr></thead>
					<tbody>
						<?php foreach ( $condition_definitions as $row ) : ?>
							<tr>
								<td><span class="class-name"><span class="dot" style="background:<?php echo esc_attr( $row['kolor'] ); ?>"></span><?php echo esc_html( $row['opis_chip'] ); ?></span></td>
								<td><?php echo esc_html( $row['stan_wizualny'] ); ?></td>
								<td><?php echo esc_html( $row['charakterystyka'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="acc-item">
			<button type="button" class="acc-btn" data-acc-btn><?php esc_html_e( 'Dostawa i zwroty', 'qutlet-theme' ); ?> <span class="sign">+</span></button>
			<div class="acc-body" data-acc-body hidden>
				<div class="info-3col">
					<div class="info-card info-card-slim">
						<h4><span class="info-card-mini"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg></span><?php esc_html_e( 'Szybka wysyłka', 'qutlet-theme' ); ?></h4>
						<p><?php esc_html_e( 'Wysyłamy w najbliższy dzień roboczy (sesja rano/popołudnie).', 'qutlet-theme' ); ?><?php if ( $allegro_enabled ) : ?><span data-allegro-only> <?php esc_html_e( 'Dotyczy obu kanałów.', 'qutlet-theme' ); ?></span><?php endif; ?></p>
					</div>
					<div class="info-card info-card-slim info-card-amber">
						<h4><span class="info-card-mini"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"></path><path d="M4 9h11a5 5 0 0 1 5 5v3"></path></svg></span><?php
							if ( $allegro_enabled ) :
								?><span data-allegro-only><?php esc_html_e( 'Zwrot — nasz sklep', 'qutlet-theme' ); ?></span><?php
							else :
								?><span data-allegro-off-only><?php esc_html_e( 'Zwrot — 14 dni', 'qutlet-theme' ); ?></span><?php
							endif;
						?></h4>
						<p><?php
							echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								/* translators: %s: bolded phrase. */
								__( '14 dni na zmianę zdania. %s', 'qutlet-theme' ),
								__( 'Koszt przesyłki zwrotnej po stronie kupującego.', 'qutlet-theme' )
							);
						?></p>
					</div>
					<?php if ( $allegro_enabled ) : ?>
						<div class="info-card info-card-slim info-card-green" data-allegro-only>
							<h4><span class="info-card-mini"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"></path><path d="M4 9h11a5 5 0 0 1 5 5v3"></path></svg></span><?php esc_html_e( 'Zwrot — Allegro', 'qutlet-theme' ); ?></h4>
							<p><?php
								echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									/* translators: %s: bolded phrase. */
									__( '14 dni na zmianę zdania. %s — przy wyborze Allegro Delivery lub abonamentu Smart.', 'qutlet-theme' ),
									__( 'Zwrot bezpłatny', 'qutlet-theme' )
								);
							?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="acc-item">
			<button type="button" class="acc-btn" data-acc-btn><?php esc_html_e( 'Gwarancja i reklamacje', 'qutlet-theme' ); ?> <span class="sign">+</span></button>
			<div class="acc-body" data-acc-body hidden>
				<div class="info-2col">
					<div class="info-card">
						<span class="info-card-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"></path></svg></span>
						<h4><?php esc_html_e( 'Gwarancja sprzedawcy', 'qutlet-theme' ); ?></h4>
						<p><?php
							$warranty_months_text = ProductPage::period_months_text( $warranty_months );

							if ( '' !== $warranty_months_text ) {
								echo esc_html( sprintf(
									/* translators: %s: formatted warranty period (e.g. "12 miesięcy"). */
									__( '%s gwarancji na każdy produkt. Reklamacje realizujemy w naszym serwisie — szybko i bezproblemowo.', 'qutlet-theme' ),
									$warranty_months_text
								) );
							} else {
								esc_html_e( 'Okres gwarancji znajdziesz w opisie klasy stanu produktu.', 'qutlet-theme' );
							}
						?></p>
					</div>
					<div class="info-card">
						<span class="info-card-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path><path d="M14 2v6h6"></path><path d="m9 15 2 2 4-4"></path></svg></span>
						<h4><?php esc_html_e( 'Prawo do reklamacji', 'qutlet-theme' ); ?></h4>
						<p><?php
							if ( '' === $claim_period_text ) {
								esc_html_e( 'Okres reklamacji znajdziesz w opisie klasy stanu produktu.', 'qutlet-theme' );
							} elseif ( $claim_months >= 24 ) {
								// Rękojmia ustawowa PEŁNA (2 lata) — klasa nie "skraca" niczego (D-12.G1: decyzja
								// liczbowa, nie `if ($condition_code === 'Nowe')`; patrz docblock SeedClassDefinitionsCommand).
								echo esc_html( sprintf(
									/* translators: %s: formatted claim period (e.g. "2 lata"). */
									__( '%s — zgodnie z ustawowym prawem rękojmi.', 'qutlet-theme' ),
									$claim_period_text
								) );
							} else {
								echo esc_html( sprintf(
									/* translators: %s: formatted claim period (e.g. "1 rok"). */
									__( '%s (zamiast ustawowych 2 lat — dopuszczalne dla towarów używanych, gdy kupujący zostanie wyraźnie poinformowany).', 'qutlet-theme' ),
									$claim_period_text
								) );
							}
						?></p>
					</div>
				</div>
				<p class="know-fine"><?php
					echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						/* translators: %s: bolded word. */
						__( 'Wszystkie produkty w Qutlet sprzedawane są jako %s. Gwarancja i prawo do reklamacji są identyczne dla każdego egzemplarza.', 'qutlet-theme' ),
						__( 'używane', 'qutlet-theme' )
					);
				?></p>
			</div>
		</div>

		<?php if ( $allegro_enabled ) : ?>
			<div class="acc-item" data-allegro-only>
				<button type="button" class="acc-btn acc-btn-allegro" data-acc-btn><?php esc_html_e( 'Kupuj przez Allegro', 'qutlet-theme' ); ?> <span class="sign">+</span></button>
				<div class="acc-body" data-acc-body hidden>
					<div class="allegro-card">
						<div>
							<h3><?php esc_html_e( 'Nasze produkty kupisz też na Allegro', 'qutlet-theme' ); ?></h3>
							<p class="lead"><?php esc_html_e( 'Jesteśmy nowym sklepem i wiemy, że zaufanie trzeba zbudować. Dlatego oferujemy alternatywę — ten sam produkt możesz kupić przez Allegro, z pełną ochroną kupującego.', 'qutlet-theme' ); ?></p>
							<a href="<?php echo esc_url( $allegro_url ); ?>" target="_blank" rel="noopener" class="btn-allegro"><?php esc_html_e( 'Odwiedź nas na Allegro → Qutlet', 'qutlet-theme' ); ?></a>
							<p class="allegro-fine"><?php
								echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									/* translators: %s: bolded shop name. */
									__( 'Nasz oficjalny sklep na Allegro to %s. Szukaj nas po tej nazwie.', 'qutlet-theme' ),
									__( 'Qutlet', 'qutlet-theme' )
								);
							?></p>
						</div>
						<div>
							<div class="allegro-step"><span class="allegro-step-num">1</span><span><?php
								echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									/* translators: %s: bolded phrase. */
									__( '%s — Allegro pobiera prowizję, którą musimy uwzględnić. W naszym sklepie kupujesz taniej.', 'qutlet-theme' ),
									__( 'Cena może być nieco wyższa', 'qutlet-theme' )
								);
							?></span></div>
							<div class="allegro-step"><span class="allegro-step-num">2</span><span><?php
								echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									/* translators: %s: bolded phrase. */
									__( '%s — jeśli chcesz mieć dodatkowe poczucie bezpieczeństwa, Allegro chroni Twoje zakupy niezależnie od nas.', 'qutlet-theme' ),
									__( 'Program Ochrony Kupujących', 'qutlet-theme' )
								);
							?></span></div>
							<div class="allegro-step"><span class="allegro-step-num">3</span><span><?php
								echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									/* translators: %s: bolded phrase. */
									__( '%s — wybierając tę opcję dostawy, zwracasz produkt zawsze bezpłatnie. Członkowie programu Smart mają dostawy i zwroty za darmo.', 'qutlet-theme' ),
									__( 'Darmowe zwroty z Allegro Delivery', 'qutlet-theme' )
								);
							?></span></div>
							<div class="allegro-step"><span class="allegro-step-num">4</span><span><?php
								echo ProductPage::bold_text( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									/* translators: %s: bolded phrase. */
									__( '%s — nie namawiamy na żadną opcję. Oba kanały to ten sam produkt, ta sama gwarancja, ta sama obsługa.', 'qutlet-theme' ),
									__( 'Wybór należy do Ciebie', 'qutlet-theme' )
								);
							?></span></div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

</div><!-- /.wrap -->

<div class="buybar" data-buybar>
	<div class="wrap buybar-inner">
		<div class="buybar-product">
			<div class="buybar-thumb"><?php
				if ( $image_ids ) {
					echo wp_get_attachment_image( $image_ids[0], 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			?></div>
			<div class="minw0">
				<div class="buybar-title"><?php echo esc_html( get_the_title() ); ?></div>
				<div class="buybar-price-row">
					<?php if ( '' !== $condition_label ) : ?>
						<span class="buybar-class"><?php
							/* translators: %s: condition code (A/B/C/D). */
							echo esc_html( sprintf( __( 'Klasa %s', 'qutlet-theme' ), $condition_code ) );
						?></span>
					<?php endif; ?>
					<span class="buybar-price" data-buybar-price><?php echo esc_html( $sale_price_text ); ?></span>
					<?php if ( $has_market_price ) : ?>
						<span class="buybar-old" data-buybar-qutlet><?php echo esc_html( ProductPage::price_text( $market_price ) ); ?></span>
					<?php endif; ?>
					<?php if ( $allegro_enabled ) : ?>
						<span class="buybar-allegro-note" data-buybar-allegro hidden><?php esc_html_e( 'na Allegro', 'qutlet-theme' ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
			<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" form="qutlet-add-to-cart-form" class="btn btn-primary" data-buybar-qutlet>
				<?php esc_html_e( 'Dodaj', 'qutlet-theme' ); ?> <span><?php esc_html_e( 'do koszyka', 'qutlet-theme' ); ?></span>
			</button>
		<?php endif; ?>
		<?php if ( $allegro_enabled ) : ?>
			<a href="<?php echo esc_url( $allegro_url ); ?>" target="_blank" rel="noopener" class="btn-buybar-allegro" data-buybar-allegro data-allegro-only hidden><?php esc_html_e( 'Kup przez Allegro', 'qutlet-theme' ); ?></a>
		<?php endif; ?>
	</div>
</div>
</div>
