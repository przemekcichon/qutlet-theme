<?php
/**
 * Slice HeaderMenu — lokalizacje menu `nawigacja` (P-16.1, `.header-nav`,
 * proste 4 linki) i `kategorie` (P-16.2b, `.subnav-band`/`.mega`/sekcja
 * „Kategorie" w `.mnav-panel`). Rejestruje WYŁĄCZNIE lokalizacje menu i bloki
 * renderujące — zero pól/CPT (D-16.G1, granica core/theme, CLAUDE.md
 * „Struktura multi-root"). Wzorem `Help::MENU_LOCATION` (P-1.5,
 * `inc/features/Help/Help.php`) — jedyna różnica: te menu renderują się w
 * nagłówku KAŻDEJ strony (bloki dynamiczne w `parts/header.html`, D-16.G4),
 * nie w bocznym sidebarze treści.
 *
 * Literał `kategorie` (self::MENU_LOCATION_KATEGORIE) MUSI być identyczny z
 * `MenuItemFields::LOCATION_KATEGORIE` w `qutlet-core` (P-16.2a) — „Literał-most
 * między repo", `docs/kontrakt-danych.md` §14.
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
	 * Slug lokalizacji menu kategorii (`.subnav-band`/`.mega`/mobile) —
	 * literał `docs/kontrakt-danych.md` §14.1, odczytywany przez
	 * {@see CategoryMenu} i seed WP-CLI. MUSI być identyczny z
	 * `MenuItemFields::LOCATION_KATEGORIE` (`qutlet-core`, P-16.2a) —
	 * „Literał-most między repo" w docblocku klasy.
	 */
	public const MENU_LOCATION_KATEGORIE = 'kategorie';

	/**
	 * Rejestruje boot slice'a.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'after_setup_theme', array( self::class, 'register_menu_location' ) );
	}

	/**
	 * Rejestruje lokalizacje menu `nawigacja` (4 proste linki `.header-nav`)
	 * i `kategorie` (pozycje `product_cat`, pola ACF z `qutlet-core`).
	 *
	 * @return void
	 */
	public static function register_menu_location(): void {
		register_nav_menu( self::MENU_LOCATION, __( 'Nawigacja (header, .header-nav)', 'qutlet-theme' ) );
		register_nav_menu( self::MENU_LOCATION_KATEGORIE, __( 'Kategorie (header, .subnav-band + .mega)', 'qutlet-theme' ) );
	}
}
