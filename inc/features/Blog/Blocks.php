<?php
/**
 * Slice Blog — rejestracja własnych bloków dynamicznych (P-11.3, rewizja
 * D-8.4.1). Loop-driven fragmenty bloga (karta wpisu, wyróżniony wpis, TOC
 * artykułu, powiązane wpisy, nawigacja prev/next, chipy kategorii, paginacja,
 * nagłówki archiwów) zostają CZYSTYM renderem PHP (`Blog::*`/`ArticleHeadings`
 * bez zmian, D-8.G1) — opakowanym w `block.json` + `render.php`, żeby dało
 * się je wstawiać z poziomu blokowych `templates/*.html`
 * (`templates/home.html`, `single.html`, `category.html`, `tag.html`).
 *
 * Bez build-stepu (brak npm/wp-scripts w tym repo — pierwszy precedens
 * własnych dynamicznych bloków, P-11.1/P-11.2 dotyczyły wyłącznie patternów
 * statycznych): edytor dostaje jeden wspólny, zwykły JS
 * (`assets/js/blog-blocks-editor.js`, bez JSX) rejestrujący `edit` przez
 * `wp.serverSideRender` — reszta metadanych (title/category/attributes)
 * pochodzi z `block.json`, bootstrapowanego do klienta automatycznie przez
 * rdzeń WP.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Blog;

use Qutlet\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja bloków dynamicznych bloga.
 */
final class Blocks {

	/**
	 * Handle wspólnego skryptu edytora (bez build-stepu) — patrz nagłówek klasy.
	 */
	private const EDITOR_SCRIPT_HANDLE = 'qutlet-blog-blocks-editor';

	/**
	 * Slug własnej kategorii bloków w inserterze (odrębny od kategorii
	 * patternów `Patterns::CATEGORY` — inny rejestr WP: `block_categories_all`,
	 * nie `register_block_pattern_category()`).
	 */
	private const BLOCK_CATEGORY = 'qutlet-blog';

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
	 * Rejestruje (bez enqueue) wspólny skrypt edytora bloków bloga. WP core
	 * enqueue'uje `editorScript` bloków automatycznie w kontekście edytora
	 * (`wp_enqueue_registered_block_scripts_and_styles()`, domyślny filtr na
	 * `enqueue_block_editor_assets`) — nie potrzeba własnego hooka enqueue.
	 *
	 * @return void
	 */
	public static function register_editor_script(): void {
		wp_register_script(
			self::EDITOR_SCRIPT_HANDLE,
			get_theme_file_uri( 'assets/js/blog-blocks-editor.js' ),
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
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
	 * Dokłada własną kategorię „Qutlet — blog" w inserterze bloków.
	 *
	 * @param array<int, array{slug: string, title: string, icon?: string|null}> $categories Kategorie zarejestrowane dotąd.
	 * @return array<int, array{slug: string, title: string, icon?: string|null}>
	 */
	public static function register_block_category( array $categories ): array {
		$categories[] = array(
			'slug'  => self::BLOCK_CATEGORY,
			'title' => __( 'Qutlet — blog', 'qutlet-theme' ),
		);

		return $categories;
	}
}
