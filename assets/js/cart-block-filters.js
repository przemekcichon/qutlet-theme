/**
 * Qutlet — odznaki/oszczędności/wartość produktów w bloku Cart (D-8.6a.1).
 *
 * Czyta dane wystawione przez Cart::cart_item_data()/cart_totals_data()
 * (Store API `item.extensions['qutlet-klasa']` / `cart.extensions['qutlet-klasa']`,
 * patrz inc/features/Cart/Cart.php). Bez build stepu — global `window.wp.data`
 * (WooCommerce 10.9.4 wystawia gotowy runtime bundle, dependency script handle
 * `wc-blocks-data-store`, patrz CartBlocksIntegration::initialize()). Ceny/
 * kwoty przychodzą już sformatowane z PHP (`wc_price()`) — ten plik tylko
 * wkleja gotowe stringi, nie liczy walut.
 *
 * Wszystko wstrzykiwane jako WĘZŁY DOM (`wp.data.subscribe()` na sklepie
 * `wc/store/cart`), NIE przez `registerCheckoutFilters` — ten filtr
 * (`itemName`) okazał się (zweryfikowane runtime) zanieczyszczać aria-label
 * przycisków ilości/usuwania surowym HTML, bo zwrócony string zasila NIE
 * TYLKO widoczną nazwę, ale i te atrybuty. DOM injection nie ma tego problemu
 * (nowy węzeł, nie modyfikacja istniejącego stringu) i działa identycznie dla
 * wiersza „Wartość produktów"/„Oszczędzasz" w podsumowaniu, których natywny
 * blok Cart w ogóle nie renderuje w tym układzie (Subtotal-block chowa się,
 * gdy wartość równa się Total — brak dostawy/rabatu do rozróżnienia).
 */
(function () {
	'use strict';

	var NAMESPACE = 'qutlet-klasa';

	function getCartData() {
		var select = window.wp && window.wp.data && window.wp.data.select;

		if (!select) {
			return null;
		}

		var cartStore = select('wc/store/cart');

		if (!cartStore || typeof cartStore.getCartData !== 'function') {
			return null;
		}

		return cartStore.getCartData();
	}

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
	 * Odznaka klasy stanu + gwarancja (obok nazwy produktu) i stara cena
	 * (osobny blok WEWNĄTRZ kolumny ceny, pod ceną sprzedaży — jak
	 * `.cart-row-price small` w prototypie) — dwa OSOBNE węzły DOM, nie
	 * modyfikacje istniejących stringów (patrz nagłówek pliku).
	 */
	function injectItemBadges() {
		var data = getCartData();

		if (!data) {
			return;
		}

		(data.items || []).forEach(function (item) {
			var ext = item.extensions && item.extensions[NAMESPACE];

			if (!ext) {
				return;
			}

			var row = document.querySelector('.wc-block-cart-items__row[data-cart-item-key="' + item.key + '"]');

			if (!row) {
				return;
			}

			var nameEl = row.querySelector('.wc-block-components-product-name');

			if (nameEl && ext.klasa_stanu && !row.querySelector('.qutlet-cart-badges')) {
				var dot = escHtml(String(ext.klasa_stanu).toLowerCase());
				var badges = document.createElement('div');
				badges.className = 'qutlet-cart-badges';
				badges.innerHTML =
					'<span class="pill"><span class="dot dot-' + dot + '"></span>Klasa ' + escHtml(ext.klasa_stanu) + '</span>' +
					'<span class="pill">Gwarancja 1 rok</span>';
				nameEl.insertAdjacentElement('afterend', badges);
			}

			var pricesEl = row.querySelector('.wc-block-cart-item__prices');

			if (pricesEl && ext.old_price_formatted && !pricesEl.querySelector('.cart-old-price')) {
				var oldPrice = document.createElement('small');
				oldPrice.className = 'cart-old-price';
				oldPrice.innerHTML = ext.old_price_formatted;
				pricesEl.appendChild(oldPrice);
			}
		});
	}

	/**
	 * Wiersz „Wartość produktów" (na wzór `.wc-block-components-totals-item`
	 * natywnego wiersza dostawy — te same klasy, żeby wtopić się bez własnego
	 * CSS) i zielony box „Oszczędzasz vs. nowe" (`.savings-note`, port
	 * design/vanilla/css/style.css:464-468) w podsumowaniu koszyka.
	 */
	function injectSummaryRows() {
		var data = getCartData();
		var ext = data && data.extensions && data.extensions[NAMESPACE];
		var totalsBlock = document.querySelector('.wp-block-woocommerce-cart-order-summary-totals-block');

		if (!totalsBlock) {
			return;
		}

		// Wartość produktów — wstaw/zaktualizuj/usuń w zależności od aktualnej
		// sumy (kwota zmienia się przy KAŻDEJ zmianie koszyka — usunięcie
		// pozycji, zmiana ilości — więc "wstaw raz i zostaw" pokazywałoby
		// nieaktualną kwotę po takiej zmianie).
		var subtotalRow = totalsBlock.querySelector('.qutlet-cart-subtotal-row');

		if (ext && ext.subtotal_formatted) {
			if (!subtotalRow) {
				subtotalRow = document.createElement('div');
				subtotalRow.className = 'wc-block-components-totals-wrapper qutlet-cart-subtotal-row';
				subtotalRow.innerHTML =
					'<div class="wc-block-components-totals-item">' +
					'<span class="wc-block-components-totals-item__label">Wartość produktów</span>' +
					'<div class="wc-block-components-totals-item__value"></div>' +
					'</div>';
				totalsBlock.insertAdjacentElement('afterbegin', subtotalRow);
			}
			subtotalRow.querySelector('.wc-block-components-totals-item__value').innerHTML = ext.subtotal_formatted;
		} else if (subtotalRow) {
			subtotalRow.remove();
		}

		// Zielony box oszczędności — ta sama logika (wstaw/zaktualizuj/usuń).
		var savings = document.querySelector('.qutlet-cart-savings-note');

		if (ext && ext.total_savings_formatted) {
			if (!savings) {
				savings = document.createElement('div');
				savings.className = 'savings-note qutlet-cart-savings-note';
				savings.innerHTML = '<span>Oszczędzasz vs. nowe</span><span></span>';
				totalsBlock.insertAdjacentElement('afterend', savings);
			}
			savings.lastElementChild.innerHTML = ext.total_savings_formatted;
		} else if (savings) {
			savings.remove();
		}
	}

	/**
	 * Mini-koszyk w headerze (`.cart-badge`/`.cart-menu`, D-8.6a.3) żyje na
	 * classic `woocommerce_add_to_cart_fragments` + `wc-cart-fragments.js`,
	 * który odświeża się na zdarzenie jQuery `wc_fragment_refresh` — ale
	 * interakcje WEWNĄTRZ bloku Cart (ilość, usuń) idą przez Store API, nie
	 * przez classic AJAX, więc to zdarzenie nigdy samo nie leci i badge/dropdown
	 * w headerze zostają z nieaktualną liczbą. Mostek: obserwuj `itemsCount`
	 * ze sklepu `wc/store/cart` (getCartData() zwraca camelCase, NIE `items_count`
	 * ze "surowego" JSON-a Store API — zweryfikowane runtime) i wywołaj
	 * zdarzenie ręcznie, gdy się zmieni.
	 */
	var lastItemsCount = null;

	function refreshHeaderFragmentsOnChange() {
		var data = getCartData();

		if (!data || typeof data.itemsCount !== 'number') {
			return;
		}

		if (lastItemsCount !== null && lastItemsCount !== data.itemsCount && window.jQuery) {
			window.jQuery(document.body).trigger('wc_fragment_refresh');
		}

		lastItemsCount = data.itemsCount;
	}

	function inject() {
		injectItemBadges();
		injectSummaryRows();
		refreshHeaderFragmentsOnChange();
	}

	function scheduleInject() {
		window.setTimeout(inject, 0);
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
