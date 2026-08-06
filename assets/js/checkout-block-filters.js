/**
 * Qutlet — odznaki/oszczędności w podsumowaniu zamówienia bloku Checkout
 * (D-8.6b.1, ten sam wzorzec co assets/js/cart-block-filters.js — P-8.6a).
 *
 * Czyta TE SAME dane Store API co koszyk (`item.extensions['qutlet-klasa']`/
 * `cart.extensions['qutlet-klasa']`, zarejestrowane raz w
 * inc/features/Cart/Cart.php — D-12.G2, potwierdzone runtime: blok Checkout
 * czyta z tego samego zasobu `wc/store/cart` co blok Cart). Bez build stepu —
 * global `window.wp.data` (dependency script handles `wc-blocks-data-store`/
 * `wp-data`, patrz CheckoutBlocksIntegration::initialize()).
 *
 * DOM bloku Checkout różni się od bloku Cart: wiersz podsumowania
 * (`.wc-block-components-order-summary-item`) NIE niesie
 * `data-cart-item-key` (w przeciwieństwie do `.wc-block-cart-items__row` w
 * koszyku) — dopasowanie do `data.items` po INDEKSIE, nie po kluczu.
 *
 * UWAGA (zweryfikowane runtime, Playwright, sesja 2026-08-06): blok Checkout
 * renderuje podsumowanie DWA RAZY jednocześnie — raz w mobilnym podglądzie
 * „Slot/Fill" (`…-block-fill`, chowany CSS-em na desktopie, ale NIE usuwany z
 * DOM) i raz w prawdziwym sidebarze (`.wc-block-checkout__sidebar`) — DWIE
 * OSOBNE listy `.wc-block-components-order-summary-item`, każda wewnętrznie w
 * tej samej kolejności co `data.items`, ale `querySelectorAll` na całym
 * dokumencie zwraca je jako JEDNĄ płaską listę (pierwsze węzły to podgląd,
 * nie sidebar) — dopasowanie po indeksie w takiej płaskiej liście myliłoby
 * wiersz koszyka 0 z węzłem podglądu przy >1 pozycji. Iteracja PO KONTENERZE
 * (`.wc-block-components-order-summary__content`, wspólny rodzic obu list)
 * osobno dla każdej z dwóch instancji naprawia dopasowanie.
 *
 * Podsumowanie zamówienia jest TYLKO DO ODCZYTU (bez stepperu/dropdownu
 * ilości z P-8.6a.3 runda 5) — zmiana ilości należy do strony koszyka, nie
 * kasy, zgodnie z tym, jak WC Blocks samo traktuje ten widok (brak natywnej
 * kontrolki ilości w podsumowaniu checkoutu).
 *
 * Wszystko wstrzykiwane jako WĘZŁY DOM (`wp.data.subscribe()`), z tego samego
 * powodu co w koszyku: `registerCheckoutFilters` zanieczyszczał aria-label
 * surowym HTML (zweryfikowane w P-8.6a).
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
	 * `klasa_stanu` to zamknięty słownik ACF (A-D, kontrakt §2) — escapowanie
	 * i tak przed wklejeniem do innerHTML (obrona w głąb, patrz cart-block-filters.js).
	 */
	function escHtml(value) {
		return String(value).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	/**
	 * Odznaka klasy stanu + gwarancja (D-12.G2 — te same trzy fakty co w
	 * koszyku, reklamacja dochodzi dopiero z bytem P-12.1a/b, dziś jak w
	 * koszyku PRZED FAZĄ 12: gwarancja to nadal statyczny literał „1 rok").
	 * Wstrzykiwane do `.wc-block-components-product-metadata` — pusty węzeł
	 * już przygotowany przez WC Blocks na dokładnie ten cel (metadane
	 * pozycji), więc bez potrzeby dopisywania własnego kontenera jak w koszyku
	 * (`.wc-block-cart-item__wrap` tam nie ma odpowiednika tutaj).
	 */
	function injectItemBadgesInto(container, items) {
		var rows = container.querySelectorAll('.wc-block-components-order-summary-item');

		items.forEach(function (item, index) {
			var ext = item.extensions && item.extensions[NAMESPACE];
			var row = rows[index];

			if (!ext || !row) {
				return;
			}

			var metadata = row.querySelector('.wc-block-components-product-metadata');
			var description = row.querySelector('.wc-block-components-order-summary-item__description');
			var prices = row.querySelector('.wc-block-cart-item__prices');

			if (metadata && ext.klasa_stanu && !metadata.querySelector('.qutlet-cart-badges')) {
				var dot = escHtml(String(ext.klasa_stanu).toLowerCase());
				var badges = document.createElement('div');
				badges.className = 'qutlet-cart-badges';
				badges.innerHTML =
					'<span class="pill"><span class="dot dot-' + dot + '"></span>Klasa ' + escHtml(ext.klasa_stanu) + '</span>' +
					'<span class="pill">Gwarancja 1 rok</span>';
				metadata.appendChild(badges);
			}

			if (description && prices) {
				if (ext.old_price_formatted && !description.querySelector('.cart-old-price')) {
					var oldPrice = document.createElement('small');
					oldPrice.className = 'cart-old-price';
					oldPrice.innerHTML =
						'<span class="cart-old-price-label">Nowy za</span>' +
						'<span class="cart-old-price-value">' + ext.old_price_formatted + '</span>';
					prices.insertAdjacentElement('afterend', oldPrice);
				}

				if (ext.item_savings_formatted && !description.querySelector('.qutlet-item-savings')) {
					var savings = document.createElement('small');
					savings.className = 'qutlet-item-savings';
					savings.innerHTML = 'Oszczędzasz ' + ext.item_savings_formatted;
					description.appendChild(savings);
				}
			}
		});
	}

	function injectItemBadges() {
		var data = getCartData();

		if (!data) {
			return;
		}

		var containers = document.querySelectorAll('.wc-block-components-order-summary__content');

		containers.forEach(function (container) {
			injectItemBadgesInto(container, data.items || []);
		});
	}

	/**
	 * Etykiety natywnych wierszy podsumowania — port terminologii prototypu
	 * (`kasa.html:103,106` — „Wartość produktów"/„Razem"), DOM injection (nie
	 * filtr tłumaczeń — string leci z bundla bloku, jak w koszyku, patrz
	 * renameFooterTotalLabel() w cart-block-filters.js). W przeciwieństwie do
	 * koszyka natywny wiersz „Kwota" (subtotal) ZAWSZE się renderuje w kasie
	 * (nie znika, gdy równa się sumie) — bez potrzeby budowania własnego
	 * wiersza `.qutlet-cart-subtotal-row` jak w Cart::cart_totals_data().
	 */
	function renameSummaryLabels() {
		document.querySelectorAll('.wc-block-components-totals-item__label').forEach(function (label) {
			if (label.textContent === 'Kwota') {
				label.textContent = 'Wartość produktów';
			} else if (label.textContent === 'Łącznie') {
				label.textContent = 'Razem';
			}
		});
	}

	/**
	 * Zielony box „Oszczędzasz vs. nowe" (port `data-co-savings-row`,
	 * `kasa.html:105`) — ten sam Store API `cart.extensions.total_savings_formatted`
	 * co w koszyku (Cart::cart_totals_data()), TA SAMA klasa
	 * `.qutlet-cart-savings-note` co cart-block-filters.js (jedna reguła CSS,
	 * dwa miejsca wstrzyknięcia — identyczny box w obu blokach, D-12.G2).
	 */
	function injectSavingsRow() {
		var data = getCartData();
		var ext = data && data.extensions && data.extensions[NAMESPACE];
		var totalsBlock = document.querySelector('.wp-block-woocommerce-checkout-order-summary-totals-block');

		if (!totalsBlock) {
			return;
		}

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

	function inject() {
		injectItemBadges();
		renameSummaryLabels();
		injectSavingsRow();
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

	/**
	 * `wp.data.subscribe()` (wyżej) NIE WYSTARCZA na tej stronie — zweryfikowane
	 * runtime, Playwright, sesja 2026-08-06: mobilny podgląd „Slot/Fill"
	 * podsumowania (patrz nagłówek pliku) montuje się WŁASnym, opóźnionym
	 * cyklem Reacta, niezależnym od zmian sklepu `wc/store/cart` — pierwszy
	 * `inject()` (na `DOMContentLoaded`) trafiał na jeszcze niezamontowane
	 * `.wc-block-components-order-summary-item`, a kolejne wywołania sklepu
	 * (dane koszyka już gotowe wcześniej) nie odpalały się ponownie PO
	 * faktycznym zamontowaniu tych węzłów. `MutationObserver` na całym
	 * `<body>` to niezawodny fallback niezależny od tego, CO dokładnie
	 * wywołuje re-render (sklep, lokalny stan, Suspense) — `inject()` i tak
	 * jest idempotentny (`!el.querySelector(...)` guardy w każdej funkcji),
	 * więc częste, nadmiarowe wywołania są tanie i bezpieczne.
	 */
	if (window.MutationObserver) {
		new MutationObserver(scheduleInject).observe(document.body, { childList: true, subtree: true });
	}
})();
