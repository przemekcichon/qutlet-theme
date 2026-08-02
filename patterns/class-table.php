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

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:table {"className":"class-table"} -->
<figure class="wp-block-table class-table"><table>
<thead>
<tr><th><?php esc_html_e( 'Klasa', 'qutlet-theme' ); ?></th><th><?php esc_html_e( 'Stan wizualny', 'qutlet-theme' ); ?></th><th><?php esc_html_e( 'Charakterystyka', 'qutlet-theme' ); ?></th></tr>
</thead>
<tbody>
<tr><td><span class="class-name"><span class="dot dot-a"></span><?php esc_html_e( 'Klasa A', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Jak nowy. Mikroryski.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Zwrot konsumencki. Oryginalne pudełko.', 'qutlet-theme' ); ?></td></tr>
<tr><td><span class="class-name"><span class="dot dot-b"></span><?php esc_html_e( 'Klasa B', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Dobry. Widoczne ryski.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Używany dłużej. Pudełko zastępcze.', 'qutlet-theme' ); ?></td></tr>
<tr><td><span class="class-name"><span class="dot dot-c"></span><?php esc_html_e( 'Klasa C', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Mocne ślady zużycia.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Sprawny technicznie, widoczna historia użytkowania.', 'qutlet-theme' ); ?></td></tr>
<tr><td><span class="class-name"><span class="dot dot-d"></span><?php esc_html_e( 'Klasa D', 'qutlet-theme' ); ?></span></td><td><?php esc_html_e( 'Na części.', 'qutlet-theme' ); ?></td><td><?php esc_html_e( 'Niesprawny technicznie.', 'qutlet-theme' ); ?></td></tr>
</tbody>
</table></figure>
<!-- /wp:table -->
