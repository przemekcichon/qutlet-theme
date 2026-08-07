<?php
/**
 * Login form (nadpisanie `woocommerce/templates/myaccount/form-login.php`,
 * port `design/vanilla/logowanie.html`) — jedyny szablon renderowany na
 * `/moje-konto/`, gdy klient NIE jest zalogowany (patrz nagłówek
 * `inc/features/Account/Account.php`, `WC_Shortcode_My_Account::output()`).
 *
 * Zakładki Logowanie/Rejestracja to CZYSTY UI-toggle
 * (`assets/js/account-auth-tabs.js`, `data-auth-tab`/`data-auth-pane`) — OBA
 * formularze renderują się zawsze (SEO/no-JS fallback pokazuje oba pod
 * sobą), JS tylko chowa nieaktywny. `woocommerce_enable_myaccount_registration`
 * włączone (D-8.6c.3 — było `no`, domyślny stan świeżej instalacji WC, nie
 * świadoma decyzja biznesowa; sklep bez rejestracji odwiedzających byłby
 * niezgodny z prototypem).
 *
 * Rejestracja: natywne pola WC to WYŁĄCZNIE e-mail + hasło
 * (`woocommerce_registration_generate_username=yes` -> username z e-maila,
 * `woocommerce_registration_generate_password=no` -> klient ustawia hasło)
 * — pola Imię/Nazwisko z prototypu (`logowanie.html:38-39`) NIE mają
 * odpowiednika (WC ich nie zbiera przy rejestracji); dopisanie +
 * zapis do usermeta to GLUE, nie szablon (D-8.G1, „Uwaga (P-8.6)") ->
 * OSOBNY punkt w core, poza zakresem tej poprawki (znany brak).
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
?>

<div class="auth-wrap">
	<div class="auth-card">
		<div class="auth-tabs">
			<button type="button" class="auth-tab active" data-auth-tab="login"><?php esc_html_e( 'Logowanie', 'qutlet-theme' ); ?></button>
			<button type="button" class="auth-tab" data-auth-tab="register"><?php esc_html_e( 'Rejestracja', 'qutlet-theme' ); ?></button>
		</div>

		<form class="auth-form woocommerce-form woocommerce-form-login login" data-auth-pane="login" method="post" novalidate>
			<h1><?php esc_html_e( 'Witaj z powrotem', 'qutlet-theme' ); ?></h1>
			<p class="sub"><?php esc_html_e( 'Zaloguj się, aby śledzić zamówienia i szybciej finalizować zakupy.', 'qutlet-theme' ); ?></p>

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<div class="field">
				<label for="username"><?php esc_html_e( 'Adres e-mail', 'qutlet-theme' ); ?></label>
				<input type="text" class="input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" placeholder="ty@przyklad.pl" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash ?>
			</div>
			<div class="field">
				<label for="password"><?php esc_html_e( 'Hasło', 'qutlet-theme' ); ?></label>
				<input class="input-text" type="password" name="password" id="password" autocomplete="current-password" placeholder="<?php esc_attr_e( 'Minimum 6 znaków', 'qutlet-theme' ); ?>" />
			</div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<button type="submit" class="btn btn-primary btn-lg btn-block" name="login" value="<?php esc_attr_e( 'Zaloguj się', 'qutlet-theme' ); ?>"><?php esc_html_e( 'Zaloguj się', 'qutlet-theme' ); ?></button>

			<p class="fine">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Nie pamiętasz hasła?', 'qutlet-theme' ); ?></a>
			</p>

			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>

		<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<form class="auth-form woocommerce-form woocommerce-form-register register" data-auth-pane="register" hidden method="post" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
				<h1><?php esc_html_e( 'Załóż konto', 'qutlet-theme' ); ?></h1>
				<p class="sub"><?php esc_html_e( 'Śledź zamówienia, zapisuj adresy i kupuj szybciej.', 'qutlet-theme' ); ?></p>

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
					<div class="field">
						<label for="reg_username"><?php esc_html_e( 'Nazwa użytkownika', 'qutlet-theme' ); ?></label>
						<input type="text" class="input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash ?>
					</div>
				<?php endif; ?>

				<div class="field">
					<label for="reg_email"><?php esc_html_e( 'Adres e-mail', 'qutlet-theme' ); ?></label>
					<input type="email" class="input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" placeholder="ty@przyklad.pl" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash ?>
				</div>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
					<div class="field">
						<label for="reg_password"><?php esc_html_e( 'Hasło', 'qutlet-theme' ); ?></label>
						<input type="password" class="input-text" name="password" id="reg_password" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Minimum 6 znaków', 'qutlet-theme' ); ?>" />
					</div>
				<?php else : ?>
					<p><?php esc_html_e( 'Link do ustawienia hasła wyślemy na Twój adres e-mail.', 'qutlet-theme' ); ?></p>
				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<button type="submit" class="btn btn-primary btn-lg btn-block" name="register" value="<?php esc_attr_e( 'Utwórz konto', 'qutlet-theme' ); ?>"><?php esc_html_e( 'Utwórz konto', 'qutlet-theme' ); ?></button>

				<p class="fine">
					<?php
					printf(
						/* translators: %s: link do regulaminu. */
						esc_html__( 'Zakładając konto akceptujesz %s.', 'qutlet-theme' ),
						'<a href="' . esc_url( home_url( '/regulamin/' ) ) . '">' . esc_html__( 'regulamin', 'qutlet-theme' ) . '</a>'
					);
					?>
				</p>

				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
