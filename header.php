<?php
/**
 * Qutlet Theme — klasyczny header.php (P-8.4).
 *
 * Motyw jest z założenia blokowy (theme.json + templates/*.html), ale blog
 * (home.php/single.php/category.php/tag.php) renderuje się przez klasyczną
 * hierarchię szablonów — fallback udokumentowany i zweryfikowany w tej
 * instalacji WP 7.0.2 (`wp-includes/block-template.php` `locate_block_template()`:
 * gdy dla danego typu NIE istnieje żaden plik w `templates/`, WP używa
 * klasycznego pliku PHP zamiast `template-canvas.php`) — patrz nagłówek
 * `inc/features/Blog/Blog.php` po pełne uzasadnienie.
 *
 * Ten plik dostarcza brakującą „ramę" dokumentu (doctype/head/body-open),
 * której blokowe szablony NIE potrzebują (buduje ją `template-canvas.php`),
 * i renderuje part `header` (`parts/header.html`) przez `block_header_area()`
 * (WP core, `wp-includes/block-template-utils.php`) — ta sama treść co
 * `<!-- wp:template-part {"slug":"header"} /-->` w szablonach blokowych.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php block_header_area(); ?>
