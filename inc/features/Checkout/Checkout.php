<?php
/**
 * Slice Checkout — kasa (port design/vanilla/kasa.html) + potwierdzenie
 * (design/vanilla/potwierdzenie.html).
 *
 * P-8.6b: ground-truth (sesja 2026-08-06) powtórzył zaskoczenie z P-8.6a —
 * Strona „Checkout" (`woocommerce_checkout_page_id`) na tej instalacji (WC
 * 10.9.4) domyślnie zawiera blok `wp:woocommerce/checkout`, NIE shortcode
 * `[woocommerce_checkout]` — mimo komentarza w prototypie (`kasa.html:13`,
 * `→ woocommerce/checkout/form-checkout.php`) sugerującego classic. Idziemy
 * TĄ SAMĄ nowoczesną ścieżką co koszyk (D-8.6a.1, potwierdzone na wyraźną
 * prośbę użytkownika w tej sesji): dane per-wiersz (klasa stanu, stara
 * cena/oszczędności) już istnieją na Store API `qutlet-klasa` zarejestrowanym
 * przez `Cart::register_store_api_data()` (endpointy `cart-item`/`cart`) —
 * D-12.G2 potwierdzone ground-truthem RUNTIME (Playwright): te same
 * rozszerzenia są widoczne w `wp.data.select('wc/store/cart').getCartData()`
 * na stronie `/kasa/`, bo blok Checkout czyta z TEGO SAMEGO zasobu Store API
 * `cart`, co blok Cart. Ta klasa NIE rejestruje więc własnych danych —
 * WYŁĄCZNIE własny skrypt (`assets/js/checkout-block-filters.js`) jako
 * integrację bloku Checkout (`CheckoutBlocksIntegration`, osobna instancja
 * `IntegrationRegistry` od bloku Cart — `woocommerce_blocks_{$block}_block_registration`
 * to hook PER BLOK, patrz IntegrationRegistry::initialize()).
 *
 * POPRAWKA GROUND-TRUTH (potwierdzenie zamówienia): pierwsza wersja tego
 * pliku zakładała (na podstawie `Checkout::is_checkout_endpoint()` w
 * `woocommerce/src/Blocks/BlockTypes/Checkout.php`), że `order-received`
 * odpada na classic shortcode/`woocommerce/checkout/thankyou.php` —
 * ZWERYFIKOWANE RUNTIME jako FAŁSZYWE (Playwright, realne zamówienie
 * testowe, ta sama sesja): ten classic override NIGDY się nie odpalał.
 * `is_checkout_endpoint()` dotyczy WYŁĄCZNIE bloku FORMULARZA checkoutu (co
 * zrobić, gdy ktoś umieści blok `wp:woocommerce/checkout` NA STRONIE
 * order-received) — o WYBORZE CAŁEGO SZABLONU STRONY dla endpointu
 * `order-received` decyduje OSOBNY mechanizm: WooCommerce Blocks rejestruje
 * własny szablon FSE `order-confirmation`
 * (`OrderConfirmationTemplate::is_active_template()` →
 * `is_wc_endpoint_url('order-received')`), analogicznie do `page-cart`/
 * `page-checkout` (ten sam `AbstractPageTemplate`, ten sam priorytet nad
 * `templates/page.html` motywu). Realny render (zweryfikowany przez
 * `document.body.innerHTML`): natywne bloki
 * `wp:woocommerce/order-confirmation-status` +
 * `-summary`/`-totals`/`-shipping-address`/`-billing-address`/… — CAŁKOWICIE
 * SERWEROWO renderowane (PHP, bez Store API/Reacta, w przeciwieństwie do
 * Cart/Checkout) — bez potrzeby JS DOM-injection. Nadpisanie:
 * `templates/order-confirmation.html` (nowy szablon FSE motywu, port
 * `potwierdzenie.html`, WYŁĄCZNIE Status+Summary — reszta bloków
 * domyślnego szablonu WC, jak totals/shipping/billing/downloads, świadomie
 * pominięta, bo prototyp jest równie minimalny) + filtry tekstu
 * `woocommerce_thankyou_order_received_title`/`_text` niżej (jedyny
 * mechanizm WC na podmianę treści bloku Status bez przepisywania komponentu
 * — te same nazwy hooków co stary classic `thankyou.php`, celowo
 * zachowane przy przejściu na bloki).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Checkout;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap integracji bloku Checkout + treści bloku Order Confirmation.
 */
final class Checkout {

	/**
	 * Statusy zamówienia, dla których blok Status ma WŁASNY, odmienny
	 * komunikat (anulowane/zwrócone/zakończone/nieudane) — nasz podmieniony
	 * tekst „Zamówienie przyjęte!" dotyczy WYŁĄCZNIE domyślnej ścieżki
	 * (nowe/w trakcie realizacji), nie tych czterech, żeby nie pokazać
	 * mylącego „przyjęte" na anulowanym zamówieniu.
	 *
	 * @var string[]
	 */
	const STATUSES_WITH_OWN_MESSAGE = array( 'cancelled', 'refunded', 'completed', 'failed' );

	/**
	 * Podpina rejestrację integracji bloku Checkout (D-8.6b.1) + filtry
	 * treści bloku Order Confirmation (D-8.6b.2).
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'woocommerce_blocks_checkout_block_registration', array( self::class, 'register_blocks_integration' ) );
		add_filter( 'woocommerce_thankyou_order_received_title', array( self::class, 'order_received_title' ), 10, 2 );
		add_filter( 'woocommerce_thankyou_order_received_text', array( self::class, 'order_received_text' ), 10, 2 );
	}

	/**
	 * Rejestruje CheckoutBlocksIntegration na `IntegrationRegistry` bloku Checkout.
	 *
	 * @param \Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry $registry Rejestr integracji bloku.
	 * @return void
	 */
	public static function register_blocks_integration( $registry ): void {
		$registry->register( new CheckoutBlocksIntegration() );
	}

	/**
	 * Port nagłówka `potwierdzenie.html:22` („Zamówienie przyjęte!") —
	 * WYŁĄCZNIE dla domyślnej ścieżki (patrz STATUSES_WITH_OWN_MESSAGE).
	 *
	 * @param string        $title Domyślny tytuł WC.
	 * @param \WC_Order|null $order Zamówienie (null gdy brak uprawnień/nie znaleziono).
	 * @return string
	 */
	public static function order_received_title( string $title, $order ): string {
		if ( ! $order instanceof \WC_Order || in_array( $order->get_status(), self::STATUSES_WITH_OWN_MESSAGE, true ) ) {
			return $title;
		}

		return __( 'Zamówienie przyjęte!', 'qutlet-theme' );
	}

	/**
	 * Port akapitu `potwierdzenie.html:23` (adres e-mail pogrubiony,
	 * `ProductPage::bold_text()` — ten sam mechanizm co reszta motywu).
	 *
	 * @param string        $text  Domyślny tekst WC.
	 * @param \WC_Order|null $order Zamówienie (null gdy brak uprawnień/nie znaleziono).
	 * @return string
	 */
	public static function order_received_text( string $text, $order ): string {
		if ( ! $order instanceof \WC_Order || in_array( $order->get_status(), self::STATUSES_WITH_OWN_MESSAGE, true ) ) {
			return $text;
		}

		return \Qutlet\Theme\features\ProductPage\ProductPage::bold_text(
			/* translators: %s: adres e-mail kupującego (pogrubiony). */
			__( 'Dziękujemy za zakup. Potwierdzenie i szczegóły wysyłki znajdziesz na adresie %s.', 'qutlet-theme' ),
			$order->get_billing_email()
		);
	}
}
