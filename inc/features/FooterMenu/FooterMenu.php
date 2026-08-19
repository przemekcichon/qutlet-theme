<?php
/**
 * Slice FooterMenu — 3 lokalizacje menu stopki (P-23.1): `stopka-sklep`,
 * `stopka-informacje`, `stopka-pomoc` (`.footer-col` w `parts/footer.html`).
 * Rejestruje WYŁĄCZNIE lokalizacje menu i blok renderujący — zero pól/CPT
 * (D-8.G1, granica core/theme, CLAUDE.md „Struktura multi-root"), wzorem
 * `HeaderMenu` (P-16.1) i `Help::MENU_LOCATION` (P-1.5,
 * `inc/features/Help/Help.php`).
 *
 * Kolumna „Sklep" dziś nie ma z czego migrować (linki-placeholdery `href="#"`,
 * poza zakresem P-8.5/P-8.3) — lokalizacja i pusty seed menu istnieją od
 * razu, treść to decyzja admina PO merge'u tego punktu.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\FooterMenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja lokalizacji menu stopki.
 */
final class FooterMenu {

	/**
	 * Slug lokalizacji menu kolumny „Sklep" (`.footer-col` #1) — literał
	 * `docs/kontrakt-danych.md` §14.4, odczytywany przez blok
	 * `qutlet/footer-nav` i seed WP-CLI.
	 */
	public const MENU_LOCATION_SKLEP = 'stopka-sklep';

	/**
	 * Slug lokalizacji menu kolumny „Informacje" (`.footer-col` #2) — literał
	 * `docs/kontrakt-danych.md` §14.4.
	 */
	public const MENU_LOCATION_INFORMACJE = 'stopka-informacje';

	/**
	 * Slug lokalizacji menu kolumny „Pomoc" (`.footer-col` #3) — literał
	 * `docs/kontrakt-danych.md` §14.4.
	 */
	public const MENU_LOCATION_POMOC = 'stopka-pomoc';

	/**
	 * Rejestruje boot slice'a.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'after_setup_theme', array( self::class, 'register_menu_location' ) );
	}

	/**
	 * Rejestruje 3 lokalizacje menu stopki.
	 *
	 * @return void
	 */
	public static function register_menu_location(): void {
		register_nav_menu( self::MENU_LOCATION_SKLEP, __( 'Stopka — Sklep (footer, .footer-col)', 'qutlet-theme' ) );
		register_nav_menu( self::MENU_LOCATION_INFORMACJE, __( 'Stopka — Informacje (footer, .footer-col)', 'qutlet-theme' ) );
		register_nav_menu( self::MENU_LOCATION_POMOC, __( 'Stopka — Pomoc (footer, .footer-col)', 'qutlet-theme' ) );
	}
}
