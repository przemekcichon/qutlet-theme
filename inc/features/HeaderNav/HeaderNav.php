<?php
/**
 * Slice HeaderNav — interakcje nagłówka (dropdowny, mega menu, mobilna
 * nawigacja, ukrywanie przy scrollu). Render markupu jest w
 * parts/header.html i parts/footer.html; ta klasa TYLKO enqueue'uje JS.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\HeaderNav;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue interakcji header/footer.
 */
final class HeaderNav {

	/**
	 * Podpina enqueue pod wp_enqueue_scripts.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
	}

	/**
	 * Rejestruje i ładuje assets/js/header-nav.js.
	 *
	 * @return void
	 */
	public static function enqueue(): void {
		wp_enqueue_script(
			'qutlet-theme-header-nav',
			get_theme_file_uri( 'assets/js/header-nav.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);
	}
}
