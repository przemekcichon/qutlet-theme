/**
 * Qutlet — galeria produktu (port zachowania design/vanilla/js/app.js).
 * Klik w miniaturkę podmienia treść głównego zdjęcia. Te same atrybuty co w
 * prototypie: data-gallery-main / data-gallery-thumb / data-main-html.
 */
document.addEventListener( 'click', function ( e ) {
	var thumb = e.target.closest( '[data-gallery-thumb]' );

	if ( ! thumb ) {
		return;
	}

	document.querySelectorAll( '[data-gallery-thumb]' ).forEach( function ( el ) {
		el.classList.remove( 'active' );
	} );
	thumb.classList.add( 'active' );

	var main = document.querySelector( '[data-gallery-main]' );

	if ( main ) {
		main.innerHTML = thumb.getAttribute( 'data-main-html' );
	}
} );
