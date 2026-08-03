<?php
/**
 * Blok `qutlet/author-box` — notka o autorze (`.author-box`), port sekcji
 * z usuniętego `single.php` (P-8.4).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) ) {
	return;
}

$bio = get_the_author_meta( 'description' );
?>
<div class="author-box">
	<span class="acct-avatar"><?php echo esc_html( Blog::author_initials( (int) get_the_author_meta( 'ID' ) ) ); ?></span>
	<div>
		<h5><?php the_author(); ?></h5>
		<?php if ( $bio ) : ?>
			<p><?php echo esc_html( $bio ); ?></p>
		<?php endif; ?>
	</div>
</div>
