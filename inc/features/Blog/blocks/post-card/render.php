<?php
/**
 * Blok `qutlet/post-card` — karta wpisu (`.post-card`/`.post-hcard`), cienka
 * warstwa nad ISTNIEJĄCYM `template-parts/blog/post-card.php` (P-8.4) — bez
 * duplikacji markupu, blok tylko dostarcza `$post_id` z kontekstu pętli
 * (`postId` z `core/post-template`) i atrybut wariantu.
 *
 * @package Qutlet\Theme
 *
 * @var array{variant?: string} $attributes
 * @var WP_Block                $block
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$post_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();

if ( $post_id <= 0 ) {
	return;
}

$variant = ( 'horizontal' === ( $attributes['variant'] ?? 'grid' ) ) ? 'horizontal' : 'grid';

get_template_part(
	'template-parts/blog/post-card',
	null,
	array(
		'post'    => get_post( $post_id ),
		'variant' => $variant,
	)
);
