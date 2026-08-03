<?php
/**
 * Title: Kafle kategorii — strona główna
 * Slug: qutlet-theme/home-categories
 * Categories: qutlet
 * Description: Siatka 8 kafli kategorii produktowych. Port .cat-grid (design/vanilla/index.html) — prototyp pokazywał 4 przykładowe sluga (smartfony/laptopy/audio/gaming) + 4 nieistniejące (tablety/smartwatche/foto/konsole); tu top 8 realnych termów `product_cat` wg liczby produktów (decyzja użytkownika, sesja P-11.4, kontrakt danych §1.1). Linki liczone przez get_term_link() — sluga wpisane na sztywno, jak inne odnośniki między-stronowe w tej bibliotece patternów (np. how-cta). Zdjęcie kafla (opcjonalne, poza zakresem prototypu — zgłoszenie użytkownika po P-11.4): natywne pole Woo „Miniaturka" na kategorii produktu (term meta `thumbnail_id`, ustawiane w Produkty → Kategorie w wp-adminie, patrz WC_Admin_Taxonomies) — motyw TYLKO czyta, zero rejestracji w core. Kafel bez ustawionej miniaturki wraca do oryginalnego wyglądu z prototypu (limonkowe tło + tekst).
 * Keywords: kategorie, strona główna
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$categories = array(
	'telefony-akcesoria'     => __( 'Akcesoria do telefonów', 'qutlet-theme' ),
	'peryferia'              => __( 'Peryferia', 'qutlet-theme' ),
	'komputery-i-podzespoly' => __( 'Podzespoły komputerowe', 'qutlet-theme' ),
	'kable-i-adaptery'       => __( 'Kable i adaptery', 'qutlet-theme' ),
	'audio'                  => __( 'Audio', 'qutlet-theme' ),
	'monitory'               => __( 'Monitory', 'qutlet-theme' ),
	'urzadzenia-sieciowe'    => __( 'Urządzenia sieciowe', 'qutlet-theme' ),
	'agd-drobne'             => __( 'AGD drobne', 'qutlet-theme' ),
);
?>
<!-- wp:group {"tagName":"section","className":"wrap home-section-tight"} -->
<section class="wp-block-group wrap home-section-tight">

<!-- wp:group {"className":"section-head-solo"} -->
<div class="wp-block-group section-head-solo">
<!-- wp:heading {"className":"section-title"} -->
<h2 class="wp-block-heading section-title"><?php esc_html_e( 'Przeglądaj kategorie', 'qutlet-theme' ); ?></h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:html -->
<div class="cat-grid">
<?php foreach ( $categories as $slug => $label ) : ?>
	<?php
	$term = get_term_by( 'slug', $slug, 'product_cat' );

	if ( ! ( $term instanceof WP_Term ) ) {
		continue;
	}

	$term_link = get_term_link( $term );

	if ( is_wp_error( $term_link ) ) {
		continue;
	}

	$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
	$has_image    = $thumbnail_id > 0;
	?>
	<a class="cat-tile<?php echo $has_image ? ' cat-tile-has-image' : ''; ?>" href="<?php echo esc_url( $term_link ); ?>">
		<?php if ( $has_image ) : ?>
			<?php echo wp_get_attachment_image( $thumbnail_id, 'medium', false, array( 'class' => 'cat-tile-img', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="cat-tile-scrim"></span>
		<?php endif; ?>
		<span class="cat-tile-label"><?php echo esc_html( $label ); ?></span>
	</a>
<?php endforeach; ?>
</div>
<!-- /wp:html -->

</section>
<!-- /wp:group -->
