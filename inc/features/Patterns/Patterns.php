<?php
/**
 * Slice Patterns — fundament biblioteki block patterns motywu (P-11.1).
 *
 * Same wzorce (`patterns/*.php`) WP-core odkrywa automatycznie z katalogu
 * `patterns/` motywu (auto-discovery od WP 6.0, `WP_Theme::get_block_patterns()`)
 * — nie wymaga ręcznego `register_block_pattern()` (D-11.G1). Ta klasa
 * rejestruje TYLKO własną kategorię patternów („Qutlet"), do której te pliki
 * się odwołują przez nagłówek `Categories: qutlet` — bez tej rejestracji
 * WordPress pokazałby patterny jako nieskategoryzowane.
 *
 * UWAGA (środowisko dev, ground-truth 2026-08-02): `WP_Theme::get_block_patterns()`
 * cache'uje listę patternów w site-transiencie kluczowanym m.in. wersją motywu
 * (`style.css` → `Version:`) — `wp transient delete --all` NIE zawsze go
 * usuwa. Po dodaniu/zmianie plików w `patterns/` podczas developmentu, gdy
 * WordPress ich „nie widzi", podbij `Version:` w `style.css` (albo wywołaj
 * `wp_get_theme()->delete_pattern_cache()`), żeby wymusić ponowny skan.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Patterns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja kategorii patternów motywu.
 */
final class Patterns {

	/**
	 * Slug własnej kategorii patternów (używany w nagłówku `Categories:`
	 * plików `patterns/*.php`).
	 */
	public const CATEGORY = 'qutlet';

	/**
	 * Podpina rejestrację kategorii pod `init`.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'init', array( self::class, 'register_pattern_category' ) );
	}

	/**
	 * Rejestruje kategorię patternów widoczną w bibliotece wzorców edytora.
	 *
	 * @return void
	 */
	public static function register_pattern_category(): void {
		register_block_pattern_category(
			self::CATEGORY,
			array(
				'label' => __( 'Qutlet', 'qutlet-theme' ),
			)
		);
	}
}
