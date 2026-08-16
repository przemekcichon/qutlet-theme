/**
 * Qutlet — interakcje szuflady filtrów archiwum (P-8.3b, port zachowania
 * z design/vanilla/js/app.js `initDeals()` — otwieranie/zamykanie `.drawer`,
 * suwaki ceny). Samo FILTROWANIE/SORTOWANIE to klasyczny GET + przeładowanie
 * strony (formularz w woocommerce/loop/filters-and-sort.php) — ten skrypt
 * NIE dotyka danych, tylko widoczność szuflady i odczyt suwaka ceny przed
 * submitem.
 *
 * Wszystkie listenery są DELEGOWANE na `document` i odpytują DOM na bieżąco
 * (bez cache'owania węzłów w zmiennej domkniętej) — poprawka P-8.3d: gdy
 * `product-filters-ajax.js` podmienia `#qutlet-archive-results` przez
 * `outerHTML`, węzły `.drawer`/`[data-range-min]` sprzed podmiany znikają z
 * DOM; jednorazowy `querySelector()` przy starcie (poprzednia wersja tego
 * pliku) łapałby wtedy odniesienia do usuniętych elementów.
 */
(function () {
	'use strict';

	function setDrawer(open) {
		var drawer = document.querySelector('[data-drawer]');
		var overlay = document.querySelector('[data-drawer-overlay]');
		if (drawer) drawer.hidden = !open;
		if (overlay) overlay.hidden = !open;
	}

	document.addEventListener('click', function (e) {
		if (e.target.closest('[data-open-drawer]')) { setDrawer(true); return; }
		if (e.target.closest('[data-close-drawer]') || e.target.matches('[data-drawer-overlay]')) { setDrawer(false); }
	});

	document.addEventListener('change', function (e) {
		if (e.target.matches('[data-sort-autosubmit]') && e.target.form) {
			e.target.form.submit();
		}
	});

	function formatPrice(value) {
		return new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' }).format(Number(value));
	}

	document.addEventListener('input', function (e) {
		if (e.target.matches('[data-range-min]')) {
			var rangeMax = document.querySelector('[data-range-max]');
			if (rangeMax && Number(e.target.value) > Number(rangeMax.value)) e.target.value = rangeMax.value;
			var readoutMin = document.querySelector('[data-price-min]');
			if (readoutMin) readoutMin.textContent = formatPrice(e.target.value);
			return;
		}
		if (e.target.matches('[data-range-max]')) {
			var rangeMin = document.querySelector('[data-range-min]');
			if (rangeMin && Number(e.target.value) < Number(rangeMin.value)) e.target.value = rangeMin.value;
			var readoutMax = document.querySelector('[data-price-max]');
			if (readoutMax) readoutMax.textContent = formatPrice(e.target.value);
		}
	});
})();
