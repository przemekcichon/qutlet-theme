<?php
/**
 * Lost password form (nadpisanie `woocommerce/templates/myaccount/form-lost-password.php`).
 *
 * BEZ odpowiednika w prototypie (`design/vanilla` nie ma ekranu
 * „zapomniałem hasła") — restyling na `.auth-wrap`/`.auth-card`/`.auth-form`
 * (te same klasy co `form-login.php`, spójna rodzina wizualna), nie
 * dopasowanie 1:1 do nieistniejącego wzorca. Endpoint pod polskim slugiem
 * (`woocommerce_myaccount_lost_password_endpoint` = `zapomniane-haslo`, nie
 * domyślne `lost-password` — ten sam wzorzec co D-8.6c.3/D-8.6a.2/D-8.6b.3,
 * `wc_lostpassword_url()`/`wc_get_endpoint_url()` czytają opcję dynamicznie,
 * nigdzie w motywie nie ma zaszytego literału starego sluga).
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="auth-wrap">
	<div class="auth-card">
		<form method="post" class="auth-form woocommerce-ResetPassword lost_reset_password">
			<h1><?php esc_html_e( 'Zapomniałeś hasła?', 'qutlet-theme' ); ?></h1>
			<p class="sub">
				<?php
				echo esc_html(
					apply_filters(
						'woocommerce_lost_password_message',
						__( 'Podaj nazwę użytkownika lub adres e-mail. Wyślemy Ci link do ustawienia nowego hasła.', 'qutlet-theme' )
					)
				);
				?>
			</p>

			<div class="field">
				<label for="user_login"><?php esc_html_e( 'Nazwa użytkownika lub e-mail', 'qutlet-theme' ); ?></label>
				<input class="input-text" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
			</div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<input type="hidden" name="wc_reset_password" value="true" />
			<button type="submit" class="btn btn-primary btn-lg btn-block"><?php esc_html_e( 'Zresetuj hasło', 'qutlet-theme' ); ?></button>

			<p class="fine">
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Wróć do logowania', 'qutlet-theme' ); ?></a>
			</p>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
		</form>
	</div>
</div>

<?php do_action( 'woocommerce_after_lost_password_form' ); ?>
