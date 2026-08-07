<?php
/**
 * Edit account form (nadpisanie `woocommerce/templates/myaccount/form-edit-account.php`,
 * port „Dane konta" `design/vanilla/moje-konto.html:71-90`, karty `.form-card`).
 *
 * Prototyp dzieli ten panel na DWA osobne formularze/submity (e-mail vs
 * hasło) — natywny handler `WC_Form_Handler::save_account_details()` wymaga
 * JEDNEGO `<form>` (required fields first/last/display name + weryfikacja
 * `password_current` przy zmianie hasła, patrz ground-truth
 * `includes/class-wc-form-handler.php:284-326`; rozbicie na dwa submity
 * zepsułoby walidację wymaganych pól i historię hasła). Zachowany więc JEDEN
 * `<form>`, wizualnie podzielony na dwie karty `.form-card` — świadome
 * odstępstwo od prototypu (znany brak, fast-follow: custom AJAX handler,
 * gdyby dwa niezależne submity były wymagane biznesowo). `required`/
 * `aria-required` na czterech polach pierwszej karty PRZYWRÓCONE po
 * niezależnej recenzji PR #24 (pierwsza wersja je zgubiła przy restylingu
 * — serwer i tak waliduje, więc to była degradacja UX, nie luka
 * bezpieczeństwa, ale przeglądarka powinna łapać puste pola przed submitem,
 * tak jak natywny szablon WC).
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<h2><?php esc_html_e( 'Dane konta', 'qutlet-theme' ); ?></h2>
<p class="pane-lead"><?php esc_html_e( 'Adres e-mail i hasło do logowania.', 'qutlet-theme' ); ?></p>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>
	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<div class="form-card">
		<h3 class="step-title"><?php esc_html_e( 'Dane osobowe i e-mail', 'qutlet-theme' ); ?></h3>
		<div class="form-grid">
			<div class="field">
				<label for="account_first_name"><?php esc_html_e( 'Imię', 'qutlet-theme' ); ?></label>
				<input type="text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" required aria-required="true" />
			</div>
			<div class="field">
				<label for="account_last_name"><?php esc_html_e( 'Nazwisko', 'qutlet-theme' ); ?></label>
				<input type="text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" required aria-required="true" />
			</div>
			<div class="field field-full">
				<label for="account_display_name"><?php esc_html_e( 'Nazwa wyświetlana', 'qutlet-theme' ); ?></label>
				<input type="text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" required aria-required="true" />
			</div>
			<div class="field field-full">
				<label for="account_email"><?php esc_html_e( 'E-mail', 'qutlet-theme' ); ?></label>
				<input type="email" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" required aria-required="true" />
			</div>
		</div>

		<?php
		/**
		 * Hook where additional fields should be rendered.
		 *
		 * @since 8.7.0
		 */
		do_action( 'woocommerce_edit_account_form_fields' );
		?>
	</div>

	<div class="form-card">
		<h3 class="step-title"><?php esc_html_e( 'Zmiana hasła', 'qutlet-theme' ); ?></h3>
		<div class="form-grid">
			<div class="field field-full">
				<label for="password_current"><?php esc_html_e( 'Obecne hasło (zostaw puste, by nie zmieniać)', 'qutlet-theme' ); ?></label>
				<input type="password" name="password_current" id="password_current" autocomplete="current-password" />
			</div>
			<div class="field">
				<label for="password_1"><?php esc_html_e( 'Nowe hasło', 'qutlet-theme' ); ?></label>
				<input type="password" name="password_1" id="password_1" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Min. 6 znaków', 'qutlet-theme' ); ?>" />
			</div>
			<div class="field">
				<label for="password_2"><?php esc_html_e( 'Powtórz hasło', 'qutlet-theme' ); ?></label>
				<input type="password" name="password_2" id="password_2" autocomplete="new-password" />
			</div>
		</div>
	</div>

	<?php
	/**
	 * My Account edit account form.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_edit_account_form' );
	?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="btn btn-dark" name="save_account_details" value="<?php esc_attr_e( 'Zapisz zmiany', 'qutlet-theme' ); ?>"><?php esc_html_e( 'Zapisz zmiany', 'qutlet-theme' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
