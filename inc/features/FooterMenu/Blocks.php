<?php
/**
 * Slice FooterMenu — rejestracja bloku dynamicznego menu stopki (P-23.1).
 * Ten sam wzorzec bez build-stepu co `HeaderMenu\Blocks`: `block.json` +
 * `render.php` per blok, własny JS edytora
 * (`assets/js/footer-menu-blocks-editor.js`, bez JSX) rejestrujący `edit`
 * przez `wp.serverSideRender`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\FooterMenu;

use Qutlet\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja bloków dynamicznych menu stopki.
 */
final class Blocks {

	/**
	 * Handle wspólnego skryptu edytora (bez build-stepu) — patrz nagłówek klasy.
	 */
	private const EDITOR_SCRIPT_HANDLE = 'qutlet-footer-menu-blocks-editor';

	/**
	 * Slug własnej kategorii bloków w inserterze.
	 */
	private const BLOCK_CATEGORY = 'qutlet-footer-menu';

	/**
	 * Podpina rejestrację.
	 *
	 * @return void
	 */
	public static function boot(): void {
		// Priorytet 5: skrypt edytora MUSI być zarejestrowany (wp_register_script)
		// zanim register_block_type() (priorytet 10) przeczyta block.json i
		// powiąże `editorScript` z tym handle'm.
		add_action( 'init', array( self::class, 'register_editor_script' ), 5 );
		add_action( 'init', array( self::class, 'register_blocks' ), 10 );
		add_filter( 'block_categories_all', array( self::class, 'register_block_category' ) );
	}

	/**
	 * Rejestruje (bez enqueue) skrypt edytora bloku menu stopki. WP core
	 * enqueue'uje `editorScript` bloków automatycznie w kontekście edytora
	 * (`wp_enqueue_registered_block_scripts_and_styles()`) — nie potrzeba
	 * własnego hooka enqueue.
	 *
	 * @return void
	 */
	public static function register_editor_script(): void {
		wp_register_script(
			self::EDITOR_SCRIPT_HANDLE,
			get_theme_file_uri( 'assets/js/footer-menu-blocks-editor.js' ),
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n' ),
			Theme\VERSION,
			true
		);
	}

	/**
	 * Rejestruje każdy blok z `blocks/*\/block.json` (jeden katalog = jeden blok).
	 *
	 * @return void
	 */
	public static function register_blocks(): void {
		$blocks_dir = __DIR__ . '/blocks';
		$entries    = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

		if ( ! is_array( $entries ) ) {
			return;
		}

		foreach ( $entries as $dir ) {
			if ( is_readable( $dir . '/block.json' ) ) {
				register_block_type( $dir );
			}
		}
	}

	/**
	 * Dokłada własną kategorię „Qutlet — menu stopki" w inserterze bloków.
	 *
	 * @param array<int, array{slug: string, title: string, icon?: string|null}> $categories Kategorie zarejestrowane dotąd.
	 * @return array<int, array{slug: string, title: string, icon?: string|null}>
	 */
	public static function register_block_category( array $categories ): array {
		$categories[] = array(
			'slug'  => self::BLOCK_CATEGORY,
			'title' => __( 'Qutlet — menu stopki', 'qutlet-theme' ),
		);

		return $categories;
	}
}
