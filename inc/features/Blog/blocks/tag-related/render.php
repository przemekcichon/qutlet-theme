<?php
/**
 * Blok `qutlet/tag-related` — sekcja „Powiązane:" na archiwum tagu (tagi
 * współwystępujące + kategoria), port sekcji z usuniętego `tag.php` (P-8.4),
 * `Blog::related_tags()`/`Blog::primary_category_for_tag()` bez zmian.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

if ( ! is_tag() ) {
	return;
}

$term = get_queried_object();

if ( ! ( $term instanceof WP_Term ) ) {
	return;
}

$related_tags     = Blog::related_tags( $term );
$related_category = Blog::primary_category_for_tag( $term );

if ( empty( $related_tags ) && ! ( $related_category instanceof WP_Term ) ) {
	return;
}
?>
<div class="term-related">
	<span><?php esc_html_e( 'Powiązane:', 'qutlet-theme' ); ?></span>
	<?php foreach ( $related_tags as $tag ) : ?>
		<a href="<?php echo esc_url( (string) get_tag_link( $tag ) ); ?>" class="art-tag">#<?php echo esc_html( $tag->slug ); ?></a>
	<?php endforeach; ?>
	<?php if ( $related_category instanceof WP_Term ) : ?>
		<a href="<?php echo esc_url( (string) get_category_link( $related_category ) ); ?>" class="art-tag">
			<?php
			/* translators: %s: nazwa kategorii. */
			printf( esc_html__( 'Kategoria: %s', 'qutlet-theme' ), esc_html( $related_category->name ) );
			?>
		</a>
	<?php endif; ?>
</div>
