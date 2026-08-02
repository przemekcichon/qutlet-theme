<?php
/**
 * Blok `qutlet/article-header` — nagłówek artykułu (`.art-head`) + zdjęcie
 * wyróżniające (`.art-hero`/`.art-hero-caption`), port sekcji z usuniętego
 * `single.php` (P-8.4). Oba fragmenty łączy TA SAMA zależność (dane bieżącego
 * wpisu) i sąsiedztwo w oryginalnym markupie — jeden blok zamiast dzielenia
 * na kicker/tytuł/lead jako osobne rdzeniowe bloki (core/post-terms itd. nie
 * odwzorowują klas `.kicker`/`.art-byline` bez utraty stylu 1:1).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) ) {
	return;
}

$post_id     = get_the_ID();
$categories  = get_the_category( $post_id );
$primary_cat = $categories[0] ?? null;
$excerpt     = get_the_excerpt();

$published_ymd = get_the_date( 'Y-m-d' );
$modified_ymd  = get_the_modified_date( 'Y-m-d' );
$show_updated  = ( $modified_ymd !== $published_ymd );

$byline_parts   = array();
$byline_parts[] = get_the_date( 'j F Y' );
$byline_parts[] = Blog::reading_time_label( $post_id );

if ( $show_updated ) {
	/* translators: %s: miesiąc i rok aktualizacji. */
	$byline_parts[] = sprintf( __( 'aktualizacja: %s', 'qutlet-theme' ), get_the_modified_date( 'F Y' ) );
}
?>
<header class="art-head">
	<?php if ( $primary_cat instanceof WP_Term ) : ?>
		<span class="kicker"><?php echo esc_html( $primary_cat->name ); ?></span>
	<?php endif; ?>
	<h1 class="art-title"><?php the_title(); ?></h1>
	<?php if ( $excerpt ) : ?>
		<p class="art-lead"><?php echo esc_html( $excerpt ); ?></p>
	<?php endif; ?>
	<div class="art-byline">
		<span class="acct-avatar"><?php echo esc_html( Blog::author_initials( (int) get_the_author_meta( 'ID' ) ) ); ?></span>
		<div>
			<div class="art-byline-name"><?php the_author(); ?></div>
			<div class="art-byline-meta"><?php echo esc_html( implode( ' · ', $byline_parts ) ); ?></div>
		</div>
	</div>
</header>

<?php if ( has_post_thumbnail( $post_id ) ) : ?>
	<?php $thumbnail_id = get_post_thumbnail_id( $post_id ); ?>
	<figure class="art-hero" style="margin-bottom: 0;">
		<?php the_post_thumbnail( 'large' ); ?>
	</figure>
	<?php $hero_caption = $thumbnail_id ? wp_get_attachment_caption( $thumbnail_id ) : ''; ?>
	<?php if ( $hero_caption ) : ?>
		<p class="art-hero-caption"><?php echo esc_html( $hero_caption ); ?></p>
	<?php endif; ?>
<?php endif; ?>
