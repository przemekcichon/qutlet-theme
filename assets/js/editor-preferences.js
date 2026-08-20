/**
 * Qutlet — domyślne wyłączenie podglądu szablonu (nagłówek/stopka) w
 * edytorze Stron/Wpisów (P-25.3b, D-25.3.1). `setDefaults` ustawia WYŁĄCZNIE
 * wartość domyślną preferencji — użytkownik z już zapisanym własnym wyborem
 * (`template-locked`) nie jest nadpisywany wstecznie.
 */
( function ( data ) {
	'use strict';

	data.dispatch( 'core/preferences' ).setDefaults( 'core', {
		renderingModes: {
			'qutlet-theme': {
				page: 'post-only',
				post: 'post-only',
			},
		},
	} );
} )( wp.data );
