<?php
/**
 * Title: Tabela klas stanu
 * Slug: qutlet-theme/class-table
 * Categories: qutlet
 * Description: Tabela klas stanu A–D (core/table). Port .class-table (design/vanilla/jak-to-dziala.html). Head/body wiersze parsowane z HTML tabeli (WP core, źródło "query") — nagłówek bloku NIE duplikuje ich w JSON.
 * Keywords: tabela, klasy stanu
 * Viewport Width: 1240
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

use Qutlet\Core\ProductCondition\ClassDefinitionsTaxonomy;

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:table {"className":"class-table"} -->
<figure class="wp-block-table class-table"><table>
<thead>
<tr><th><?php esc_html_e( 'Klasa', 'qutlet-theme' ); ?></th><th><?php esc_html_e( 'Stan wizualny', 'qutlet-theme' ); ?></th><th><?php esc_html_e( 'Charakterystyka', 'qutlet-theme' ); ?></th></tr>
</thead>
<tbody>
<?php foreach ( ClassDefinitionsTaxonomy::all() as $row ) : ?>
<tr><td><span class="class-name"><span class="dot" style="background:<?php echo esc_attr( $row['kolor'] ); ?>"></span><?php echo esc_html( $row['opis_chip'] ); ?></span></td><td><?php echo esc_html( $row['stan_wizualny'] ); ?></td><td><?php echo esc_html( $row['charakterystyka'] ); ?></td></tr>
<?php endforeach; ?>
</tbody>
</table></figure>
<!-- /wp:table -->
