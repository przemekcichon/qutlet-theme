/**
 * Qutlet — AJAX progressive enhancement nad klasycznym GET+WP_Query filtrem
 * archiwum (P-8.3d, D-8.3d.1/D-8.3d.2/D-8.3d.4/D-8.3d.5). Ładowany PO
 * `product-filters.js` (zależność w `wp_enqueue_script`) — ten plik NIE
 * duplikuje drawer/readout, tylko dokłada `fetch()` + `pushState` zamiast
 * przeładowania strony.
 *
 * Czysta nakładka (progressive enhancement): jeśli `fetch`/`history.pushState`
 * niedostępne w przeglądarce, jeśli węzeł `#qutlet-archive-results` nie
 * istnieje, albo jeśli żądanie zawiedzie (sieć/status/JSON) — kod spada do
 * zwykłej nawigacji (`window.location.href`), czyli DOKŁADNIE tego, co
 * zrobiłby klasyczny formularz (D-8.3b.1). Nigdy nie zostawia użytkownika
 * bez działającego filtra.
 *
 * Listenery `submit`/`change`/`click` są rejestrowane w FAZIE PRZECHWYTYWANIA
 * (`capture: true`) na `document`, żeby zdeterminowanie (niezależnie od
 * kolejności ładowania skryptów) uprzedzić klasyczny listener sortowania z
 * `product-filters.js` (`sortSelect.form.submit()` na `change`) — bez tego,
 * zmiana sortowania wywołałaby RÓWNOLEGLE prawdziwe przeładowanie strony I
 * nasz fetch. `event.stopPropagation()` wołane WYŁĄCZNIE gdy faktycznie
 * przejmujemy zdarzenie (inne kliknięcia/zmiany — np. przycisk otwierania
 * szuflady — przechodzą normalnie do `product-filters.js`).
 */
(function () {
	'use strict';

	if (!window.fetch || !window.history || !window.history.pushState || !window.AbortController) {
		return;
	}

	var config = window.qutletFiltersAjax || {};
	var HEADER_NAME = config.headerName || 'X-Qutlet-Ajax-Filters';
	var RESULTS_ID = config.resultsId || 'qutlet-archive-results';

	var pendingController = null;

	function resultsEl() {
		return document.getElementById(RESULTS_ID);
	}

	function setLoading(on) {
		var el = resultsEl();
		if (el) el.classList.toggle('is-loading', !!on);
	}

	/**
	 * Pobiera i podmienia fragment (D-8.3d.2/D-8.3d.3). `opts.pushState`
	 * (domyślnie true) steruje aktualizacją adresu — `false` przy re-fetchu
	 * po `popstate` (adres już zmieniony przez przeglądarkę, D-8.3d.5).
	 */
	function loadArchive(url, opts) {
		opts = opts || {};
		var el = resultsEl();

		if (!el) {
			window.location.href = url;
			return;
		}

		if (pendingController) pendingController.abort();
		var controller = new AbortController();
		pendingController = controller;

		setLoading(true);

		var headers = {};
		headers[HEADER_NAME] = '1';

		fetch(url, { method: 'GET', credentials: 'same-origin', signal: controller.signal, headers: headers })
			.then(function (response) {
				if (!response.ok) throw new Error('qutlet-ajax-filters: bad status ' + response.status);
				return response.json();
			})
			.then(function (payload) {
				if (!payload || !payload.success || !payload.data || typeof payload.data.html !== 'string') {
					throw new Error('qutlet-ajax-filters: bad payload');
				}

				var holder = document.createElement('div');
				holder.innerHTML = payload.data.html;
				var fresh = holder.firstElementChild;

				if (!fresh || fresh.id !== RESULTS_ID) {
					throw new Error('qutlet-ajax-filters: unexpected fragment');
				}

				el.replaceWith(fresh);

				if (opts.pushState !== false) {
					window.history.pushState({ qutletAjax: true }, '', url);
				}

				fresh.scrollIntoView({ block: 'nearest' });
			})
			.catch(function (err) {
				if (err && err.name === 'AbortError') return;
				window.location.href = url;
			})
			.then(function () {
				if (pendingController === controller) {
					pendingController = null;
					setLoading(false);
				}
			});
	}

	function formUrl(form) {
		var params = new URLSearchParams(new FormData(form));
		var action = form.getAttribute('action') || window.location.href;
		var qs = params.toString();
		return qs ? action + (action.indexOf('?') === -1 ? '?' : '&') + qs : action;
	}

	document.addEventListener(
		'submit',
		function (e) {
			var form = e.target.closest('.qutlet-filters-form');
			if (!form) return;

			e.preventDefault();
			e.stopPropagation();
			loadArchive(formUrl(form));
		},
		true
	);

	document.addEventListener(
		'change',
		function (e) {
			var form = e.target.closest('.qutlet-filters-form');
			if (!form) return;

			var isFacetCheckbox = e.target.matches('input[type="checkbox"]');
			var isSort = e.target.matches('[data-sort-autosubmit]');
			var isPriceRange = e.target.matches('[data-range-min], [data-range-max]');

			if (!isFacetCheckbox && !isSort && !isPriceRange) return;

			e.stopPropagation();
			loadArchive(formUrl(form));
		},
		true
	);

	document.addEventListener(
		'click',
		function (e) {
			var link = e.target.closest('#' + RESULTS_ID + ' a[href]');
			if (!link) return;
			if (link.target && link.target !== '_self') return;

			e.preventDefault();
			e.stopPropagation();
			loadArchive(link.getAttribute('href'));
		},
		true
	);

	window.addEventListener('popstate', function () {
		loadArchive(window.location.href, { pushState: false });
	});
})();
