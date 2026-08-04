/**
 * Qutlet — rejestracja klienta (edytor) bloków dynamicznych bloga (P-11.3,
 * rozszerzone P-11.5).
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
 *
 * `qutlet/article-product` (P-11.5) dostaje WŁASNY `edit` — w odróżnieniu od
 * pozostałych 15 bloków (dane z kontekstu pętli/bieżącego wpisu, bez
 * atrybutów do ręcznego ustawienia) ten blok wymaga, żeby redaktor wskazał
 * KONKRETNY produkt (`productId`) — stąd panel boczny (`InspectorControls`)
 * z polem liczbowym zamiast samego podglądu SSR.
 */
( function ( blocks, element, blockEditor, components, serverSideRender ) {
	'use strict';

	var el = element.createElement;
	var Fragment = element.Fragment;
	var useBlockProps = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
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

	blocks.registerBlockType( 'qutlet/article-product', {
		edit: function ( props ) {
			var blockProps = useBlockProps( { className: 'qutlet-block-preview' } );
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var productId = attributes.productId || 0;

			return el(
				Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: 'Produkt', initialOpen: true },
						el( TextControl, {
							label: 'ID produktu (WooCommerce)',
							type: 'number',
							value: productId || '',
							onChange: function ( value ) {
								setAttributes( { productId: value ? parseInt( value, 10 ) : 0 } );
							},
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
						} )
					)
				),
				el(
					'div',
					blockProps,
					productId > 0
						? el( ServerSideRender, {
							block: 'qutlet/article-product',
							attributes: attributes,
						} )
						: el( 'p', {}, 'Wpisz ID produktu w panelu bocznym →' )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.serverSideRender );
