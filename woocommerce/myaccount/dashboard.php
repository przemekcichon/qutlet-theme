<?php
/**
 * My Account dashboard (nadpisanie `woocommerce/templates/myaccount/dashboard.php`,
 * port „Pulpit" `design/vanilla/moje-konto.html:42-62`).
 *
 * Kafelek „Metody płatności" z prototypu świadomie POMINIĘTY (patrz nagłówek
 * `inc/features/Account/Account.php`) — zostają dwa kafelki: Zamówienia,
 * Adres dostawy.
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

use Qutlet\Theme\features\Account\Account;

$current_user = wp_get_current_user();
$first_name   = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
$orders_count = wc_get_customer_order_count( $current_user->ID );
?>

<h2>
	<?php
	printf(
		/* translators: %s: imię klienta (lub nazwa wyświetlana, gdy imię nieustawione). */
		esc_html__( 'Cześć, %s! 👋', 'qutlet-theme' ),
		esc_html( $first_name )
	);
	?>
</h2>
<p class="pane-lead"><?php esc_html_e( 'Tu znajdziesz swoje zamówienia, dane i ustawienia konta.', 'qutlet-theme' ); ?></p>

<div class="tile-grid">
	<a class="tile" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
		<span class="tile-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"></path><path d="M1 3h22v5H1z"></path><path d="M10 12h4"></path></svg></span>
		<h4><?php esc_html_e( 'Zamówienia', 'qutlet-theme' ); ?></h4>
		<p>
			<?php
			printf(
				/* translators: %d: liczba złożonych zamówień. */
				esc_html( _n( '%d zamówienie', '%d zamówień', $orders_count, 'qutlet-theme' ) ),
				(int) $orders_count
			);
			?>
		</p>
	</a>
	<a class="tile" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'shipping', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
		<span class="tile-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg></span>
		<h4><?php esc_html_e( 'Adres dostawy', 'qutlet-theme' ); ?></h4>
		<p><?php echo esc_html( Account::address_short_label( (int) $current_user->ID ) ); ?></p>
	</a>
</div>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );
