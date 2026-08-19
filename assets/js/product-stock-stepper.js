/**
 * Qutlet — custom stepper ilości (`.pd-stepper`, P-22.3) syncowany do
 * ukrytego natywnego inputa `quantity` (jedyne pole, które faktycznie
 * submituje się z formularzem koszyka — D-8.G1, theme nie duplikuje logiki
 * Woo). Min/max czytane z natywnego inputa (`wc_get_quantity_input_args()`,
 * już uwzględnia stan magazynowy i limity produktu), nie z `_stock`
 * ponownie.
 */
document.querySelectorAll( '[data-stepper]' ).forEach( function ( stepper ) {
	var pane   = stepper.closest( '[data-buy-pane]' ) || document;
	var input  = pane.querySelector( 'input[name="quantity"]' );
	var valEl  = stepper.querySelector( '[data-qty-val]' );
	var decBtn = stepper.querySelector( '[data-qty-dec]' );
	var incBtn = stepper.querySelector( '[data-qty-inc]' );

	if ( ! input || ! valEl || ! decBtn || ! incBtn ) {
		return;
	}

	var min = parseInt( input.min, 10 ) || 1;
	var max = input.max ? parseInt( input.max, 10 ) : Infinity;

	function setValue( next ) {
		next = Math.min( max, Math.max( min, next ) );

		valEl.textContent = String( next );
		input.value        = String( next );
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );

		decBtn.disabled = next <= min;
		incBtn.disabled = next >= max;
	}

	decBtn.addEventListener( 'click', function () {
		setValue( parseInt( valEl.textContent, 10 ) - 1 );
	} );
	incBtn.addEventListener( 'click', function () {
		setValue( parseInt( valEl.textContent, 10 ) + 1 );
	} );

	setValue( parseInt( input.value, 10 ) || min );
} );
