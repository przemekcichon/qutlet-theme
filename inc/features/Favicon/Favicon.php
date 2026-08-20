<?php
/**
 * Slice Favicon — favicona motywu (P-23.5), port design/vanilla/assets/favicon.svg
 * (`<link rel="icon" type="image/svg+xml" href="assets/favicon.svg">` na każdej
 * stronie prototypu). Natywny WP „Site Icon" wymaga obrazu rastrowego (upload w
 * adminie nie akceptuje SVG) — niezgodne z plikiem źródłowym, więc favicona jest
 * hardkodowana w `wp_head` (decyzja użytkownika, sesja 2026-08-20, D-23.5.1):
 * zachowuje SVG 1:1 z prototypem, kosztem edytowalności przez admina bez zmiany
 * kodu — uznane za akceptowalne, bo favicona to element brandingu/kodu, nie
 * treść redakcyjna.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Favicon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render tagu favicony.
 */
final class Favicon {

	/**
	 * Rejestruje boot slice'a.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'wp_head', array( self::class, 'render_favicon_link' ), 1 );
	}

	/**
	 * Drukuje `<link rel="icon">` na SVG motywu.
	 *
	 * @return void
	 */
	public static function render_favicon_link(): void {
		printf(
			'<link rel="icon" type="image/svg+xml" href="%s">' . "\n",
			esc_url( get_theme_file_uri( 'assets/favicon.svg' ) )
		);
	}
}
