<?php
/**
 * Qutlet — przycisk „Dodaj do koszyka" (port stylu design/vanilla/produkt.html
 * `.btn-buy`), nadpisanie natywnego szablonu WooCommerce
 * (woocommerce/templates/single-product/add-to-cart/simple.php, wersja 10.2.0
 * zainstalowanej wtyczki — zweryfikowane źródło, nie pamięć).
 *
 * Formularz i logika (nonce, walidacja, przekierowanie, komunikaty) to
 * WYŁĄCZNIE natywny mechanizm WooCommerce
 * (`WC_Form_Handler::add_to_cart_action()`, `woocommerce_template_single_add_to_cart()`)
 * — theme go nie duplikuje (D-8.G1: theme tylko renderuje). Zmienione
 * względem oryginału:
 * 1) markup przycisku (ikona + `.btn-buy`) i `id` na `<form>`, żeby sticky
 *    buybar (`woocommerce/content-single-product.php`) mógł submitować TEN
 *    formularz przyciskiem POZA nim (atrybut HTML5
 *    `form="qutlet-add-to-cart-form"`, bez JS);
 * 2) `woocommerce_quantity_input()` owinięte w `! $product->is_sold_individually()`
 *    — TO JEST DODATEK tego PR-a, NIE natywne zachowanie oryginalnego
 *    szablonu (który woła stepper bezwarunkowo). Dodane, bo
 *    `design/vanilla/produkt.html` nie przewiduje żadnego selektora ilości
 *    na stronie produktu (jednosztukowy outlet). Gdy produkt ma
 *    `sold_individually=false`, natywny input i tak się renderuje (musi
 *    istnieć w DOM — jedyne pole `quantity`, które faktycznie submituje się
 *    z formularzem), ale `.pd-stock ~ .cart .quantity` w `style.css` (P-22.3)
 *    chowa go wizualnie: `woocommerce/content-single-product.php` renderuje
 *    WŁASNY custom stepper (`.pd-stepper`, `assets/js/product-stock-stepper.js`)
 *    tuż nad tym formularzem i syncuje jego wartość do tego ukrytego inputa —
 *    zero duplikacji logiki submit/walidacji (D-8.G1).
 * 3) natywny `wc_get_stock_html()` renderuje się TYLKO gdy produkt jest OUT
 *    OF STOCK — dla in-stock tę rolę przejął `.pd-stock`
 *    (`content-single-product.php`, P-22.3), więc podwójny badge byłby
 *    duplikacją tej samej informacji dwoma różnymi stylami.
 *
 * @package Qutlet\Theme
 * @see     https://woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

if ( ! $product->is_in_stock() ) {
	echo wc_get_stock_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( $product->is_in_stock() ) :
	?>
	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form id="qutlet-add-to-cart-form" class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );

		if ( ! $product->is_sold_individually() ) {
			woocommerce_quantity_input(
				array(
					'min_value'   => $product->get_min_purchase_quantity(),
					'max_value'   => $product->get_max_purchase_quantity(),
					'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
				)
			);
		}

		do_action( 'woocommerce_after_add_to_cart_quantity' );
		?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="btn-buy" data-buy-anchor>
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 2-1.58l1.65-7.42H5.12"></path></svg>
			<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
		</button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
	<?php
endif;
