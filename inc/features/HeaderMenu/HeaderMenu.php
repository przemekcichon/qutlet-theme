<?php
/**
 * Slice HeaderMenu — lokalizacja menu `nawigacja` (P-16.1, `.header-nav`,
 * proste 4 linki) i docelowo (P-16.2b) lokalizacja `kategorie`. Rejestruje
 * WYŁĄCZNIE lokalizacje menu i bloki renderujące — zero pól/CPT (D-16.G1,
 * granica core/theme, CLAUDE.md „Struktura multi-root"). Wzorem
 * `Help::MENU_LOCATION` (P-1.5, `inc/features/Help/Help.php`) — jedyna
 * różnica: to menu renderuje się w nagłówku KAŻDEJ strony (blok dynamiczny
 * `qutlet/header-nav` w `parts/header.html`, D-16.G4), nie w bocznym
 * sidebarze treści.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\HeaderMenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja lokalizacji menu nagłówka.
 */
final class HeaderMenu {

	/**
	 * Slug lokalizacji menu nawigacyjnego (`.header-nav`) — literał
	 * `docs/kontrakt-danych.md` §14.1, odczytywany przez blok
	 * `qutlet/header-nav` (Blocks.php/blocks/header-nav/render.php) i seed WP-CLI.
	 */
	public const MENU_LOCATION = 'nawigacja';

	/**
	 * Rejestruje boot slice'a.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'after_setup_theme', array( self::class, 'register_menu_location' ) );
	}

	/**
	 * Rejestruje lokalizację menu `nawigacja` (4 proste linki `.header-nav`).
	 *
	 * @return void
	 */
	public static function register_menu_location(): void {
		register_nav_menu( self::MENU_LOCATION, __( 'Nawigacja (header, .header-nav)', 'qutlet-theme' ) );
	}
}
