<?php
/**
 * Qutlet — wyniki wyszukiwania (P-23.4, Relevanssi wersja darmowa).
 *
 * Klasyczny szablon dobrany przez hierarchię plików WP (`search.php`) — ten
 * motyw blokowy nie ma `templates/search.html`, więc `locate_block_template()`
 * (patrz nagłówek `header.php`/`footer.php`) spada na ten plik, wzorem
 * `page-{slug}.php` z P-8.5. Zakres wyników (produkty + wpisy bloga, NIE
 * strony) jest zawężony w `Search::restrict_query()`
 * (`inc/features/Search/Search.php`, hook `pre_get_posts`) — Relevanssi
 * respektuje to samo `query_vars['post_type']`.
 *
 * Decyzja użytkownika (sesja 2026-08-20, brak wzorca w design/vanilla dla tej
 * strony): mieszane wyniki w DWÓCH osobnych sekcjach (Produkty / Wpisy blog),
 * nie jednej przeplatanej liście — każda sekcja reużywa GOTOWY, już
 * zbudowany render: karta produktu (`woocommerce/content-product.php`,
 * P-8.3a) w siatce `.grid-3` (jak Shop/kategoria) oraz karta wpisu
 * (`template-parts/blog/post-card.php`, P-8.4) w siatce `.post-grid` (jak
 * archiwum bloga). Sekcja znika całkowicie, gdy nie ma wyników jej typu.
 * Jedna paginacja dla całego (mieszanego) wyniku wyszukiwania —
 * `Blog::render_pagination()`, reużywalna niezależnie od typu treści w pętli.
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Theme\features\Blog\Blog;
use Qutlet\Theme\features\Search\Search;

defined( 'ABSPATH' ) || exit;

get_header();

$search_query    = get_search_query();
$products_count  = 0;
$posts_count     = 0;

// Licznik TYLKO dla niepustej frazy — dla pustej frazy `is_search()` (WP-core)
// i Relevanssi NIE zawężają głównego zapytania (`Search::restrict_query()`
// wychodzi wcześnie), więc `have_posts()` zwraca wtedy treść niezwiązaną z
// wyszukiwaniem; port `design/vanilla/js/app.js:456-459` (`if (qn) {...}`),
// który też liczy produkty/wpisy TYLKO dla niepustego zapytania — bez tego
// warunku `.search-summary` pokazywałby mylącą, niezerową liczbę tuż nad
// komunikatem pustego stanu „Wpisz, czego szukasz".
if ( ! empty( $search_query ) && have_posts() ) {
	while ( have_posts() ) :
		the_post();

		if ( 'product' === get_post_type() ) {
			// `is_visible()` (nie tylko post_type): Relevanssi trafia w tekst
			// produktu NIEZALEŻNIE od `product_visibility` (np. wycofany ze
			// sprzedaży `outofstock` z ustawieniem Woo „Hide out of stock
			// items"), a `woocommerce/content-product.php` na TAKIM produkcie
			// tylko wychodzi (`return;`), nic nie renderuje — bez tego warunku
			// sekcja „Produkty" pokazywała nagłówek nad pustą siatką (zgłoszone
			// przez użytkownika, zreprodukowane na frazie „naprawy", produkt
			// #1040, `_stock_status=outofstock`).
			$found_product = wc_get_product( get_the_ID() );

			if ( $found_product instanceof WC_Product && $found_product->is_visible() ) {
				++$products_count;
			}
		} elseif ( 'post' === get_post_type() ) {
			++$posts_count;
		}
	endwhile;
	rewind_posts();
}

$has_products = $products_count > 0;
$has_posts    = $posts_count > 0;

// Link „strefa okazji"/„blog" we wspólnym komunikacie pustego stanu (oba
// warianty niżej) — wzorem `wp_kses_post( sprintf( ... ) )` w
// `woocommerce/content-product.php`, bo sprintf wstawia gotowe znaczniki `<a>`.
$empty_state_links = sprintf(
	/* translators: 1: link do strefy okazji, 2: link do bloga. */
	__( 'Spróbuj innego hasła albo przejdź do %1$s lub %2$s.', 'qutlet-theme' ),
	'<a href="' . esc_url( '/strefa-okazji/' ) . '">' . esc_html__( 'strefy okazji', 'qutlet-theme' ) . '</a>',
	'<a href="' . esc_url( Blog::blog_url() ) . '">' . esc_html__( 'bloga', 'qutlet-theme' ) . '</a>'
);
?>
<main class="wrap page-main">
	<nav class="breadcrumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'qutlet-theme' ); ?></a><span class="sep">/</span>
		<span class="current"><?php esc_html_e( 'Wyniki wyszukiwania', 'qutlet-theme' ); ?></span>
	</nav>

	<div class="page-head">
		<h1 class="page-title">
			<?php
			printf(
				/* translators: %s: wyszukiwana fraza. */
				esc_html__( 'Wyniki wyszukiwania dla „%s”', 'qutlet-theme' ),
				esc_html( $search_query )
			);
			?>
		</h1>
		<p class="search-summary">
			<b><?php echo esc_html( Search::count_label( $products_count + $posts_count, __( 'wynik', 'qutlet-theme' ), __( 'wyniki', 'qutlet-theme' ), __( 'wyników', 'qutlet-theme' ) ) ); ?></b>
			— <?php esc_html_e( 'produkty i wpisy na blogu.', 'qutlet-theme' ); ?>
		</p>
	</div>

	<?php if ( empty( $search_query ) ) : ?>
		<div class="empty-state">
			<svg width="56" height="56" viewBox="0 0 24 24" fill="none" class="empty-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path><path d="M8 11h6"></path></svg>
			<h3><?php esc_html_e( 'Wpisz, czego szukasz', 'qutlet-theme' ); ?></h3>
			<p><?php echo wp_kses_post( $empty_state_links ); ?></p>
		</div>
	<?php elseif ( ! $has_products && ! $has_posts ) : ?>
		<div class="empty-state">
			<svg width="56" height="56" viewBox="0 0 24 24" fill="none" class="empty-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path><path d="M8 11h6"></path></svg>
			<h3>
				<?php
				printf(
					/* translators: %s: wyszukiwana fraza. */
					esc_html__( 'Brak wyników dla „%s”', 'qutlet-theme' ),
					esc_html( $search_query )
				);
				?>
			</h3>
			<p><?php echo wp_kses_post( $empty_state_links ); ?></p>
		</div>
	<?php else : ?>

		<?php if ( $has_products ) : ?>
			<section class="results-section">
				<div class="results-section-head">
					<h2><?php esc_html_e( 'Produkty', 'qutlet-theme' ); ?></h2>
					<span class="results-section-count"><?php echo esc_html( Search::count_label( $products_count, __( 'produkt', 'qutlet-theme' ), __( 'produkty', 'qutlet-theme' ), __( 'produktów', 'qutlet-theme' ) ) ); ?></span>
					<a class="results-section-more" href="<?php echo esc_url( '/strefa-okazji/' ); ?>">
						<?php esc_html_e( 'Cała strefa okazji', 'qutlet-theme' ); ?>
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
					</a>
				</div>
				<div class="grid-3">
					<?php
					while ( have_posts() ) :
						the_post();

						if ( 'product' === get_post_type() ) {
							wc_get_template_part( 'content', 'product' );
						}
					endwhile;
					rewind_posts();
					?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $has_posts ) : ?>
			<section class="results-section">
				<div class="results-section-head">
					<h2><?php esc_html_e( 'Wpisy na blogu', 'qutlet-theme' ); ?></h2>
					<span class="results-section-count"><?php echo esc_html( Search::count_label( $posts_count, __( 'wpis', 'qutlet-theme' ), __( 'wpisy', 'qutlet-theme' ), __( 'wpisów', 'qutlet-theme' ) ) ); ?></span>
					<a class="results-section-more" href="<?php echo esc_url( Blog::blog_url() ); ?>">
						<?php esc_html_e( 'Cały blog „Drugi obieg”', 'qutlet-theme' ); ?>
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
					</a>
				</div>
				<div class="post-grid">
					<?php
					while ( have_posts() ) :
						the_post();

						if ( 'post' === get_post_type() ) {
							get_template_part(
								'template-parts/blog/post-card',
								null,
								array(
									'post'    => get_post(),
									'variant' => 'grid',
								)
							);
						}
					endwhile;
					rewind_posts();
					?>
				</div>
			</section>
		<?php endif; ?>

		<?php Blog::render_pagination(); ?>

	<?php endif; ?>
</main>
<?php
get_footer();
