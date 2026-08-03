<?php
/**
 * Blok `qutlet/term-hero` — nagłówek archiwum kategorii (`.term-hero`), port
 * sekcji z usuniętego `category.php` (P-8.4). Dynamiczny (nazwa/opis/licznik
 * bieżącej kategorii) — NIE statyczny marketing copy, więc zostaje kodem
 * (blok), nie patternem (D-11.G3).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! is_category() ) {
	return;
}

$term = get_queried_object();

if ( ! ( $term instanceof WP_Term ) ) {
	return;
}
?>
<header class="term-hero">
	<div>
		<span class="kicker"><?php esc_html_e( 'Kategoria', 'qutlet-theme' ); ?></span>
		<h1><?php echo esc_html( $term->name ); ?></h1>
		<?php if ( $term->description ) : ?>
			<p><?php echo esc_html( $term->description ); ?></p>
		<?php endif; ?>
	</div>
	<div class="term-hero-stat">
		<b><?php echo esc_html( (string) $term->count ); ?></b>
		<span><?php esc_html_e( 'wpisów w kategorii', 'qutlet-theme' ); ?></span>
	</div>
</header>
