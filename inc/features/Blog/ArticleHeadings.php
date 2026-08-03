<?php
/**
 * Slice Blog — nagłówki artykułu + spis treści (P-8.4), port `.art-toc`
 * (`blog-artykul.html:164-173`). WordPress nie generuje automatycznie `id`
 * dla nagłówków `<h2>` w treści wpisu (blok Nagłówek ma opcjonalną „kotwicę
 * HTML", ale redaktor musi ją ustawić ręcznie per nagłówek) — ta klasa
 * dogenerowuje brakujące `id` przy renderze (`the_content`) i jednocześnie
 * zbiera listę nagłówków do spisu treści w sidebarze, żeby `id` w obu
 * miejscach ZAWSZE się zgadzały (jedno przejście, jedno źródło prawdy).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\Blog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dogenerowuje kotwice `<h2 id="…">` w treści wpisu i zbiera spis treści.
 */
final class ArticleHeadings {

	/**
	 * Nagłówki złapane podczas ostatniego wywołania `capture_and_anchor()`.
	 *
	 * @var array<int, array{id: string, text: string}>
	 */
	private static array $captured = array();

	/**
	 * Slugi już użyte w BIEŻĄCYM wywołaniu — dedupe (dwa nagłówki o tej samej
	 * treści nie mogą dostać tego samego `id`).
	 *
	 * @var array<int, string>
	 */
	private static array $used_slugs = array();

	/**
	 * Filtr `the_content`: dogenerowuje `id` dla `<h2>` bez kotwicy i zapisuje
	 * listę nagłówków do `get_captured()`. Podpinany GLOBALNIE (P-11.3,
	 * `Blog::boot()`), bo blokowy `templates/single.html` renderuje treść
	 * przez rdzeniowy `<!-- wp:post-content /-->`, a nie przez wywołanie
	 * `the_content()` który dałoby się owinąć punktowo jak w usuniętym
	 * `single.php` — dlatego strażnik `is_singular( 'post' )` PONIŻEJ jest
	 * konieczny: `the_content` odpala się też np. dla opisu produktu Woo
	 * (`woocommerce_template_single_content()`), którego ten filtr NIE może
	 * dotknąć.
	 *
	 * @param string $content Przefiltrowana treść (HTML, po `do_blocks`/`wpautop`).
	 * @return string Treść z dogenerowanymi `id` (reszta markupu bez zmian).
	 */
	public static function capture_and_anchor( string $content ): string {
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}

		self::$captured   = array();
		self::$used_slugs = array();

		$result = preg_replace_callback(
			'/<h2(\s[^>]*)?>(.*?)<\/h2>/is',
			array( self::class, 'anchor_single_heading' ),
			$content
		);

		return is_string( $result ) ? $result : $content;
	}

	/**
	 * Callback `preg_replace_callback` dla pojedynczego dopasowania `<h2>`.
	 *
	 * @param array<int, string> $matches Dopasowanie regexu (0: całość, 1: atrybuty, 2: wnętrze).
	 * @return string
	 */
	private static function anchor_single_heading( array $matches ): string {
		$attrs = $matches[1] ?? '';
		$inner = $matches[2] ?? '';
		$text  = trim( wp_strip_all_tags( $inner ) );

		if ( '' === $text ) {
			return $matches[0];
		}

		if ( preg_match( '/\bid=(["\'])(.*?)\1/i', $attrs, $id_match ) ) {
			// Kotwica ustawiona ręcznie w edytorze (blok Nagłówek, „HTML anchor") —
			// zachowujemy ją, ale MUSI wejść do puli zajętych slugów, inaczej kolejny
			// auto-generowany nagłówek o tej samej treści (ten sam `sanitize_title()`)
			// mógłby dostać identyczne `id` — dwie kotwice, jeden cel linku.
			$id                  = $id_match[2];
			self::$used_slugs[] = $id;
		} else {
			$id     = self::unique_slug( sanitize_title( $text ) );
			$attrs .= ' id="' . esc_attr( $id ) . '"';
		}

		self::$captured[] = array(
			'id'   => $id,
			'text' => $text,
		);

		return '<h2' . $attrs . '>' . $inner . '</h2>';
	}

	/**
	 * Nagłówki złapane przy ostatnim `capture_and_anchor()` — do spisu treści.
	 *
	 * @return array<int, array{id: string, text: string}>
	 */
	public static function get_captured(): array {
		return self::$captured;
	}

	/**
	 * Deduplikuje slug w obrębie bieżącego artykułu (przyrostek `-2`, `-3`, …).
	 *
	 * @param string $slug Slug bazowy (`sanitize_title()`).
	 * @return string
	 */
	private static function unique_slug( string $slug ): string {
		if ( '' === $slug ) {
			$slug = 'sekcja';
		}

		$base  = $slug;
		$index = 2;

		while ( in_array( $slug, self::$used_slugs, true ) ) {
			$slug = $base . '-' . $index;
			++$index;
		}

		self::$used_slugs[] = $slug;

		return $slug;
	}
}
