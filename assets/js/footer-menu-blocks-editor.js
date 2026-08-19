/**
 * Qutlet — rejestracja klienta (edytor) bloku dynamicznego menu stopki
 * (P-23.1). Ten sam wzorzec bez build-stepu co `header-menu-blocks-editor.js`
 * — osobny plik/kategoria (slice FooterMenu, patrz nagłówek `FooterMenu\Blocks`).
 *
 * Lista nazw MUSI być zsynchronizowana ręcznie z katalogami w
 * `inc/features/FooterMenu/blocks/*` (brak automatycznego odkrywania po
 * stronie klienta) — dodanie nowego bloku wymaga dopisania go tutaj.
 */
( function ( blocks, element, blockEditor, serverSideRender ) {
	'use strict';

	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var ServerSideRender = serverSideRender;

	var BLOCK_NAMES = [
		'qutlet/footer-nav',
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
