<?php
/**
 * Slice ProductPage — render strony produktu (port design/vanilla/produkt.html).
 *
 * P-8.2a: szkielet + galeria + nagłówek. Taby zakupu/kanał Allegro (P-8.2b) i
 * sekcja treści/specyfikacji (P-8.2c) rozbudują ten slice w kolejnych punktach
 * FAZY 8. Markup mieszka w woocommerce/content-single-product.php (nadpisanie
 * szablonu WooCommerce); ta klasa trzyma enqueue JS + pomocnicze funkcje
 * odczytu/prezentacji współdzielone przez ten szablon.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\ProductPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue interakcji galerii + pomocnicze funkcje prezentacyjne.
 */
final class ProductPage {

	/**
	 * Podpina enqueue pod wp_enqueue_scripts.
	 *
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
	}

	/**
	 * Rejestruje i ładuje assets/js/product-gallery.js — wyłącznie na stronie produktu.
	 *
	 * @return void
	 */
	public static function enqueue(): void {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		wp_enqueue_script(
			'qutlet-theme-product-gallery',
			get_theme_file_uri( 'assets/js/product-gallery.js' ),
			array(),
			\Qutlet\Theme\VERSION,
			true
		);
	}

	/**
	 * Odczyt pola ACF z fallbackiem na post meta, gdyby ACF Pro nie było aktywne
	 * (motyw nie ma prawa fatalować — D-G5 dotyczy jego zależności, nie ACF
	 * bezpośrednio, bo to zależność core, nie theme).
	 *
	 * @param string $key     Literał pola (kontrakt-danych.md §2, VERBATIM).
	 * @param int    $post_id Id produktu.
	 * @return mixed
	 */
	public static function acf_field( string $key, int $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $key, $post_id );
		}

		return get_post_meta( $post_id, $key, true );
	}

	/**
	 * Etykieta klasy stanu — słownik A→„Jak nowy" itd. (kontrakt §6, `data.js` QT.COND).
	 *
	 * @param string $code Literał klasy stanu (`A`, `B`, `C`, `D`).
	 * @return string Pusty string, gdy kod nieznany.
	 */
	public static function condition_label( string $code ): string {
		$labels = array(
			'A' => __( 'Jak nowy', 'qutlet-theme' ),
			'B' => __( 'Dobry', 'qutlet-theme' ),
			'C' => __( 'Mocne ślady', 'qutlet-theme' ),
			'D' => __( 'Na części', 'qutlet-theme' ),
		);

		return $labels[ $code ] ?? '';
	}

	/**
	 * Rabat „-X%" liczony z ceny rynkowej nowego vs cena sprzedaży (kontrakt §6,
	 * `data.js` QT.savePct). NIE przechowywane — liczone przez motyw.
	 *
	 * @param float $sale_price   Cena sprzedaży (Woo `_price`).
	 * @param float $market_price Cena rynkowa nowego (ACF `cena_rynkowa_nowego`).
	 * @return int Zaokrąglony procent rabatu; 0, gdy cena rynkowa nie jest dodatnia.
	 */
	public static function save_percent( float $sale_price, float $market_price ): int {
		if ( $market_price <= 0.0 ) {
			return 0;
		}

		return (int) round( ( 1 - $sale_price / $market_price ) * 100 );
	}
}
