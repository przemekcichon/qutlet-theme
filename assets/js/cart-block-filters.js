/**
 * Qutlet — odznaki/oszczędności w bloku Cart (D-8.6a.1).
 *
 * Czyta dane wystawione przez Cart::cart_item_data()/cart_totals_data()
 * (Store API `item.extensions['qutlet-klasa']` / `cart.extensions['qutlet-klasa']`,
 * patrz inc/features/Cart/Cart.php). Bez build stepu — globale `window.wc.*`/
 * `window.wp.data` (WooCommerce 10.9.4 wystawia gotowy runtime bundle,
 * dependency script handle `wc-blocks-checkout`, patrz
 * CartBlocksIntegration::initialize()). Ceny/kwoty przychodzą już
 * sformatowane z PHP — ten plik tylko wkleja gotowe stringi, nie liczy walut.
 *
 * DWA różne mechanizmy, bo `registerCheckoutFilters` okazał się (zweryfikowane
 * runtime) niebezpieczny dla odznaki klasy stanu/starej ceny: zwrócony string
 * z filtra `itemName` trafia NIE TYLKO do widocznej nazwy produktu, ale też
 * dosłownie (z tagami HTML) do aria-label przycisków ilości/usuwania —
 * czytnik ekranu usłyszałby surowe znaczniki. Dlatego odznaki dokładamy przez
 * bezpośrednie wstrzyknięcie DOM (`injectItemBadges`, osobny węzeł OBOK nazwy,
 * nie wewnątrz jej stringu) reagując na `wp.data.subscribe()` na sklepie
 * `wc/store/cart`. `totalValue` (suma "Razem") nie ma tego problemu — nie
 * zasila żadnego aria-label — więc tam `registerCheckoutFilters` zostaje.
 */
(function () {
	'use strict';

	var NAMESPACE = 'qutlet-klasa';

	/**
	 * `klasa_stanu` to zamknięty słownik ACF (A-D, kontrakt §2), więc w praktyce
	 * nie jest wstrzykiwalny — escapowanie i tak przed wklejeniem do innerHTML
	 * (obrona w głąb: wartość podróżuje przez Store API/JSON, nie kod motywu).
	 */
	function escHtml(value) {
		return String(value).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	/**
	 * Suma oszczędności w wierszu totali — bezpieczne przez registerCheckoutFilters
	 * (renderowane jako czysty tekst, zweryfikowane runtime).
	 */
	var checkoutApi = window.wc && window.wc.blocksCheckout;

	if (checkoutApi && typeof checkoutApi.registerCheckoutFilters === 'function') {
		checkoutApi.registerCheckoutFilters('qutlet-cart', {
			totalValue: function (value, extensions) {
				var data = extensions && extensions[NAMESPACE];

				if (!data || !data.total_savings_text) {
					return value;
				}

				return value + ' (oszczędzasz vs. nowe: ' + data.total_savings_text + ')';
			},
		});
	}

	/**
	 * Odznaka klasy stanu + gwarancja + stara cena per wiersz koszyka —
	 * wstrzykiwane jako osobny węzeł DOM (nie modyfikują nazwy produktu/jej
	 * stringu), żeby nie zanieczyścić aria-label generowanych z nazwy przez
	 * sam blok Cart.
	 */
	function injectItemBadges() {
		var select = window.wp && window.wp.data && window.wp.data.select;

		if (!select) {
			return;
		}

		var cartStore = select('wc/store/cart');

		if (!cartStore || typeof cartStore.getCartData !== 'function') {
			return;
		}

		var items = cartStore.getCartData().items || [];

		items.forEach(function (item) {
			var data = item.extensions && item.extensions[NAMESPACE];

			if (!data) {
				return;
			}

			var row = document.querySelector('.wc-block-cart-items__row[data-cart-item-key="' + item.key + '"]');
			var wrap = row && row.querySelector('.wc-block-cart-item__wrap');

			if (!wrap || wrap.querySelector('.qutlet-cart-item-meta')) {
				return;
			}

			var html = '';

			if (data.klasa_stanu) {
				var dot = escHtml(String(data.klasa_stanu).toLowerCase());
				html += '<span class="pill"><span class="dot dot-' + dot + '"></span>Klasa ' + escHtml(data.klasa_stanu) + '</span>' +
					'<span class="pill">Gwarancja 1 rok</span>';
			}

			if (data.old_price_formatted) {
				html += '<small class="cart-old-price">' + data.old_price_formatted + '</small>';
			}

			if (!html) {
				return;
			}

			var meta = document.createElement('div');
			meta.className = 'qutlet-cart-item-meta';
			meta.innerHTML = html;
			wrap.appendChild(meta);
		});
	}

	function scheduleInject() {
		window.setTimeout(injectItemBadges, 0);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', scheduleInject);
	} else {
		scheduleInject();
	}

	if (window.wp && window.wp.data && typeof window.wp.data.subscribe === 'function') {
		window.wp.data.subscribe(scheduleInject);
	}
})();
