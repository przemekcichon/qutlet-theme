<?php
/**
 * Qutlet — paginacja archiwum produktów (P-9.4), port `.pager`/`.page-btn` z
 * design/vanilla/css/style.css + js/app.js (`renderPager` — chevron prev/next
 * zamiast tekstowych strzałek, zaokrąglone „pigułki" zamiast domyślnej listy
 * `.page-numbers`).
 *
 * Nadpisuje domyślny szablon WooCommerce (woocommerce/templates/loop/pagination.php)
 * — reużywa NATYWNY `paginate_links()` (numeracja/ellipsis/URL-e z query args,
 * ten sam mechanizm GET+WP_Query co P-8.3b, D-8.3b.1); theme WYŁĄCZNIE podmienia
 * markup/klasy na `.pager`/`.page-btn`/`.pager-dots` (D-8.G1 — render, nie logika).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( '999999999', '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}

$current    = max( 1, (int) $current );
$chev_left  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>';
$chev_right = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>';

$links = paginate_links(
	apply_filters(
		'woocommerce_pagination_args',
		array( // WPCS: XSS ok.
			'base'      => $base,
			'format'    => $format,
			'add_args'  => false,
			'current'   => $current,
			'total'     => $total,
			'prev_text' => $chev_left,
			'next_text' => $chev_right,
			'type'      => 'array',
			'end_size'  => 3,
			'mid_size'  => 3,
		)
	)
);

if ( ! $links ) {
	return;
}

// Podmiana WYŁĄCZNIE klas na wzór prototypu — `paginate_links()` (WP core)
// nadal buduje href/numerację/ellipsis, theme nie dotyka tej logiki (D-8.G1).
$class_map = array(
	'class="page-numbers current"' => 'class="page-btn active"',
	'class="page-numbers dots"'    => 'class="pager-dots"',
	'class="prev page-numbers"'    => 'class="page-btn"',
	'class="next page-numbers"'    => 'class="page-btn"',
	'class="page-numbers"'         => 'class="page-btn"',
);
?>
<nav class="pager" aria-label="<?php esc_attr_e( 'Paginacja produktów', 'qutlet-theme' ); ?>">
	<?php if ( 1 === $current ) : ?>
		<span class="page-btn" aria-disabled="true"><?php echo $chev_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literał SVG zdefiniowany wyżej w tym pliku, nie dane. ?></span>
	<?php endif; ?>
	<?php foreach ( $links as $link ) : ?>
		<?php echo str_replace( array_keys( $class_map ), array_values( $class_map ), $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output paginate_links() (WP core), ten sam wzorzec co oryginalny szablon Woo ("WPCS: XSS ok." wyżej), tu tylko podmiana klas. ?>
	<?php endforeach; ?>
	<?php if ( $current === (int) $total ) : ?>
		<span class="page-btn" aria-disabled="true"><?php echo $chev_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literał SVG zdefiniowany wyżej w tym pliku, nie dane. ?></span>
	<?php endif; ?>
</nav>
