<?php
/**
 * Blok `qutlet/archive-pagination` — cienka warstwa nad
 * `Blog::render_pagination()` (bez zmian, P-8.4), żeby dało się ją wstawić
 * jako sibling `<!-- wp:query -->` w blokowych `templates/*.html` (P-11.3).
 * Nie renderuje niczego, gdy wynik mieści się na jednej stronie — bez
 * potrzeby warunku w szablonie.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

Blog::render_pagination();
