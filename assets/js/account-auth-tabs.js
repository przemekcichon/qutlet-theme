/**
 * Qutlet — przełącznik zakładek Logowanie/Rejestracja na `/moje-konto/` w
 * stanie wylogowanym (port `data-auth-tab`/`data-auth-pane`,
 * design/vanilla/js/app.js, `initAuth`). Oba formularze renderują się
 * zawsze po stronie serwera (`woocommerce/myaccount/form-login.php`) — ten
 * skrypt tylko przełącza widoczność, no-op gdy `.auth-tabs` nie istnieje w
 * DOM (czyli klient jest zalogowany i widzi pulpit, nie formularz).
 */
( function () {
	'use strict';

	function switchTab( pane ) {
		document.querySelectorAll( '[data-auth-tab]' ).forEach( function ( btn ) {
			btn.classList.toggle( 'active', btn.getAttribute( 'data-auth-tab' ) === pane );
		} );
		document.querySelectorAll( '[data-auth-pane]' ).forEach( function ( form ) {
			form.hidden = form.getAttribute( 'data-auth-pane' ) !== pane;
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var tab = e.target.closest( '[data-auth-tab]' );

		if ( tab ) {
			switchTab( tab.getAttribute( 'data-auth-tab' ) );
		}
	} );

	/**
	 * Deep-link `#register` z linku „Zarejestruj się" w dropdownie konta
	 * (`Account::render_account_menu()`) — nasłuch `hashchange` DOPISANY po
	 * niezależnej recenzji PR #24: pierwsza wersja czytała `location.hash`
	 * WYŁĄCZNIE raz przy starcie skryptu, więc klik na link kończący się
	 * `#register`, gdy klient JEST JUŻ na `/moje-konto/`, zmieniał tylko hash
	 * (nawigacja tego samego dokumentu — brak przeładowania) i zakładka się
	 * nie przełączała.
	 */
	function applyHash() {
		if ( 'register' === window.location.hash.replace( '#', '' ) && document.querySelector( '[data-auth-tab="register"]' ) ) {
			switchTab( 'register' );
		}
	}

	window.addEventListener( 'hashchange', applyHash );
	applyHash();
} )();
