<?php
/**
 * Lost password confirmation (nadpisanie
 * `woocommerce/templates/myaccount/lost-password-confirmation.php`) — ekran
 * po wysłaniu formularza `form-lost-password.php` (`?reset-link-sent=1`).
 * BEZ odpowiednika w prototypie — ta sama rodzina wizualna `.auth-*`,
 * natywny `wc_print_notice()` (ten sam mechanizm co komunikat sukcesu przy
 * zapisie adresu, `.woocommerce-message`).
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="auth-wrap">
	<div class="auth-card">
		<?php wc_print_notice( esc_html__( 'Link do zresetowania hasła został wysłany.', 'qutlet-theme' ) ); ?>

		<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

		<p class="sub">
			<?php
			echo esc_html(
				apply_filters(
					'woocommerce_lost_password_confirmation_message',
					__( 'Wiadomość z linkiem do ustawienia nowego hasła powinna dotrzeć w ciągu kilku minut. Jeśli jej nie widzisz, sprawdź folder Spam.', 'qutlet-theme' )
				)
			);
			?>
		</p>

		<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>

		<p class="fine">
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Wróć do logowania', 'qutlet-theme' ); ?></a>
		</p>
	</div>
</div>
