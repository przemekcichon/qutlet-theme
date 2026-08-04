<?php
/**
 * Blok `qutlet/article-product` — wstawka produktowa w treści artykułu
 * (`.art-product`, P-11.5), port `design/vanilla/blog-artykul.html:122-130`
 * (komentarz `→ shortcode / blok produktu Woo`). W odróżnieniu od
 * pozostałych 15 bloków bloga (P-11.3, wszystkie loop-driven/kontekstowe,
 * `"inserter": false`) TEN blok redaktor wstawia RĘCZNIE do treści wpisu —
 * atrybut `productId` (ID realnego produktu Woo) wybiera się w panelu
 * bocznym edytora (patrz `assets/js/blog-blocks-editor.js`).
 *
 * Cena reużywa TEGO SAMEGO odczytu co karta produktu (`woocommerce/content-product.php`,
 * P-8.3a) — `$product->get_price()` (Woo, sprzedaż) i
 * `ProductPage::acf_field('cena_rynkowa_nowego', ...)` (ACF, odniesienie
 * „nowy w sklepach", kontrakt §2/§6, opcjonalne — brak ceny rynkowej ukrywa
 * przekreśloną linię). Link CTA to REALNY permalink produktu (`get_permalink()`),
 * nie statyczny link do kategorii jak w prototypie — plan (P-11.5) wprost
 * wymaga, żeby blok czytał „cenę/nazwę/LINK" realnego produktu.
 *
 * @package Qutlet\Theme
 *
 * @var array{productId?: int} $attributes
 */

declare( strict_types=1 );

use Qutlet\Theme\features\ProductPage\ProductPage;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_product' ) ) {
	return;
}

$product_id = isset( $attributes['productId'] ) ? (int) $attributes['productId'] : 0;

if ( $product_id <= 0 ) {
	return;
}

$product = wc_get_product( $product_id );

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$image_id         = $product->get_image_id();
$sale_price       = (float) $product->get_price();
$market_price     = (float) ProductPage::acf_field( 'cena_rynkowa_nowego', $product_id );
$has_market_price = $market_price > 0.0;
?>
<div class="art-product">
	<div class="art-product-thumb">
		<?php if ( $image_id ) : ?>
			<?php echo wp_get_attachment_image( (int) $image_id, 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="ph-stripes">[ <?php esc_html_e( 'brak zdjęcia', 'qutlet-theme' ); ?> ]</div>
		<?php endif; ?>
	</div>
	<div class="art-product-info">
		<div class="art-product-kicker"><?php esc_html_e( 'Wolisz gotowe? Mamy sprawdzone', 'qutlet-theme' ); ?></div>
		<div class="art-product-name"><?php echo esc_html( get_the_title( $product_id ) ); ?></div>
		<div class="art-product-price">
			<?php if ( $has_market_price ) : ?>
				<s><?php echo wp_kses_post( wc_price( $market_price ) ); ?></s>
			<?php endif; ?>
			<?php
			/* translators: %s: cena sprzedaży (formatowana wc_price()). */
			echo wp_kses_post( sprintf( __( '%s · przetestowany, 12 mies. gwarancji', 'qutlet-theme' ), wc_price( $sale_price ) ) );
			?>
		</div>
	</div>
	<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Zobacz produkt', 'qutlet-theme' ); ?></a>
</div>
