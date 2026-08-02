/**
 * Qutlet — rejestracja klienta (edytor) bloków dynamicznych bloga (P-11.3).
 *
 * Bez build-stepu (brak npm/wp-scripts w tym repo, pierwszy precedens
 * własnych bloków dynamicznych — zwykły JS, `wp.element.createElement`, bez
 * JSX). Metadane (title/category/icon/attributes) każdego bloku pochodzą z
 * `block.json` — rdzeń WP bootstrapuje je do klienta automatycznie, więc tu
 * wystarczy dostarczyć WYŁĄCZNIE `edit` (podgląd na żywo przez
 * `wp.serverSideRender`, ten sam render co front) i `save` (zawsze `null` —
 * czysto dynamiczny blok, treść generuje `render.php` przy każdym renderze).
 *
 * Lista nazw MUSI być zsynchronizowana ręcznie z katalogami w
 * `inc/features/Blog/blocks/*` (brak automatycznego odkrywania po stronie
 * klienta) — dodanie nowego bloku wymaga dopisania go tutaj.
 */
( function ( blocks, element, blockEditor, serverSideRender ) {
	'use strict';

	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var ServerSideRender = serverSideRender;

	var BLOCK_NAMES = [
		'qutlet/breadcrumbs',
		'qutlet/blog-categories',
		'qutlet/featured-post',
		'qutlet/post-card',
		'qutlet/archive-pagination',
		'qutlet/term-hero',
		'qutlet/popular-tags',
		'qutlet/tag-hero',
		'qutlet/tag-related',
		'qutlet/article-header',
		'qutlet/post-tags',
		'qutlet/article-toc',
		'qutlet/author-box',
		'qutlet/related-posts',
		'qutlet/post-nav',
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
