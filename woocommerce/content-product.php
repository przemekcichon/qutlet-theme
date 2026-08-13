<?php
/**
 * Qutlet — karta produktu w pętli (port design/vanilla/js/templates.js
 * QT.tpl.productCard() → woocommerce/content-product.php, komentarz templates.js:6).
 *
 * Nadpisuje domyślny szablon WooCommerce (woocommerce/templates/content-product.php)
 * — theme owns the customer-facing render (D-8.G1), core/Woo tylko dostarcza dane.
 *
 * P-8.3a: karta (pętla) + etykieta liczby sztuk (`pcard-stock`, przeniesiona
 * z P-8.2a — ground-truth `produkt.html` potwierdził, że QT.qtyLabel renderuje
 * się WYŁĄCZNIE tutaj, kontrakt §1/§6). Reużywa `ProductPage::acf_field()` /
 * `condition_label()` / `save_percent()` (klasa stanu + rabat — te same pola
 * i formuły co strona produktu, P-8.2a). Kropka klasy (P-12.3):
 * `ProductPage::condition_color()` zamiast statycznej klasy CSS `.dot-<kod>`
 * (byt rozszerzalny, D-12.G1 — klasa „Nowe" nie miała gotowej reguły `.dot-nowe`).
 *
 * Ground-truth runtime (P-8.3a): dla archiwów renderowanych przez blok
 * `wp:woocommerce/legacy-template {"template":"archive-product"}`
 * (`templates/taxonomy-product_cat.html`), WooCommerce Blocks
 * (`ClassicTemplate::render_archive_product()`) hardkoduje nagłówek archiwum
 * WPROST w PHP wtyczki — theme nie ma tam punktu nadpisania (stąd
 * `woocommerce/archive-product.php` NIE istnieje, byłby martwym kodem).
 * Sama pętla (wrapper `<ul>`/`<div>` + ten partial) JEST jednak respektowana:
 * `woocommerce_product_loop_start()`/`_end()` wołają `wc_get_template()`, więc
 * `woocommerce/loop/loop-start.php`/`loop-end.php` zastępują `<ul class="products">`
 * siatką `.grid-3` — karta może więc zostać czystym `<a class="pcard">`, bez
 * opakowania w `<li>`, dokładnie jak w prototypie.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\ProductCard\ProductCard;
use Qutlet\Theme\features\ProductPage\ProductPage;

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$product_id  = $product->get_id();
$image_id    = $product->get_image_id();

$condition_code  = (string) ProductPage::acf_field( 'klasa_stanu', $product_id );
$condition_label = '' !== $condition_code ? ProductPage::condition_label( $condition_code ) : '';

$sale_price       = (float) $product->get_price();
$market_price     = (float) ProductPage::acf_field( 'cena_rynkowa_nowego', $product_id );
$has_market_price = $market_price > 0.0;
?>
<a id="product-<?php echo esc_attr( (string) $product_id ); ?>" <?php wc_product_class( 'pcard', $product ); ?> href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
	<div class="pcard-media">
		<?php if ( $image_id ) : ?>
			<?php echo wp_get_attachment_image( $image_id, 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="ph-stripes">[ <?php esc_html_e( 'brak zdjęcia', 'qutlet-theme' ); ?> ]</div>
		<?php endif; ?>
	</div>
	<div class="pcard-body">
		<span class="pcard-stock"><?php echo esc_html( ProductCard::qty_label( $product ) ); ?></span>
		<h3 class="pcard-title"><?php echo esc_html( get_the_title( $product_id ) ); ?></h3>
		<?php if ( '' !== $condition_label ) : ?>
			<div class="pcard-cond">
				<span class="dot" style="background:<?php echo esc_attr( ProductPage::condition_color( $condition_code ) ); ?>"></span><?php
					/* translators: 1: condition code (A/B/C/D), 2: condition label. */
					echo esc_html( sprintf( __( 'Klasa %1$s · %2$s', 'qutlet-theme' ), $condition_code, $condition_label ) );
				?>
			</div>
		<?php endif; ?>
		<div class="pcard-price-row">
			<div class="pcard-price-col">
				<span class="price-now"><?php echo wp_kses_post( wc_price( $sale_price ) ); ?></span>
				<?php if ( $has_market_price ) : ?>
					<span class="price-old-line">
						<span class="price-old"><?php
							/* translators: %s: formatted market price. */
							echo wp_kses_post( sprintf( __( '%s nowy', 'qutlet-theme' ), wc_price( $market_price ) ) );
						?></span>
						<span class="price-save"><?php
							echo esc_html( '-' . ProductPage::save_percent( $sale_price, $market_price ) . '%' );
						?></span>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="pcard-foot">
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"></path><path d="M4 9h11a5 5 0 0 1 5 5v3"></path></svg>
		<?php esc_html_e( 'Zwrot 14-dniowy', 'qutlet-theme' ); ?>
	</div>
</a>
