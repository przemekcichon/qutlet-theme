/**
 * Qutlet — przełącznik zakładek Logowanie/Rejestracja na `/moje-konto/` w
 * stanie wylogowanym (port `data-auth-tab`/`data-auth-pane`,
 * design/vanilla/js/app.js, `initAuth`). Oba formularze renderują się
 * zawsze po stronie serwera (`woocommerce/myaccount/form-login.php`) — ten
 * skrypt tylko przełącza widoczność, no-op gdy `.auth-tabs` nie istnieje w
 * DOM (czyli klient jest zalogowany i widzi pulpit, nie formularz).
 */
document.addEventListener( 'click', function ( e ) {
	var tab = e.target.closest( '[data-auth-tab]' );

	if ( ! tab ) {
		return;
	}

	var pane = tab.getAttribute( 'data-auth-tab' );

	document.querySelectorAll( '[data-auth-tab]' ).forEach( function ( btn ) {
		btn.classList.toggle( 'active', btn === tab );
	} );
	document.querySelectorAll( '[data-auth-pane]' ).forEach( function ( form ) {
		form.hidden = form.getAttribute( 'data-auth-pane' ) !== pane;
	} );
} );

if ( 'register' === window.location.hash.replace( '#', '' ) ) {
	var registerTab = document.querySelector( '[data-auth-tab="register"]' );

	if ( registerTab ) {
		registerTab.click();
	}
}
