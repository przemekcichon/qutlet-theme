<?php
/**
 * WooCommerce Blocks Integration dla bloku Checkout (D-8.6b.1).
 *
 * Odpowiednik `Cart\CartBlocksIntegration` — osobna instancja, bo
 * `IntegrationRegistry` jest per blok (Cart i Checkout dostają każdy swoją,
 * `BlockTypesController::register_blocks()`), więc integracja zarejestrowana
 * na bloku Cart NIE ładuje swojego skryptu na bloku Checkout. Dane
 * (`item.extensions['qutlet-klasa']`/`cart.extensions['qutlet-klasa']`) są
 * WSPÓLNE (Store API `cart-item`/`cart`, zarejestrowane raz w `Cart::boot()`,
 * D-12.G2) — tu rejestrujemy WYŁĄCZNIE skrypt czytający je po stronie bloku
 * Checkout.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Checkout;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integracja skryptu odznak/oszczędności z blokiem kasy.
 */
final class CheckoutBlocksIntegration implements IntegrationInterface {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'qutlet-checkout';
	}

	/**
	 * Rejestruje skrypt (bez enqueue — o to dba IntegrationRegistry na
	 * podstawie get_script_handles()).
	 *
	 * @return void
	 */
	public function initialize() {
		wp_register_script(
			'qutlet-theme-checkout-block-filters',
			get_theme_file_uri( 'assets/js/checkout-block-filters.js' ),
			// `wc-blocks-data-store` = sklep `wc/store/cart` (odczyt `item.extensions`
			// przez wp.data.select — DOM-injection odznak, ten sam mechanizm co
			// cart-block-filters.js, patrz CartBlocksIntegration::initialize()).
			array( 'wc-blocks-data-store', 'wp-data' ),
			\Qutlet\Theme\VERSION,
			true
		);
	}

	/**
	 * @return array<int, string>
	 */
	public function get_script_handles() {
		return array( 'qutlet-theme-checkout-block-filters' );
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
