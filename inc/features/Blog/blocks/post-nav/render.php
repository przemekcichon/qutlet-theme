<?php
/**
 * Blok `qutlet/post-nav` — nawigacja prev/next w obrębie kategorii
 * (`.art-prevnext`), port sekcji z usuniętego `single.php` (P-8.4).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) ) {
	return;
}

$prev_post = get_previous_post( true, '', 'category' );
$next_post = get_next_post( true, '', 'category' );

if ( ! ( $prev_post instanceof WP_Post ) && ! ( $next_post instanceof WP_Post ) ) {
	return;
}
?>
<nav class="art-prevnext">
	<?php if ( $prev_post instanceof WP_Post ) : ?>
		<a href="<?php echo esc_url( (string) get_permalink( $prev_post ) ); ?>" class="art-pn">
			<span><?php esc_html_e( '← Poprzedni wpis', 'qutlet-theme' ); ?></span>
			<b><?php echo esc_html( get_the_title( $prev_post ) ); ?></b>
		</a>
	<?php endif; ?>
	<?php if ( $next_post instanceof WP_Post ) : ?>
		<a href="<?php echo esc_url( (string) get_permalink( $next_post ) ); ?>" class="art-pn art-pn-next">
			<span><?php esc_html_e( 'Następny wpis →', 'qutlet-theme' ); ?></span>
			<b><?php echo esc_html( get_the_title( $next_post ) ); ?></b>
		</a>
	<?php endif; ?>
</nav>
