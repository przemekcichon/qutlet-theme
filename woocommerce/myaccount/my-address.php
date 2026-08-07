<?php
/**
 * My Addresses (nadpisanie `woocommerce/templates/myaccount/my-address.php`) —
 * index adresu rozliczeniowego/wysyłkowego BEZ odpowiednika w prototypie
 * (`moje-konto.html` ma jedną kartę „Adres dostawy", nawigacja kieruje wprost
 * na `edit-address/shipping`, patrz nagłówek `form-edit-address.php`). Ten
 * plik jest osiągalny tylko przy bezpośrednim wejściu na `/edytuj-adres/`
 * (bez podtypu) — lekki restyling `.form-card`, żeby nie wyglądał jak
 * niedokończona strona, bez próby dopasowania 1:1 do prototypu (znany brak).
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Adres rozliczeniowy', 'qutlet-theme' ),
			'shipping' => __( 'Adres dostawy', 'qutlet-theme' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array( 'billing' => __( 'Adres rozliczeniowy', 'qutlet-theme' ) ),
		$customer_id
	);
}
?>

<p class="pane-lead"><?php esc_html_e( 'Te adresy będą domyślnie podpowiadane w kasie.', 'qutlet-theme' ); ?></p>

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
	<?php $address = wc_get_account_formatted_address( $name ); ?>
	<div class="form-card">
		<header class="woocommerce-Address-title title">
			<h3 class="step-title">
				<?php echo esc_html( $address_title ); ?>
				<a class="text-btn-accent" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>">
					<?php echo esc_html( $address ? __( 'Edytuj', 'qutlet-theme' ) : __( 'Dodaj', 'qutlet-theme' ) ); ?>
				</a>
			</h3>
		</header>
		<address>
			<?php
			echo $address ? wp_kses_post( $address ) : esc_html__( 'Nie ustawiono jeszcze tego adresu.', 'qutlet-theme' );

			/**
			 * Used to output content after core address fields.
			 *
			 * @since 8.7.0
			 */
			do_action( 'woocommerce_my_account_after_my_address', $name );
			?>
		</address>
	</div>
<?php endforeach; ?>
