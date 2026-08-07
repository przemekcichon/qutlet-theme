<?php
/**
 * My Account navigation (nadpisanie `woocommerce/templates/myaccount/navigation.php`,
 * port `.acct-side`, `design/vanilla/moje-konto.html:24-38`).
 *
 * Natywne helpery WC (`wc_get_account_menu_items()`/`wc_get_account_endpoint_url()`/
 * `wc_is_current_account_menu_item()`/`wc_get_account_menu_item_classes()`) zostają
 * — etykiety/kolejność/usunięcie `downloads` już przefiltrowane przez
 * `Account::menu_items()` (`woocommerce_account_menu_items`). Prototyp używał
 * `<button data-acct-nav>` (JS-owy toggle panelu SPA) — tu każda pozycja to
 * prawdziwy endpoint WC pod osobnym URL-em, więc `<a href>`, nie `<button>`.
 *
 * `<nav aria-label>` + `<ul>/<li>` (nie gołe `<a>` bezpośrednio w `<aside>`) i
 * `do_action('woocommerce_before/after_account_navigation')` PRZYWRÓCONE po
 * niezależnej recenzji PR #24 — pierwsza wersja gubiła oba (landmark
 * nawigacyjny/semantyka listy dla czytników ekranu, punkt zaczepienia dla
 * wtyczek dopinających się do menu konta), mimo że nic w porcie tego nie
 * wymagało. `wc_get_account_menu_item_classes()` też przywrócone — dokłada
 * `woocommerce-MyAccount-navigation-link(--{endpoint})`/`is-active` OBOK
 * `.acct-nav-btn`/`.active` (nasze klasy wizualne), więc oba światy (styling
 * motywu + selektory, na których wtyczki/CSS WC mogą polegać) współistnieją.
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();

do_action( 'woocommerce_before_account_navigation' );
?>

<aside class="acct-side">
	<div class="acct-side-user">
		<div class="acct-avatar"><?php echo esc_html( \Qutlet\Theme\features\Blog\Blog::author_initials( (int) $current_user->ID ) ); ?></div>
		<div class="minw0">
			<div class="acct-side-name"><?php echo esc_html( $current_user->display_name ); ?></div>
			<div class="acct-side-email"><?php echo esc_html( $current_user->user_email ); ?></div>
		</div>
	</div>

	<nav class="woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Nawigacja konta', 'qutlet-theme' ); ?>">
		<ul>
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<?php
				$is_current  = wc_is_current_account_menu_item( $endpoint );
				$link_classes = array( 'acct-nav-btn' );

				if ( $is_current ) {
					$link_classes[] = 'active';
				}

				if ( 'customer-logout' === $endpoint ) {
					$link_classes[] = 'danger';
				}
				?>
				<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
					<a
						href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
						class="<?php echo esc_attr( implode( ' ', $link_classes ) ); ?>"
						<?php echo $is_current ? 'aria-current="page"' : ''; ?>
					><?php echo esc_html( $label ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
</aside>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
