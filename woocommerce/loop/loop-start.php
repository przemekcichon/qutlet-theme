<?php
/**
 * Qutlet — początek pętli produktów (siatka kart, P-8.3a).
 *
 * Nadpisuje domyślny szablon WooCommerce (woocommerce/templates/loop/loop-start.php,
 * `<ul class="products columns-N">`) — theme owns the customer-facing render
 * (D-8.G1). W przeciwieństwie do woocommerce/archive-product.php (nagłówek
 * archiwum HARDKODOWANY przez WooCommerce Blocks w
 * ClassicTemplate::render_archive_product() — theme nie ma tam punktu
 * nadpisania), ten partial JEST respektowany: `woocommerce_product_loop_start()`
 * woła `wc_get_template( 'loop/loop-start.php' )`, więc theme swobodnie
 * zastępuje `<ul>` uniwersalną siatką `.grid-3` (design/vanilla — `.grid-3`,
 * `strefa-okazji.html`), zamiast wymuszać ją na `<ul class="products">` przez CSS.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;
?>
<div class="grid-3">
