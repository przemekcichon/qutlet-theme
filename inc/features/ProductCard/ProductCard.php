<?php
/**
 * Slice ProductCard — karta produktu w pętli (port design/vanilla/js/templates.js
 * QT.tpl.productCard()).
 *
 * P-8.3a: etykieta liczby sztuk (`pcard-stock`, `templates.js:51` — przeniesiona
 * z P-8.2a, ground-truth `produkt.html` potwierdził, że QT.qtyLabel renderuje się
 * WYŁĄCZNIE na karcie, nie na stronie produktu). Reszta pomocniczych odczytów
 * (klasa stanu, cena, rabat) reużywa istniejących metod
 * `Qutlet\Theme\features\ProductPage\ProductPage` — markup karty mieszka w
 * woocommerce/content-product.php (nadpisanie szablonu WooCommerce).
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

namespace Qutlet\Theme\features\ProductCard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pomocnicze funkcje prezentacyjne karty produktu.
 */
final class ProductCard {

	/**
	 * Etykieta liczby sztuk (kontrakt §1/§6 — WARTOŚĆ LICZONA, nie przechowywana):
	 * `$product->get_stock_quantity()` (`_stock`); brak/null lub < 1 traktowane
	 * jak pojedynczy egzemplarz (natura sklepu — `data.js` QT.qtyLabel).
	 *
	 * @param \WC_Product $product Produkt.
	 * @return string
	 */
	public static function qty_label( \WC_Product $product ): string {
		$qty = $product->get_stock_quantity();

		if ( null === $qty || $qty < 1 ) {
			$qty = 1;
		}

		if ( 1 === $qty ) {
			return __( 'Pojedyncza sztuka', 'qutlet-theme' );
		}

		return sprintf(
			/* translators: 1: quantity, 2: Polish word for "piece(s)" matching the quantity (sztuka/sztuki/sztuk). */
			__( '%1$d %2$s', 'qutlet-theme' ),
			$qty,
			self::plural_word(
				$qty,
				__( 'sztuka', 'qutlet-theme' ),
				__( 'sztuki', 'qutlet-theme' ),
				__( 'sztuk', 'qutlet-theme' )
			)
		);
	}

	/**
	 * Polska odmiana liczebnikowa (port `data.js` QT.plural).
	 *
	 * @param int    $n    Liczba.
	 * @param string $one  Forma dla 1 (nieużywana tu — qty_label() obsługuje 1 osobno,
	 *                     zostawiona dla kompletności portu).
	 * @param string $few  Forma dla 2-4 (poza 12-14).
	 * @param string $many Forma dla pozostałych.
	 * @return string
	 */
	private static function plural_word( int $n, string $one, string $few, string $many ): string {
		if ( 1 === $n ) {
			return $one;
		}

		$mod10  = $n % 10;
		$mod100 = $n % 100;

		if ( $mod10 >= 2 && $mod10 <= 4 && ! ( $mod100 >= 12 && $mod100 <= 14 ) ) {
			return $few;
		}

		return $many;
	}
}
