<?php
/**
 * Title: Hero — strona główna
 * Slug: qutlet-theme/home-hero
 * Categories: qutlet
 * Description: Sekcja hero strony głównej (kicker, nagłówek, lead, chipy, CTA, zdjęcie). Port .home-hero-section/.hero (design/vanilla/index.html). Link CTA wpisany na sztywno (slug `strefa-okazji`, jak inne odnośniki między-stronowe w tej bibliotece patternów, np. how-cta).
 * Keywords: hero, strona główna
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$hero_image_url = get_theme_file_uri( 'assets/images/home-hero.png' );
?>
<!-- wp:group {"tagName":"section","className":"home-hero-section"} -->
<section class="wp-block-group home-hero-section">
<!-- wp:group {"className":"wrap"} -->
<div class="wp-block-group wrap">
<!-- wp:group {"className":"hero"} -->
<div class="wp-block-group hero">

<!-- wp:group -->
<div class="wp-block-group">

<!-- wp:paragraph {"className":"kicker kicker-lime"} -->
<p class="kicker kicker-lime"><?php esc_html_e( 'Drugie życie elektroniki', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e( 'Pełnowartościowy sprzęt.', 'qutlet-theme' ); ?><br><?php esc_html_e( 'Outletowa', 'qutlet-theme' ); ?> <span class="lime"><?php esc_html_e( 'cena.', 'qutlet-theme' ); ?></span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"hero-lead"} -->
<p class="hero-lead"><?php esc_html_e( 'Zwroty konsumenckie i sprzęt używany, sprawdzony i objęty gwarancją. Każdy produkt to konkretny, opisany egzemplarz — bez niespodzianek.', 'qutlet-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="hero-chips">
	<span class="hero-chip"><?php esc_html_e( 'Zwroty konsumenckie', 'qutlet-theme' ); ?></span>
	<span class="hero-chip"><?php esc_html_e( 'Produkty używane', 'qutlet-theme' ); ?></span>
	<span class="hero-chip"><?php esc_html_e( 'Gwarancja', 'qutlet-theme' ); ?></span>
	<span class="hero-chip hero-chip-lime"><?php esc_html_e( 'Ceny nawet -50%', 'qutlet-theme' ); ?></span>
</div>
<!-- /wp:html -->

<!-- wp:paragraph -->
<p><a href="/strefa-okazji/" class="hero-cta">
	<?php esc_html_e( 'Zobacz ofertę', 'qutlet-theme' ); ?>
	<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:group {"className":"hero-visual"} -->
<div class="wp-block-group hero-visual">
<!-- wp:html -->
<img src="<?php echo esc_url( $hero_image_url ); ?>" alt="<?php echo esc_attr__( 'Mysz gamingowa z podświetleniem RGB', 'qutlet-theme' ); ?>">
<!-- /wp:html -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
