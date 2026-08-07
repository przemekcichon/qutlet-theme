<?php
/**
 * My Account orders (nadpisanie `woocommerce/templates/myaccount/orders.php`,
 * port `.order-card`, `design/vanilla/js/templates.js:126-141` — prototyp
 * renderował listę zamówień z fejkowego `localStorage`, tu realne
 * `$customer_orders` przekazane przez `woocommerce_account_orders()`).
 *
 * Statusy: etykieta ZAWSZE natywna WC (`wc_get_order_status_name()`) — w tym
 * własny status `shipped`/„Wysłane" zarejestrowany przez qutlet-core
 * (patrz nagłówek `Account::status_pill_class()`), kolor pigułki z tej samej
 * metody.
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

use Qutlet\Theme\features\Account\Account;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>

<?php if ( $has_orders ) : ?>

	<?php foreach ( $customer_orders->orders as $customer_order ) : ?>
		<?php
		$order = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		if ( ! $order instanceof WC_Order ) {
			continue;
		}
		?>
		<div class="order-card">
			<div class="order-card-head">
				<div>
					<div class="order-no">
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
							<?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
						</a>
					</div>
					<div class="order-date">
						<?php
						printf(
							/* translators: %s: data złożenia zamówienia. */
							esc_html__( 'Złożone %s', 'qutlet-theme' ),
							esc_html( wc_format_datetime( $order->get_date_created() ) )
						);
						?>
					</div>
				</div>
				<span class="status-pill <?php echo esc_attr( Account::status_pill_class( $order->get_status() ) ); ?>">
					<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
				</span>
			</div>

			<div class="order-card-items">
				<?php foreach ( $order->get_items() as $item ) : ?>
					<?php
					$product   = $item->get_product();
					$image_id  = $product ? $product->get_image_id() : 0;
					$image_url = $image_id ? wp_get_attachment_image_url( (int) $image_id, 'thumbnail' ) : '';
					?>
					<div
						class="cart-thumb cart-thumb-sm"
						title="<?php echo esc_attr( $item->get_name() ); ?>"
						<?php echo $image_url ? ' style="background-image:url(' . esc_url( $image_url ) . ')"' : ''; ?>
					></div>
				<?php endforeach; ?>
			</div>

			<div class="order-card-foot">
				<span class="order-total">
					<?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
				</span>
			</div>
		</div>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<div class="empty-state">
		<h3><?php esc_html_e( 'Brak zamówień', 'qutlet-theme' ); ?></h3>
		<p><?php esc_html_e( 'Twoje przyszłe zakupy pojawią się tutaj.', 'qutlet-theme' ); ?></p>
		<a class="btn btn-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'Przeglądaj produkty', 'qutlet-theme' ); ?></a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
