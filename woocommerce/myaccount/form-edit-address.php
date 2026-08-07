<?php
/**
 * Edit address form (nadpisanie `woocommerce/templates/myaccount/form-edit-address.php`,
 * port „Adres dostawy" `design/vanilla/moje-konto.html:92-107`, karta `.form-card`).
 *
 * `wc_ship_to_billing_address_only()` zwraca FALSE na tej instalacji
 * (`woocommerce_ship_to_destination = 'billing'`, nie `'billing_only'`) —
 * WC natywnie rozróżnia adres rozliczeniowy i wysyłkowy (`my-address.php`,
 * dwie karty). Nawigacja konta (`Account::header_fragments()`/`navigation.php`)
 * kieruje na `edit-address/shipping` wprost — jeden adres „dostawy" z
 * prototypu to więc adres WYSYŁKOWY WC, bez dotykania `woocommerce_ship_to_destination`
 * (opcja współdzielona z Kasą, P-8.6b — poza zakresem tej poprawki). Adres
 * rozliczeniowy zostaje osiągalny natywnie pod `/edytuj-adres/` (index
 * `my-address.php`, nieprzeportowany — znany brak, prototyp nie ma dla niego
 * odpowiednika).
 *
 * Pola BEZ przycinania (company/phone/country/state zostają, jeśli
 * `WC()->countries->get_address_fields()` je zwróci) — tylko restyling,
 * żeby nie utracić danych wymaganych do wysyłki/faktury.
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? __( 'Adres rozliczeniowy', 'qutlet-theme' ) : __( 'Adres dostawy', 'qutlet-theme' );

do_action( 'woocommerce_before_edit_account_address_form' );
?>

<?php if ( ! $load_address ) : ?>

	<?php wc_get_template( 'myaccount/my-address.php' ); ?>

<?php else : ?>

	<div class="form-card">
		<form method="post" novalidate>
			<h3 class="step-title"><?php echo esc_html( apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ) ); ?></h3>

			<div class="form-grid">
				<?php
				do_action( "woocommerce_before_edit_address_form_{$load_address}" );

				$full_width_keys = array( 'company', 'address_1', 'address_2' );

				foreach ( $address as $key => $field ) {
					$suffix           = preg_replace( '/^(billing|shipping)_/', '', $key );
					$field['class']   = in_array( $suffix, $full_width_keys, true ) ? array( 'field', 'field-full' ) : array( 'field' );
					$field['label_class'] = array();

					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				}

				do_action( "woocommerce_after_edit_address_form_{$load_address}" );
				?>
			</div>

			<button type="submit" class="btn btn-dark" name="save_address" value="<?php esc_attr_e( 'Zapisz adres', 'qutlet-theme' ); ?>"><?php esc_html_e( 'Zapisz adres', 'qutlet-theme' ); ?></button>
			<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
			<input type="hidden" name="action" value="edit_address" />
		</form>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
