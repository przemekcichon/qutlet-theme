/**
 * Qutlet — przełącznik kanału zakupu (taby Qutlet/Allegro), sticky buybar i
 * akordeon informacyjny „jak-to-dziala" (port zachowania design/vanilla/js/app.js).
 * Ceny w buybarze biorą się z `data-price-text` na przyciskach taba, nie z
 * literałów w tym pliku — każdy produkt ma inne ceny.
 */
document.addEventListener( 'click', function ( e ) {
	var tab = e.target.closest( '[data-buy-tab]' );

	if ( tab ) {
		var channel = tab.getAttribute( 'data-buy-tab' );

		document.querySelectorAll( '[data-buy-tab]' ).forEach( function ( btn ) {
			btn.classList.toggle( 'active', btn === tab );
		} );
		document.querySelectorAll( '[data-buy-pane]' ).forEach( function ( pane ) {
			pane.hidden = pane.getAttribute( 'data-buy-pane' ) !== channel;
		} );

		var price = document.querySelector( '[data-buybar-price]' );

		if ( price ) {
			price.textContent = tab.getAttribute( 'data-price-text' ) || '';
		}

		var isAllegro = 'allegro' === channel;

		document.querySelectorAll( '[data-buybar-qutlet]' ).forEach( function ( el ) {
			el.hidden = isAllegro;
		} );
		document.querySelectorAll( '[data-buybar-allegro]' ).forEach( function ( el ) {
			el.hidden = ! isAllegro;
		} );

		return;
	}

	var accBtn = e.target.closest( '[data-acc-btn]' );

	if ( accBtn ) {
		var body = accBtn.parentElement.querySelector( '[data-acc-body]' );
		var sign = accBtn.querySelector( '.sign' );

		if ( body ) {
			body.hidden = ! body.hidden;

			if ( sign ) {
				sign.textContent = body.hidden ? '+' : '−';
			}
		}
	}
} );

var buybar = document.querySelector( '[data-buybar]' );

if ( buybar ) {
	window.addEventListener(
		'scroll',
		function () {
			var pane = document.querySelector( '[data-buy-pane]:not([hidden])' ) || document.querySelector( 'main' );
			var anchor = pane ? ( pane.querySelector( '[data-buy-anchor]' ) || pane ) : null;
			var past = anchor ? anchor.getBoundingClientRect().bottom < 0 : false;

			buybar.classList.toggle( 'visible', past );
		},
		{ passive: true }
	);
}
