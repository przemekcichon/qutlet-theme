<?php
/**
 * Blok `qutlet/tag-hero` — nagłówek archiwum tagu (`.tag-head`), port sekcji
 * z usuniętego `tag.php` (P-8.4).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! is_tag() ) {
	return;
}

$term = get_queried_object();

if ( ! ( $term instanceof WP_Term ) ) {
	return;
}
?>
<header class="tag-head">
	<span class="pill">
		<?php
		/* translators: %d: liczba wpisów. */
		printf( esc_html( _n( 'Tag · %d wpis', 'Tag · %d wpisów', (int) $term->count, 'qutlet-theme' ) ), (int) $term->count );
		?>
	</span>
	<h1><span class="accent">#</span><?php echo esc_html( $term->slug ); ?></h1>
	<?php if ( $term->description ) : ?>
		<p><?php echo esc_html( $term->description ); ?></p>
	<?php endif; ?>
</header>
