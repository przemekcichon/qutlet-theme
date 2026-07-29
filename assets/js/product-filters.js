/**
 * Qutlet — interakcje szuflady filtrów archiwum (P-8.3b, port zachowania
 * z design/vanilla/js/app.js `initDeals()` — otwieranie/zamykanie `.drawer`,
 * suwaki ceny). Samo FILTROWANIE/SORTOWANIE to klasyczny GET + przeładowanie
 * strony (formularz w woocommerce/loop/filters-and-sort.php) — ten skrypt
 * NIE dotyka danych, tylko widoczność szuflady i odczyt suwaka ceny przed
 * submitem.
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var overlay = document.querySelector('[data-drawer-overlay]');
		var drawer = document.querySelector('[data-drawer]');

		function setDrawer(open) {
			if (drawer) drawer.hidden = !open;
			if (overlay) overlay.hidden = !open;
		}

		document.addEventListener('click', function (e) {
			if (e.target.closest('[data-open-drawer]')) { setDrawer(true); return; }
			if (e.target.closest('[data-close-drawer]') || e.target === overlay) { setDrawer(false); }
		});

		var sortSelect = document.querySelector('[data-sort-autosubmit]');
		if (sortSelect) {
			sortSelect.addEventListener('change', function () {
				if (sortSelect.form) sortSelect.form.submit();
			});
		}

		var rangeMin = document.querySelector('[data-range-min]');
		var rangeMax = document.querySelector('[data-range-max]');
		var readoutMin = document.querySelector('[data-price-min]');
		var readoutMax = document.querySelector('[data-price-max]');

		function formatPrice(value) {
			return new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' }).format(Number(value));
		}

		if (rangeMin && rangeMax) {
			rangeMin.addEventListener('input', function () {
				if (Number(rangeMin.value) > Number(rangeMax.value)) rangeMin.value = rangeMax.value;
				if (readoutMin) readoutMin.textContent = formatPrice(rangeMin.value);
			});
			rangeMax.addEventListener('input', function () {
				if (Number(rangeMax.value) < Number(rangeMin.value)) rangeMax.value = rangeMin.value;
				if (readoutMax) readoutMax.textContent = formatPrice(rangeMax.value);
			});
		}
	});
})();
