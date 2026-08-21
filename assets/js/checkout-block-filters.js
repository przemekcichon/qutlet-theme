/**
 * Qutlet — podpis pozycji (klasa/gwarancja/reklamacja/ilość) w podsumowaniu
 * zamówienia bloku Checkout (D-8.6b.1).
 *
 * Czyta TE SAME dane Store API co koszyk (`item.extensions['qutlet-klasa']`,
 * zarejestrowane raz w inc/features/Cart/Cart.php — D-12.G2, potwierdzone
 * runtime: blok Checkout czyta z tego samego zasobu `wc/store/cart` co blok
 * Cart). Bez build stepu — global `window.wp.data` (dependency script
 * handles `wc-blocks-data-store`/`wp-data`, patrz
 * CheckoutBlocksIntegration::initialize()).
 *
 * ZAKRES ZWĘŻONY (decyzja użytkownika, sesja 2026-08-06, PO ground-truth
 * porównaniu z prototypem `kasa.html`/`QT.tpl.checkoutItem` —
 * design/vanilla/js/templates.js:116-123): prototyp pokazuje w wierszu
 * pozycji WYŁĄCZNIE miniaturkę/nazwę/cenę + zwykły tekstowy podpis
 * „Klasa X · N szt." — BEZ kolorowych chipów, BEZ starej ceny („Nowy za"),
 * BEZ osobnej pigułki „Oszczędzasz" per pozycja. Użytkownik rozszerzył
 * podpis o gwarancję i reklamację (drugi i trzeci fakt z D-12.G2 — reklamacja
 * dołączona w P-12.1b, gdy byt klas stanu z FAZY 12 był już gotowy) i
 * ZREZYGNOWAŁ ze starej ceny/oszczędności — zarówno per pozycja, JAK I w
 * zbiorczym podsumowaniu
 * (`total_savings_formatted`, wcześniej `.qutlet-cart-savings-note` —
 * usunięte razem z całym mechanizmem `injectSavingsRow`, poprzednia wersja
 * tego pliku). Miniaturka przestylowana na wygląd koszyka (style.css).
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
 * Wstrzykiwane jako WĘZEŁ DOM (`wp.data.subscribe()`), z tego samego powodu
 * co w koszyku: `registerCheckoutFilters` zanieczyszczał aria-label surowym
 * HTML (zweryfikowane w P-8.6a). Tekst wstawiany przez `textContent`
 * (NIE `innerHTML`) — bez ręcznego escapowania, przeglądarka sama traktuje
 * wartość jako zwykły tekst.
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
	 * Podpis pozycji „Klasa {X} · Gwarancja {Y} · Reklamacja {Z} · {N} szt."
	 * (D-12.G2 — te same TRZY fakty co w koszyku; P-12.1b: `gwarancja_text`/
	 * `reklamacja_text` czytane z bytu klas stanu przez
	 * `\Qutlet\Core\Cart\CartStoreApiData::cart_item_data()`
	 * — TEN SAM endpoint Store API `cart-item`, D-12.G2, bez osobnej rejestracji
	 * dla Checkout. Dawniej gwarancja była statycznym literałem „1 rok" wpisanym
	 * wprost w ten plik, a reklamacja nie była pokazywana wcale — usunięte w
	 * P-12.1b. „szt." to niedomienny skrót, port `QT.tpl.checkoutItem`, nie
	 * odmienia się przez liczbę, patrz nagłówek pliku). Wstrzykiwane do
	 * `.wc-block-components-product-metadata` — pusty węzeł już
	 * przygotowany przez WC Blocks na dokładnie ten cel (metadane pozycji).
	 *
	 * ZAKRES WCIĄŻ ZWĘŻONY (patrz nagłówek pliku): tekstowy podpis bez
	 * kolorowych chipów — `klasa_kolor` (P-12.1b) świadomie NIE jest tu użyty.
	 */
	function injectItemMetaInto(container, items) {
		var rows = container.querySelectorAll('.wc-block-components-order-summary-item');

		items.forEach(function (item, index) {
			var ext = item.extensions && item.extensions[NAMESPACE];
			var row = rows[index];

			if (!ext || !ext.klasa_stanu || !row) {
				return;
			}

			var metadata = row.querySelector('.wc-block-components-product-metadata');

			if (!metadata || metadata.querySelector('.qutlet-summary-meta')) {
				return;
			}

			var text = 'Klasa ' + ext.klasa_stanu;

			if (ext.gwarancja_text) {
				text += ' · ' + ext.gwarancja_text;
			}

			if (ext.reklamacja_text) {
				text += ' · ' + ext.reklamacja_text;
			}

			text += ' · ' + item.quantity + ' szt.';

			var meta = document.createElement('small');
			meta.className = 'qutlet-summary-meta';
			meta.textContent = text;
			metadata.appendChild(meta);
		});
	}

	function injectItemMeta() {
		var data = getCartData();

		if (!data) {
			return;
		}

		var containers = document.querySelectorAll('.wc-block-components-order-summary__content');

		containers.forEach(function (container) {
			injectItemMetaInto(container, data.items || []);
		});
	}

	/**
	 * Etykiety natywnych wierszy podsumowania — port terminologii prototypu
	 * (`kasa.html:103,106` — „Wartość produktów"/„Razem"), DOM injection (nie
	 * filtr tłumaczeń — string leci z bundla bloku, jak w koszyku, patrz
	 * renameFooterTotalLabel() w cart-block-filters.js). W przeciwieństwie do
	 * koszyka natywny wiersz „Kwota" (subtotal) ZAWSZE się renderuje w kasie
	 * (nie znika, gdy równa się sumie) — bez potrzeby budowania własnego
	 * wiersza `.qutlet-cart-subtotal-row` jak w
	 * `\Qutlet\Core\Cart\CartStoreApiData::cart_totals_data()`.
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

	function inject() {
		injectItemMeta();
		renameSummaryLabels();
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
	 * podsumowania (patrz nagłówek pliku) montuje się WŁASNYM, opóźnionym
	 * cyklem Reacta, niezależnym od zmian sklepu `wc/store/cart` — pierwszy
	 * `inject()` (na `DOMContentLoaded`) trafiał na jeszcze niezamontowane
	 * `.wc-block-components-order-summary-item`, a kolejne wywołania sklepu
	 * (dane koszyka już gotowe wcześniej) nie odpalały się ponownie PO
	 * faktycznym zamontowaniu tych węzłów. `MutationObserver` na całym
	 * `<body>` to niezawodny fallback niezależny od tego, CO dokładnie
	 * wywołuje re-render (sklep, lokalny stan, Suspense).
	 *
	 * KRYTYCZNE (niezależna recenzja, sesja 2026-08-06, dotyczyło USUNIĘTEJ
	 * już `injectSavingsRow`, ale zasada zostaje udokumentowana): ten
	 * obserwator reaguje na WŁASNE mutacje `inject()`, więc `inject()` MUSI
	 * być NAPRAWDĘ idempotentny — mutacja WYŁĄCZNIE przy realnej zmianie
	 * (guardy `!metadata.querySelector(...)` / porównanie `textContent`
	 * przed nadpisaniem w `renameSummaryLabels`), inaczej ten
	 * `MutationObserver` zamienia się w busy-loop mutacji.
	 */
	if (window.MutationObserver) {
		new MutationObserver(scheduleInject).observe(document.body, { childList: true, subtree: true });
	}
})();
