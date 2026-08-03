<?php
/**
 * Blok `qutlet/breadcrumbs` — dynamiczny odpowiednik `<nav class="breadcrumbs">`
 * z usuniętych home.php/single.php/category.php/tag.php (P-8.4), teraz JEDEN
 * blok reużywany we wszystkich czterech blokowych `templates/*.html` (P-11.3).
 * Auto-detekcja widoku warunkowymi tagami WP — bez atrybutów, bo blok żyje
 * wyłącznie w `templates/` (nie wstawiany ręcznie przez redaktora).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

?>
<nav class="breadcrumbs">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Qutlet', 'qutlet-theme' ); ?></a><span class="sep">/</span>
	<?php if ( is_home() ) : ?>
		<span class="current"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></span>
	<?php else : ?>
		<a href="<?php echo esc_url( Blog::blog_url() ); ?>"><?php esc_html_e( 'Drugi obieg', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<?php if ( is_singular( 'post' ) ) : ?>
			<?php
			$categories  = get_the_category();
			$primary_cat = $categories[0] ?? null;
			?>
			<?php if ( $primary_cat instanceof WP_Term ) : ?>
				<a href="<?php echo esc_url( (string) get_category_link( $primary_cat ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a><span class="sep">/</span>
			<?php endif; ?>
			<span class="current"><?php the_title(); ?></span>
		<?php elseif ( is_category() ) : ?>
			<?php $term = get_queried_object(); ?>
			<span class="current"><?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?></span>
		<?php elseif ( is_tag() ) : ?>
			<?php $term = get_queried_object(); ?>
			<?php if ( $term instanceof WP_Term ) : ?>
				<?php
				/* translators: %s: nazwa tagu. */
				$label = sprintf( __( 'Tag: %s', 'qutlet-theme' ), $term->name );
				?>
				<span class="current"><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
</nav>
