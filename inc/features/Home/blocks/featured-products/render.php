<?php
/**
 * Blok `qutlet/featured-products` — sekcja „Świeżo na wyprzedaży" strony
 * głównej (P-11.4). Port `data-featured-grid` (design/vanilla/index.html:93) —
 * pętla WP_Query po wyróżnionych produktach (kontrakt danych §1: flaga
 * „featured" Woo, `product_visibility` term `featured`).
 *
 * Karty reużywają BEZPOŚREDNIO `woocommerce/content-product.php` (ten sam
 * partial co archiwum kategorii, P-8.3a) — czyta wyłącznie explicit
 * `$product`/ID-based helpery (get_the_title($id), $product->get_image_id()
 * itd.), bez zależności od global $post/the_post(), więc wystarczy podstawić
 * `global $product` per iterację (bez WP_Query/setup_postdata).
 *
 * Puste wyniki (dziś: brak produktów oznaczonych jako „featured" w tej
 * instalacji) → sekcja znika całkowicie, wzorem `qutlet/related-posts`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$featured_products = wc_get_products(
	array(
		'status'       => 'publish',
		'stock_status' => 'instock',
		'featured'     => true,
		'orderby'      => 'date',
		'order'        => 'DESC',
		'limit'        => 4,
	)
);

if ( empty( $featured_products ) ) {
	return;
}
?>
<section class="wrap home-section">
	<div class="section-head">
		<div>
			<h2 class="section-title"><?php esc_html_e( 'Świeżo na wyprzedaży', 'qutlet-theme' ); ?></h2>
			<p class="section-sub"><?php esc_html_e( 'Pojedyncze egzemplarze sprawdzone przez nasz serwis. Pierwszy kupujący wygrywa.', 'qutlet-theme' ); ?></p>
		</div>
		<a href="/strefa-okazji/" class="link-accent">
			<?php esc_html_e( 'Cała strefa okazji', 'qutlet-theme' ); ?>
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
		</a>
	</div>
	<div class="grid-4">
		<?php
		global $product;
		$qutlet_product_backup = $product;

		foreach ( $featured_products as $featured_product ) {
			$product = $featured_product;
			wc_get_template_part( 'content', 'product' );
		}

		$product = $qutlet_product_backup;
		?>
	</div>
</section>
