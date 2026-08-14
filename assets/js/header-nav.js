/**
 * Qutlet — interakcje header/footer (dropdown koszyk/konto, mega menu,
 * mobilna nawigacja, ukrywanie nagłówka przy scrollu).
 *
 * Wydzielone z design/vanilla/js/app.js (QT.header.init + initHideOnScroll):
 * TYLKO fragmenty bez zależności od danych. Renderowanie zawartości
 * dropdownów (koszyk/konto) i logika dodawania/usuwania z koszyka zostają
 * w prototypie — tu podpięcie do realnego WooCommerce/wp_users to osobny,
 * późniejszy punkt (P-8.6a/P-8.6c).
 */
(function () {
	'use strict';

	function initDropdowns() {
		document.addEventListener('click', function (e) {
			var toggle = e.target.closest('[data-toggle-menu]');
			if (toggle) {
				var name = toggle.getAttribute('data-toggle-menu');
				document.querySelectorAll('.dropdown').forEach(function (d) {
					if (d.getAttribute('data-menu') !== name) d.hidden = true;
				});
				var menu = document.querySelector('[data-menu="' + name + '"]');
				if (menu) menu.hidden = !menu.hidden;
				return;
			}
			if (!e.target.closest('.dropdown')) {
				document.querySelectorAll('.dropdown').forEach(function (d) { d.hidden = true; });
			}
		});
	}

	function initMegaMenu() {
		document.addEventListener('click', function (e) {
			var mega = document.querySelector('[data-mega]');
			if (!mega) return;
			if (e.target.closest('[data-toggle-mega]')) mega.hidden = !mega.hidden;
			else if (!e.target.closest('[data-mega]')) mega.hidden = true;
		});
	}

	function initMobileNav() {
		document.addEventListener('click', function (e) {
			var mnav = document.querySelector('[data-mnav]');
			if (!mnav) return;
			if (e.target.closest('[data-open-mnav]')) { mnav.hidden = false; return; }
			if (e.target.closest('[data-close-mnav]') || e.target === mnav) mnav.hidden = true;
		});
	}

	/* header chowa się przy scrollu w dół, wraca przy scrollu w górę */
	function initHideOnScroll() {
		var header = document.querySelector('.site-header');
		if (!header) return;
		var lastY = window.scrollY;
		var ticking = false;
		function update() {
			ticking = false;
			var y = window.scrollY;
			var delta = y - lastY;
			/* nie chowaj, gdy otwarte jest jakieś menu w headerze */
			var menuOpen = header.querySelector('[data-menu]:not([hidden]), [data-mega]:not([hidden])');
			if (y < 120 || menuOpen) {
				header.classList.remove('header-hidden');
				lastY = y;
			} else if (delta > 4) {
				header.classList.add('header-hidden');
				lastY = y;
			} else if (delta < -4) {
				header.classList.remove('header-hidden');
				lastY = y;
			}
			/* w przeciwnym razie (|delta| <= 4) NIE nadpisuj lastY — przy wielu
			   drobnych przyrostach tego samego znaku (płynny/ciągły scroll,
			   każda klatka animation frame < 4px) delta musi się kumulować
			   względem ostatniego punktu odniesienia, inaczej próg nigdy nie
			   zostanie przekroczony i nagłówek zostaje w błędnym stanie mimo
			   dużego sumarycznego przesunięcia */
		}
		window.addEventListener('scroll', function () {
			if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
		}, { passive: true });
	}

	document.addEventListener('DOMContentLoaded', function () {
		initDropdowns();
		initMegaMenu();
		initMobileNav();
		initHideOnScroll();
	});
})();
