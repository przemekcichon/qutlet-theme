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
 *
 * Jeden wyjątek od „tylko odczyt/wklejanie stringów": dropdown ilości
 * (`injectQuantityDropdown()`) DYSPATCHUJE do store'a
 * (`dispatch('wc/store/cart').changeCartItemQuantity()`) — zamienia ilość
 * w koszyku, nie tylko ją wyświetla. Sam mechanizm wstrzykiwania (nowy
 * węzeł DOM, natywny stepper schowany CSS-em, nie usunięty) jest
 * identyczny z resztą pliku.
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
	 * `klasa_stanu`/`klasa_kolor`/`gwarancja_text`/`reklamacja_text` pochodzą z
	 * bytu „klasa stanu" (P-12.1a/b, taksonomia edytowalna w adminie — od
	 * P-12.1a NIE jest już zamkniętym słownikiem ACF A-D), więc escapowanie
	 * przed wklejeniem do innerHTML jest realną obroną w głąb, nie formalnością.
	 */
	function escHtml(value) {
		return String(value).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	/**
	 * Odznaka klasy stanu + gwarancja + reklamacja (P-12.1b — trzeci fakt,
	 * dawniej nieobecny w koszyku, D-12.G2) w jednej linii ze starą ceną —
	 * osobne węzły DOM na tym samym wierszu gridu, dwie kolumny), ikonka „?"
	 * z pełną nazwą w tooltipie obok (możliwe) przyciętej nazwy produktu —
	 * żadna z tych operacji nie modyfikuje istniejących stringów (patrz
	 * nagłówek pliku).
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
			var wrap = row.querySelector('.wc-block-cart-item__wrap');

			if (!wrap) {
				return;
			}

			// Ikonka "?" — WYŁĄCZNIE dopisana jako nowy węzeł do `wrap`, NIGDY
			// nie owijamy/przenosimy `nameEl` samego (poprzednia wersja robiła
			// `nameRow.appendChild(nameEl)` — przenosiła węzeł Reacta do nowego
			// rodzica). Cart Block to komponent Reacta: przeniesienie jego
			// węzła psuje wewnętrzne śledzenie DOM przez Reacta, a przy
			// najbliższym re-renderze (np. usunięcie INNEJ pozycji z koszyka,
			// przeliczenie sumy) React próbuje `removeChild` węzła, który już
			// nie jest dzieckiem oczekiwanego rodzica → crash "Unexpected
			// error in: woocommerce/cart-line-items-block" (zgłoszone przez
			// użytkownika — błąd występował tylko po dodaniu tego wrappera).
			// Pozycja ikonki „za nazwą" liczona w JS (`updateNameTruncation()`,
			// `margin-left` na podstawie realnej renderowanej szerokości
			// nazwy) — obie są w TEJ SAMEJ komórce gridu (kolumna 1, wiersz 1),
			// bez wspólnego kontenera.
			if (nameEl && !wrap.querySelector('.qutlet-name-tooltip')) {
				var tip = document.createElement('span');
				tip.className = 'qutlet-name-tooltip';
				tip.textContent = '?';
				tip.title = item.name || '';
				wrap.appendChild(tip);
			}

			if (ext.klasa_stanu && !wrap.querySelector('.qutlet-cart-badges')) {
				// Kolor odznaki jako inline `style` (nie klasa CSS `dot-<litera>`) —
				// byt klas stanu (P-12.1a) jest rozszerzalny, nowe klasy (np. "Nowe")
				// nie mają gotowej reguły `.dot-<kod>` w style.css.
				var dotStyle = ext.klasa_kolor ? ' style="background:' + escHtml(ext.klasa_kolor) + '"' : '';
				var html =
					'<span class="pill"><span class="dot"' + dotStyle + '></span>Klasa ' + escHtml(ext.klasa_stanu) + '</span>';

				if (ext.gwarancja_text) {
					html += '<span class="pill">' + escHtml(ext.gwarancja_text) + '</span>';
				}

				if (ext.reklamacja_text) {
					html += '<span class="pill">' + escHtml(ext.reklamacja_text) + '</span>';
				}

				var badges = document.createElement('div');
				badges.className = 'qutlet-cart-badges';
				badges.innerHTML = html;
				wrap.appendChild(badges);
			}

			// Stara cena w JEDNEJ LINII z odznakami (ten sam wiersz gridu, druga
			// kolumna) — NIE wewnątrz `.wc-block-cart-item__prices` (tam by
			// stackowała się pod ceną sprzedaży, w innym wierszu niż odznaki).
			// Etykieta "Nowy za" (poza prototypem, na wyraźną prośbę
			// użytkownika) w osobnym spanie BEZ przekreślenia — przekreślona
			// jest tylko sama kwota (`.cart-old-price-value`).
			if (ext.old_price_formatted && !row.querySelector('.cart-old-price')) {
				var oldPrice = document.createElement('small');
				oldPrice.className = 'cart-old-price';
				oldPrice.innerHTML =
					'<span class="cart-old-price-label">Nowy za</span>' +
					'<span class="cart-old-price-value">' + ext.old_price_formatted + '</span>';
				wrap.appendChild(oldPrice);
			}

			// "Oszczędzasz X" per wiersz (na wyraźną prośbę użytkownika, sesja
			// 2026-08-06) — TEN SAM limonkowy kolor co `.qutlet-cart-savings-note`
			// w podsumowaniu, ale własna, mniejsza klasa: to pigułka w wierszu
			// produktu, nie pełnoszerokościowy box podsumowania. Brak
			// `cena_rynkowa_nowego` → `item_savings_formatted` puste (PHP,
			// Cart::cart_item_data()) → nic się nie wstawia, zgodnie z prośbą.
			if (ext.item_savings_formatted && !row.querySelector('.qutlet-item-savings')) {
				var savings = document.createElement('small');
				savings.className = 'qutlet-item-savings';
				savings.innerHTML = 'Oszczędzasz ' + ext.item_savings_formatted;
				wrap.appendChild(savings);
			}
		});

		updateNameTruncation();
	}

	/**
	 * Polska odmiana liczby sztuk — PORT `QT.qtyLabel`/`QT.plural`
	 * (design/vanilla/js/data.js:66-78, kontrakt §6 „Etykieta liczby sztuk").
	 * Literały ("Pojedyncza sztuka"/"sztuka"/"sztuki"/"sztuk") skopiowane
	 * 1:1 ze źródła prawdy, nie wymyślone na nowo.
	 */
	function plPlural(n, one, few, many) {
		if (n === 1) {
			return one;
		}

		var m10 = n % 10;
		var m100 = n % 100;

		if (m10 >= 2 && m10 <= 4 && !(m100 >= 12 && m100 <= 14)) {
			return few;
		}

		return many;
	}

	function qtyLabel(q) {
		if (q === 1) {
			return 'Pojedyncza sztuka';
		}

		return q + ' ' + plPlural(q, 'sztuka', 'sztuki', 'sztuk');
	}

	/**
	 * Dropdown ilości ZAMIAST steppera +/- (na wyraźną prośbę użytkownika,
	 * sesja 2026-08-06 — inspiracja zrzutami z koszyka Back Market: "idea
	 * sklepu jest taka, że najwięcej będzie pojedynczych sztuk", więc wybór
	 * z listy pasuje lepiej niż klikanie +/-). Natywny stepper Cart Blocka
	 * (`.wc-block-components-quantity-selector`, React) NIE jest usuwany z
	 * DOM — tylko chowany CSS-em (`display:none`, style.css) i pozostaje
	 * jedynym prawdziwym źródłem `min`/`max`/`value` (atrybuty na jego
	 * `<input>`, ustawiane przez WC ze stanu magazynowego). Nasz `<select>`
	 * to OSOBNY węzeł DOM dopisany do `.wc-block-cart-item__quantity` (ten
	 * sam wzorzec DOM-injection co reszta pliku — zero ruszania węzłów
	 * Reacta), zbudowany RAZ z opcji `min..max` odczytanych z natywnego
	 * inputu, zmiana wybiera nową ilość przez
	 * `wp.data.dispatch('wc/store/cart').changeCartItemQuantity(key, qty)`
	 * (zweryfikowane runtime — ten sam Store API, który już napędza
	 * odznaki/ceny/podsumowanie w tym pliku, więc re-render po zmianie
	 * ilości aktualizuje WSZYSTKO — sumy, „Oszczędzasz", mini-koszyk
	 * headera — bez dodatkowego kodu). Etykieta „Pojedyncza sztuka"/„2
	 * sztuki" (`qtyLabel()`) w osobnym, `aria-hidden` spanie — czysto
	 * wizualna, `<select>` ma już własny `aria-label` (skopiowany z
	 * natywnego inputu), więc czytnik ekranu nie usłyszy tego samego dwa
	 * razy.
	 */
	function injectQuantityDropdown() {
		var data = getCartData();

		if (!data) {
			return;
		}

		(data.items || []).forEach(function (item) {
			var row = document.querySelector('.wc-block-cart-items__row[data-cart-item-key="' + item.key + '"]');

			if (!row) {
				return;
			}

			var qtyContainer = row.querySelector('.wc-block-cart-item__quantity');
			var nativeInput = row.querySelector('.wc-block-components-quantity-selector__input');

			if (!qtyContainer || !nativeInput) {
				return;
			}

			var select = qtyContainer.querySelector('.qutlet-qty-select');

			if (!select) {
				var min = parseInt(nativeInput.min, 10) || 1;
				// `Math.max(…, item.quantity)` — jeśli obecna ilość w koszyku jest
				// (teoretycznie) POZA przeliczonym `max` natywnego inputu (np. stan
				// magazynowy spadł PO dodaniu do koszyka, w innej karcie/sesji),
				// opcja dla niej i tak istnieje — inaczej `<select>` nie miałby
				// gdzie ustawić bieżącej wartości (`select.value=…` niżej trafiłoby
				// w nieistniejącą opcję, natywny `<input type="number">` tego
				// problemu nie ma, bo pokazuje liczbę niezależnie od atrybutu `max`).
				// `Math.min(…, 50)` — górny cap na wszelki wypadek: produkt bez
				// zarządzanego stanu magazynowego dostaje z Store API `max=9999`
				// (`QuantityLimits::adjust_product_quantity_limit()`), co przy tym
				// modelu biznesowym (pojedyncze/limitowane egzemplarze) nie powinno
				// się zdarzyć, ale 9999 węzłów `<option>` byłoby bezsensowne, gdyby
				// się zdarzyło.
				var max = Math.max( Math.min( parseInt(nativeInput.max, 10) || min, 50 ), item.quantity );

				select = document.createElement('select');
				select.className = 'qutlet-qty-select';
				select.setAttribute('aria-label', nativeInput.getAttribute('aria-label') || '');
				select.disabled = max <= min;

				for (var q = min; q <= max; q++) {
					var option = document.createElement('option');
					option.value = String(q);
					option.textContent = String(q);
					select.appendChild(option);
				}

				select.addEventListener('change', function () {
					var dispatch = window.wp && window.wp.data && window.wp.data.dispatch;

					if (dispatch) {
						dispatch('wc/store/cart').changeCartItemQuantity(item.key, parseInt(select.value, 10));
					}
				});

				qtyContainer.appendChild(select);
			}

			if (select.value !== String(item.quantity)) {
				select.value = String(item.quantity);
			}

			var label = qtyContainer.querySelector('.qutlet-qty-label');

			if (!label) {
				label = document.createElement('span');
				label.className = 'qutlet-qty-label';
				label.setAttribute('aria-hidden', 'true');
				qtyContainer.appendChild(label);
			}

			label.textContent = qtyLabel(item.quantity);
		});
	}

	/**
	 * Ikonka "?" (pełna nazwa w tooltipie) widoczna WYŁĄCZNIE, gdy nazwa
	 * faktycznie się przycięła elipsą (`.wc-block-components-product-name`
	 * ma `max-width:80%` w CSS — krótkie nazwy się nie przycinają, więc nie
	 * powinny dostawać znaczka). Mierzone realnym `scrollWidth > clientWidth`,
	 * nie samą obecnością `max-width` w CSS — jedyny sposób odróżnić „nazwa
	 * zmieściła się w 80%" od „nazwa przekroczyła 80% i się przycięła".
	 *
	 * Nazwa i ikonka są w TEJ SAMEJ komórce gridu (kolumna 1, wiersz 1) —
	 * bez wspólnego kontenera (patrz komentarz w `injectItemBadges()` o tym,
	 * czemu nie owijamy `nameEl`) — więc pozycję ikonki „za nazwą" liczymy
	 * tu, `margin-left` na podstawie realnej (możliwe, że przyciętej)
	 * renderowanej szerokości nazwy (`clientWidth`, NIE `scrollWidth` — to
	 * byłaby szerokość PRZED przycięciem).
	 *
	 * Wywoływane po każdym wstrzyknięciu ORAZ po zmianie szerokości okna —
	 * przycięcie/pozycja zależą od dostępnej szerokości wiersza (patrz
	 * zgłoszenie o węższych ekranach), nie tylko od długości nazwy.
	 */
	function updateNameTruncation() {
		var wraps = document.querySelectorAll('.wc-block-cart-item__wrap');

		for (var i = 0; i < wraps.length; i++) {
			var nameEl = wraps[i].querySelector('.wc-block-components-product-name');
			var tip = wraps[i].querySelector('.qutlet-name-tooltip');

			if (!nameEl || !tip) {
				continue;
			}

			tip.classList.toggle('is-truncated', nameEl.scrollWidth > nameEl.clientWidth + 1);
			tip.style.marginLeft = (nameEl.clientWidth + 6) + 'px';
		}
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
	 * Etykieta wiersza „Razem" — WC-owy label to string „Estimated total"
	 * tłumaczony (paczka językowa community, nie nasz kod) na „Szacowana
	 * łączna kwota": poprawne, ale zbyt długie/toporne, na wyraźną prośbę
	 * użytkownika zamienione na krótkie „Razem" (port `.summary-total`
	 * prototypu, design/vanilla/koszyk.html:40). Podmiana przez DOM,
	 * NIE filtr tłumaczeń — string leci z JS-owego bundla bloku Cart
	 * (`wp.i18n`, nie PHP `__()`), więc `gettext`/`load_script_translation_file`
	 * by nie trafiły; DOM injection już i tak jest mechanizmem całego pliku.
	 */
	function renameFooterTotalLabel() {
		var label = document.querySelector('.wc-block-components-totals-footer-item .wc-block-components-totals-item__label');

		if (label && label.textContent !== 'Razem') {
			label.textContent = 'Razem';
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
		injectQuantityDropdown();
		injectSummaryRows();
		renameFooterTotalLabel();
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

	// Przycięcie nazwy zależy od dostępnej szerokości wiersza (nie tylko od
	// treści) — bez tego zmiana szerokości okna (np. obrót telefonu) mogłaby
	// zostawić znaczek "?" tam, gdzie nazwa już się zmieściła, albo odwrotnie.
	var resizeTimer = null;
	window.addEventListener('resize', function () {
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(updateNameTruncation, 150);
	});
})();
