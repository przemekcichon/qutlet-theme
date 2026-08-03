/**
 * Qutlet — rejestracja klienta (edytor) bloków dynamicznych strony głównej
 * (P-11.4). Ten sam wzorzec bez build-stepu co `blog-blocks-editor.js`
 * (P-11.3, zwykły JS bez JSX) — osobny plik/kategoria od bloga (Home to
 * odrębny slice, patrz nagłówek `Home\Blocks`).
 *
 * Lista nazw MUSI być zsynchronizowana ręcznie z katalogami w
 * `inc/features/Home/blocks/*` (brak automatycznego odkrywania po stronie
 * klienta) — dodanie nowego bloku wymaga dopisania go tutaj.
 */
( function ( blocks, element, blockEditor, serverSideRender ) {
	'use strict';

	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var ServerSideRender = serverSideRender;

	var BLOCK_NAMES = [
		'qutlet/featured-products',
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
