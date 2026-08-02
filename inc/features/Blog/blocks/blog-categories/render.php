<?php
/**
 * Blok `qutlet/blog-categories` — pasek chipów kategorii bloga (`.blog-cats`),
 * port `Blog::render_category_chips()` (P-8.4) opakowany jako blok, żeby dało
 * się go wstawić z `templates/home.html`/`templates/category.html` (P-11.3).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

$current_term = is_category() ? get_queried_object() : null;

Blog::render_category_chips( $current_term instanceof WP_Term ? $current_term : null );
