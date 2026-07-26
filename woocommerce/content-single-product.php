<?php
/**
 * Qutlet — treść strony produktu (port design/vanilla/produkt.html).
 *
 * Nadpisuje domyślny szablon WooCommerce
 * (woocommerce/templates/content-single-product.php) — theme owns the
 * customer-facing render (D-8.G1), core tylko dostarcza dane (ACF + Woo).
 *
 * P-8.2a: układ + galeria + nagłówek + klasa stanu + ceny (`now`/`old`,
 * rabat). Taby zakupu (kanał Qutlet/Allegro) i buybar są poza zakresem —
 * dochodzą w P-8.2b. Sekcja treści/specyfikacji (zakładki „Co w przesyłce" /
 * „Opis i specyfikacja") dochodzi w P-8.2c.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\ProductPage\ProductPage;

defined( 'ABSPATH' ) || exit;

global $product;

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$product_id       = $product->get_id();
$condition_code   = (string) ProductPage::acf_field( 'klasa_stanu', $product_id );
$condition_label  = '' !== $condition_code ? ProductPage::condition_label( $condition_code ) : '';
$market_price     = (float) ProductPage::acf_field( 'cena_rynkowa_nowego', $product_id );
$sale_price       = (float) $product->get_price();
$has_market_price = $market_price > 0.0;

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
			<?php if ( '' !== $condition_label ) : ?>
				<div class="class-row">
					<span class="class-pill"><b><?php
						/* translators: %s: condition code (A/B/C/D). */
						echo esc_html( sprintf( __( 'Klasa %s', 'qutlet-theme' ), $condition_code ) );
					?></b> · <?php echo esc_html( $condition_label ); ?></span>
					<a href="#jak-to-dziala" class="class-link"><?php esc_html_e( 'Co to znaczy?', 'qutlet-theme' ); ?></a>
				</div>
			<?php endif; ?>

			<h1 class="pd-title"><?php the_title(); ?></h1>

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
		</div>
	</div>

</div>
</div>
