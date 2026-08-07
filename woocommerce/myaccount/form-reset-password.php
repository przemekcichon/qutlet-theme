<?php
/**
 * Lost password reset form (nadpisanie `woocommerce/templates/myaccount/form-reset-password.php`) —
 * ekran po kliknięciu linku z e-maila (`?action=rp&key=…&login=…` pod
 * endpointem `zapomniane-haslo`, patrz nagłówek `form-lost-password.php`).
 * BEZ odpowiednika w prototypie — ta sama rodzina wizualna `.auth-*`.
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="auth-wrap">
	<div class="auth-card">
		<form method="post" class="auth-form woocommerce-ResetPassword lost_reset_password">
			<h1><?php esc_html_e( 'Ustaw nowe hasło', 'qutlet-theme' ); ?></h1>
			<p class="sub">
				<?php echo esc_html( apply_filters( 'woocommerce_reset_password_message', __( 'Wpisz nowe hasło poniżej.', 'qutlet-theme' ) ) ); ?>
			</p>

			<div class="field">
				<label for="password_1"><?php esc_html_e( 'Nowe hasło', 'qutlet-theme' ); ?></label>
				<input type="password" class="input-text" name="password_1" id="password_1" autocomplete="new-password" required aria-required="true" placeholder="<?php esc_attr_e( 'Minimum 6 znaków', 'qutlet-theme' ); ?>" />
			</div>
			<div class="field">
				<label for="password_2"><?php esc_html_e( 'Powtórz nowe hasło', 'qutlet-theme' ); ?></label>
				<input type="password" class="input-text" name="password_2" id="password_2" autocomplete="new-password" required aria-required="true" />
			</div>

			<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
			<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

			<?php do_action( 'woocommerce_resetpassword_form' ); ?>

			<input type="hidden" name="wc_reset_password" value="true" />
			<button type="submit" class="btn btn-primary btn-lg btn-block"><?php esc_html_e( 'Zapisz', 'qutlet-theme' ); ?></button>

			<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
		</form>
	</div>
</div>

<?php do_action( 'woocommerce_after_reset_password_form' ); ?>
