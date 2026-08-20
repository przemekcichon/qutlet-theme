/**
 * Qutlet — rejestracja klienta (edytor) bloku dynamicznego menu
 * nawigacyjnego nagłówka (P-16.1). Ten sam wzorzec bez build-stepu co
 * `home-blocks-editor.js`/`blog-blocks-editor.js` — osobny plik/kategoria
 * (slice HeaderMenu, patrz nagłówek `HeaderMenu\Blocks`).
 *
 * Lista nazw MUSI być zsynchronizowana ręcznie z katalogami w
 * `inc/features/HeaderMenu/blocks/*` (brak automatycznego odkrywania po
 * stronie klienta) — dodanie nowego bloku wymaga dopisania go tutaj.
 */
( function ( blocks, element, blockEditor, serverSideRender ) {
	'use strict';

	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var ServerSideRender = serverSideRender;

	var BLOCK_NAMES = [
		'qutlet/header-nav',
		'qutlet/header-categories-band',
		'qutlet/header-mega-grid',
		'qutlet/header-mobile-nav',
	];

	BLOCK_NAMES.forEach( function ( name ) {
		blocks.registerBlockType( name, {
			edit: function ( props ) {
				var blockProps = useBlockProps( { className: 'qutlet-block-preview' } );

				return el(
					'div',
					blockProps,
					el( ServerSideRender, {
						block: name,
						attributes: props.attributes,
					} )
				);
			},
			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender );
