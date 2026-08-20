/**
 * Qutlet — konwersja add-to-cart na stronie produktu na AJAX dla ŚCIEŻKI
 * SUKCESU (P-22.7, D-22.7.1). Przechwytuje submit `#qutlet-add-to-cart-form`
 * (`woocommerce/single-product/add-to-cart/simple.php` — submitowany TEŻ
 * przez sticky buybar spoza formularza, atrybut HTML5
 * `form="qutlet-add-to-cart-form"`, `content-single-product.php`), POST-uje
 * do natywnego endpointu `wc_add_to_cart_params.wc_ajax_url` (`add_to_cart`,
 * `WC_AJAX::add_to_cart()`) i na sukces ręcznie wyzwala natywne jQuery
 * `added_to_cart` — `woocommerce/assets/js/frontend/add-to-cart.js`
 * (enqueued sitewide przez WooCommerce niezależnie od tego motywu) samo
 * podmienia fragmenty (mini-koszyk w headerze, `inc/features/Cart/Cart.php`,
 * + `.woocommerce-notices-wrapper`, `ProductPage::ajax_add_to_cart_message()`/
 * `ajax_add_to_cart_notices_fragment()`) — zero duplikacji logiki (D-8.G1).
 *
 * Ścieżka błędu (limit stanu, walidacja — `WC_AJAX::add_to_cart()` zwraca
 * `{error:true, product_url}`) zostaje natywnym fallbackiem: pełne
 * przeładowanie, identycznie z `add-to-cart.js` (Wątek 3, P-22.7) — theme
 * NIE reimplementuje walidacji stanu (server-side, `WC_Cart::add_to_cart()`).
 *
 * Przycisk zamknięcia toastu (D-22.7.2, brak auto-hide) dokładany tu, bo
 * żaden szablon Woo (`templates/block-notices/success.php`/`error.php`) go
 * nie renderuje — musi się pojawić zarówno po fragmencie AJAX, jak i po
 * pełnym przeładowaniu strony (stąd wywołanie też poza handlerem submitu).
 */
jQuery( function ( $ ) {
	'use strict';

	if ( typeof wc_add_to_cart_params === 'undefined' ) {
		return;
	}

	function injectCloseButtons() {
		document
			.querySelectorAll( '.woocommerce-notices-wrapper .wc-block-components-notice-banner' )
			.forEach( function ( banner ) {
				if ( banner.querySelector( '[data-toast-close]' ) ) {
					return;
				}

				var closeBtn = document.createElement( 'button' );
				closeBtn.type = 'button';
				closeBtn.className = 'toast-close';
				closeBtn.setAttribute( 'data-toast-close', '' );
				closeBtn.setAttribute( 'aria-label', 'Zamknij' );
				closeBtn.innerHTML = '&times;';
				banner.appendChild( closeBtn );
			} );
	}

	document.addEventListener( 'click', function ( e ) {
		var closeBtn = e.target.closest( '[data-toast-close]' );

		if ( ! closeBtn ) {
			return;
		}

		var banner = closeBtn.closest( '.wc-block-components-notice-banner' );

		if ( banner ) {
			banner.remove();
		}
	} );

	injectCloseButtons();

	$( document.body ).on( 'submit', '#qutlet-add-to-cart-form', function ( e ) {
		var $form      = $( this );
		var productId  = $form.find( '[name="add-to-cart"]' ).val();
		var quantity   = $form.find( '[name="quantity"]' ).val() || 1;

		if ( ! productId ) {
			return;
		}

		e.preventDefault();

		$.ajax( {
			type: 'POST',
			url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' ),
			data: {
				product_id: productId,
				quantity: quantity,
			},
			dataType: 'json',
			success: function ( response ) {
				if ( ! response ) {
					return;
				}

				if ( response.error && response.product_url ) {
					window.location = response.product_url;
					return;
				}

				$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $form ] );
				injectCloseButtons();
			},
		} );
	} );
} );
