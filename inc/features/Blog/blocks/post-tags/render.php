<?php
/**
 * Blok `qutlet/post-tags` — lista tagów wpisu (`.art-tags`), port sekcji
 * z usuniętego `single.php` (P-8.4).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) ) {
	return;
}

$tags = get_the_tags();

if ( ! is_array( $tags ) || empty( $tags ) ) {
	return;
}
?>
<div class="art-tags">
	<?php foreach ( $tags as $tag ) : ?>
		<a href="<?php echo esc_url( (string) get_tag_link( $tag ) ); ?>" class="art-tag">#<?php echo esc_html( $tag->slug ); ?></a>
	<?php endforeach; ?>
</div>
