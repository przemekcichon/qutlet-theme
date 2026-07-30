<?php
/**
 * Qutlet Theme — klasyczny footer.php (P-8.4).
 *
 * Odpowiednik `header.php` — patrz komentarz tam. Renderuje part `footer`
 * (`parts/footer.html`) przez `block_footer_area()` (WP core) i domyka
 * dokument (`wp_footer()` + zamknięcie `<body>`/`<html>`).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

block_footer_area();
wp_footer();
?>
</body>
</html>
