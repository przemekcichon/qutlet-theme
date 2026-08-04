<?php
/**
 * WooCommerce Blocks Integration dla bloku Cart (D-8.6a.1).
 *
 * Rejestruje JS motywu (`assets/js/cart-block-filters.js`) jako zależność
 * bloku `woocommerce/cart` — bez tego skrypt ładowałby się na każdej stronie,
 * nie tylko tam, gdzie Cart Block faktycznie się renderuje. Sam skrypt woła
 * `registerCheckoutFilters()` (global `window.wc.blocksCheckout`) — brak build
 * stepu, dependency script handle `wc-blocks-checkout` (WooCommerce 10.9.4
 * wystawia go jako gotowy runtime bundle, patrz D-8.6a.1).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Cart;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integracja skryptu odznak/oszczędności z blokiem koszyka.
 */
final class CartBlocksIntegration implements IntegrationInterface {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'qutlet-cart';
	}

	/**
	 * Rejestruje skrypt (bez enqueue — o to dba IntegrationRegistry na
	 * podstawie get_script_handles()).
	 *
	 * @return void
	 */
	public function initialize() {
		wp_register_script(
			'qutlet-theme-cart-block-filters',
			get_theme_file_uri( 'assets/js/cart-block-filters.js' ),
			// `wc-blocks-data-store` = sklep `wc/store/cart` (odczyt `item.extensions`
			// przez wp.data.select — DOM-injection odznak, patrz nagłówek pliku JS).
			array( 'wc-blocks-checkout', 'wc-blocks-data-store', 'wp-data' ),
			\Qutlet\Theme\VERSION,
			true
		);
	}

	/**
	 * @return array<int, string>
	 */
	public function get_script_handles() {
		return array( 'qutlet-theme-cart-block-filters' );
	}

	/**
	 * Bez wsparcia edytora — filtry dotyczą wyłącznie frontu (D-8.6a.1).
	 *
	 * @return array<int, string>
	 */
	public function get_editor_script_handles() {
		return array();
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_script_data() {
		return array();
	}
}
