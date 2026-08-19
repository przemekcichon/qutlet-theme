/**
 * Qutlet — widget „Inne sztuki tego modelu" (`#ism`, P-22.4): przełącznik
 * układu lista/kafelki i scroll karuzeli kafelków (port zachowania
 * design/vanilla/produkt-inne-sztuki.html).
 */
var ismBand = document.getElementById( 'ism' );

if ( ismBand ) {
	var ismTrack = ismBand.querySelector( '[data-ism-track]' );
	var ismWrap  = ismTrack ? ismTrack.closest( '.ism-cards-wrap' ) : null;
	var ismPrev  = ismBand.querySelector( '[data-ism-prev]' );
	var ismNext  = ismBand.querySelector( '[data-ism-next]' );

	var updateIsmCarousel = function () {
		if ( ! ismTrack || ! ismWrap ) {
			return;
		}

		var max = ismTrack.scrollWidth - ismTrack.clientWidth;

		ismWrap.classList.toggle( 'has-overflow', max > 2 );

		if ( ismPrev ) {
			ismPrev.disabled = ismTrack.scrollLeft <= 2;
		}

		if ( ismNext ) {
			ismNext.disabled = ismTrack.scrollLeft >= max - 2;
		}
	};

	var stepIsmCarousel = function ( direction ) {
		if ( ! ismTrack ) {
			return;
		}

		var card = ismTrack.querySelector( '.ism-card' );
		var amount = card ? card.getBoundingClientRect().width + 15 : 260;

		ismTrack.scrollBy( { left: direction * amount, behavior: 'smooth' } );
	};

	if ( ismPrev ) {
		ismPrev.addEventListener( 'click', function () {
			stepIsmCarousel( -1 );
		} );
	}

	if ( ismNext ) {
		ismNext.addEventListener( 'click', function () {
			stepIsmCarousel( 1 );
		} );
	}

	if ( ismTrack ) {
		ismTrack.addEventListener( 'scroll', updateIsmCarousel, { passive: true } );
	}

	window.addEventListener( 'resize', updateIsmCarousel );

	ismBand.querySelectorAll( '[data-ism-layout]' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			ismBand.setAttribute( 'data-layout', button.getAttribute( 'data-ism-layout' ) );
			ismBand.querySelectorAll( '[data-ism-layout]' ).forEach( function ( other ) {
				other.classList.toggle( 'active', other === button );
			} );
			requestAnimationFrame( updateIsmCarousel );
		} );
	} );

	updateIsmCarousel();

	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( updateIsmCarousel );
	}
}
