<?php
/**
 * Slice EditorPreferences — domyślne wyłączenie podglądu szablonu
 * (nagłówek/stopka) w edytorze Stron/Wpisów (P-25.3b, D-25.3.1,
 * `docs/plan.md` w `qutlet-meta`). Ustawia WYŁĄCZNIE wartość domyślną
 * preferencji `core.renderingModes.qutlet-theme.{page,post}` przez oficjalne
 * API `@wordpress/preferences` (`setDefaults` — nie narusza już zapisanych
 * wyborów użytkownika, patrz ground-truth (b) przy D-25.3.1 w planie).
 * Front-end i Site Editor (edycja szablonów/parts wprost, `wp_template`/
 * `wp_template_part`) bez wpływu — mechanizm czysto edytorski, zaczepiony
 * WYŁĄCZNIE na `enqueue_block_editor_assets`.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\EditorPreferences;

use Qutlet\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja domyślnej preferencji edytora bloków.
 */
final class EditorPreferences {

	/**
	 * Handle skryptu enqueue'owanego w edytorze bloków.
	 */
	private const SCRIPT_HANDLE = 'qutlet-editor-preferences';

	/**
	 * Rejestruje boot slice'a.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'enqueue_block_editor_assets', array( self::class, 'enqueue_script' ) );
	}

	/**
	 * Enqueue'uje skrypt ustawiający domyślny rendering mode `post-only` dla
	 * `page`/`post` w motywie `qutlet-theme` (D-25.3.1).
	 *
	 * @return void
	 */
	public static function enqueue_script(): void {
		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			get_theme_file_uri( 'assets/js/editor-preferences.js' ),
			array( 'wp-data', 'wp-preferences' ),
			Theme\VERSION,
			true
		);
	}
}
