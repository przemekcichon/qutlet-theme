<?php
/**
 * My Account page (nadpisanie `woocommerce/templates/myaccount/my-account.php`,
 * port `design/vanilla/moje-konto.html:17-22`).
 *
 * `[woocommerce_my_account]` (`WC_Shortcode_My_Account::output()`) sam
 * sprawdza `is_user_logged_in()` PRZED wywołaniem tego szablonu — ten plik
 * renderuje się WYŁĄCZNIE dla zalogowanych (stan wylogowany obsługuje osobno
 * `form-login.php`, patrz nagłówek `inc/features/Account/Account.php`), stąd
 * nagłówek „Moje konto" jest tu bezwarunkowy.
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="page-head">
	<h1 class="page-title"><?php esc_html_e( 'Moje konto', 'qutlet-theme' ); ?></h1>
</div>

<div class="account-layout">
	<?php
	/**
	 * My Account navigation.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_navigation' );
	?>

	<div>
		<div class="woocommerce-MyAccount-content acct-pane">
			<?php
			/**
			 * My Account content.
			 *
			 * @since 2.6.0
			 */
			do_action( 'woocommerce_account_content' );
			?>
		</div>
	</div>
</div>
