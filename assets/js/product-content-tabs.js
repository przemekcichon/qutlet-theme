/**
 * Qutlet — zakładki treści produktu („Co w przesyłce" / „Opis i specyfikacja")
 * i karuzela zdjęć zestawu (port zachowania design/vanilla/js/app.js). Podpis
 * karuzeli bierze się z `data-car-label` na aktywnym slajdzie (etykieta pozycji
 * z ACF), nie z literałów w tym pliku — każdy produkt ma inny zestaw.
 */
document.addEventListener( 'click', function ( e ) {
	var tab = e.target.closest( '[data-pd-tab]' );

	if ( tab ) {
		var pane = tab.getAttribute( 'data-pd-tab' );

		document.querySelectorAll( '[data-pd-tab]' ).forEach( function ( btn ) {
			btn.classList.toggle( 'active', btn === tab );
		} );
		document.querySelectorAll( '[data-pd-pane]' ).forEach( function ( p ) {
			p.hidden = p.getAttribute( 'data-pd-pane' ) !== pane;
		} );

		return;
	}

	var carPrev = e.target.closest( '[data-car-prev]' );
	var carNext = e.target.closest( '[data-car-next]' );
	var carDot  = e.target.closest( '[data-car-dot]' );

	if ( carPrev ) {
		setCarouselSlide( carPrev.closest( '.carousel' ), currentCarouselIndex( carPrev.closest( '.carousel' ) ) - 1 );
	} else if ( carNext ) {
		setCarouselSlide( carNext.closest( '.carousel' ), currentCarouselIndex( carNext.closest( '.carousel' ) ) + 1 );
	} else if ( carDot ) {
		setCarouselSlide( carDot.closest( '.carousel' ), Number( carDot.getAttribute( 'data-car-dot' ) ) );
	}
} );

var carouselIndexes = new WeakMap();

function currentCarouselIndex( carousel ) {
	return carousel ? ( carouselIndexes.get( carousel ) || 0 ) : 0;
}

function setCarouselSlide( carousel, index ) {
	if ( ! carousel ) {
		return;
	}

	var track = carousel.querySelector( '[data-car-track]' );

	if ( ! track || ! track.children.length ) {
		return;
	}

	var count = track.children.length;
	var idx   = ( index + count ) % count;

	carouselIndexes.set( carousel, idx );
	track.style.transform = 'translateX(-' + ( idx * 100 ) + '%)';

	carousel.querySelectorAll( '[data-car-dot]' ).forEach( function ( dot, dotIndex ) {
		dot.classList.toggle( 'active', dotIndex === idx );
	} );

	var caption = carousel.querySelector( '[data-car-caption]' );

	if ( caption ) {
		var label = track.children[ idx ].getAttribute( 'data-car-label' ) || '';

		caption.textContent = 'Zdjęcie ' + ( idx + 1 ) + ' z ' + count + ( label ? ' — ' + label : '' );
	}
}

document.querySelectorAll( '.carousel' ).forEach( function ( carousel ) {
	setCarouselSlide( carousel, 0 );
} );
