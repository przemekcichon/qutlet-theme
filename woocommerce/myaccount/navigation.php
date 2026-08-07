<?php
/**
 * My Account navigation (nadpisanie `woocommerce/templates/myaccount/navigation.php`,
 * port `.acct-side`, `design/vanilla/moje-konto.html:24-38`).
 *
 * Natywne helpery WC (`wc_get_account_menu_items()`/`wc_get_account_endpoint_url()`/
 * `wc_is_current_account_menu_item()`) zostają — etykiety/kolejność/usunięcie
 * `downloads` już przefiltrowane przez `Account::menu_items()`
 * (`woocommerce_account_menu_items`). Prototyp używał `<button data-acct-nav>`
 * (JS-owy toggle panelu SPA) — tu każda pozycja to prawdziwy endpoint WC pod
 * osobnym URL-em, więc `<a href>`, nie `<button>`.
 *
 * @package Qutlet\Theme
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
?>

<aside class="acct-side">
	<div class="acct-side-user">
		<div class="acct-avatar"><?php echo esc_html( \Qutlet\Theme\features\Blog\Blog::author_initials( (int) $current_user->ID ) ); ?></div>
		<div class="minw0">
			<div class="acct-side-name"><?php echo esc_html( $current_user->display_name ); ?></div>
			<div class="acct-side-email"><?php echo esc_html( $current_user->user_email ); ?></div>
		</div>
	</div>

	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<?php
		$classes = array( 'acct-nav-btn' );

		if ( wc_is_current_account_menu_item( $endpoint ) ) {
			$classes[] = 'active';
		}

		if ( 'customer-logout' === $endpoint ) {
			$classes[] = 'danger';
		}
		?>
		<a
			href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			<?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>
		><?php echo esc_html( $label ); ?></a>
	<?php endforeach; ?>
</aside>
