<?php
/**
 * Blok `qutlet/article-toc` — spis treści artykułu (`.art-toc`), port sekcji
 * z usuniętego `single.php` (P-8.4). Czyta `ArticleHeadings::get_captured()`
 * (bez zmian) — WYMAGA, żeby `<!-- wp:post-content /-->` wyrenderował się
 * WCZEŚNIEJ w tym samym request (kolejność źródłowa w `templates/single.html`:
 * `.art-body` z treścią przed `.art-side` z tym blokiem), bo filtr
 * `the_content` zbierający nagłówki (`ArticleHeadings::capture_and_anchor()`,
 * podpięty globalnie w `Blog::boot()`) odpala się dopiero przy renderze treści.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\ArticleHeadings;

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) ) {
	return;
}

$headings = ArticleHeadings::get_captured();

if ( empty( $headings ) ) {
	return;
}
?>
<div class="art-side-card">
	<h5><?php esc_html_e( 'W tym artykule', 'qutlet-theme' ); ?></h5>
	<nav class="art-toc">
		<?php foreach ( $headings as $heading ) : ?>
			<a href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['text'] ); ?></a>
		<?php endforeach; ?>
	</nav>
</div>
