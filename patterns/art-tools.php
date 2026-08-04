<?php
/**
 * Title: Artykuł — czego potrzebujesz
 * Slug: qutlet-theme/art-tools
 * Categories: qutlet
 * Description: Ramka z checklistą narzędzi/materiałów potrzebnych do instrukcji (P-11.5). Port .art-tools (design/vanilla/blog-artykul.html). Lista jako prawdziwy blok core/list — WŁASNY HTML odrzucony (zgłoszenie użytkownika, ground-truth runtime: podgląd bloku Custom HTML renderuje się w edytorze w odizolowanym `<iframe sandbox>` bez `allow-same-origin`, więc CSS motywu fizycznie tam nie dociera — bullet pointy i inny kształt checkmarka w edytorze, mimo poprawnego frontu). Checkmark jako czysty CSS `::before` na `<li>`, bez SVG — działa identycznie w edytorze i na froncie. Kosztem: pozycja listy to jeden jednolity rozmiar tekstu (bez wyszarzonego/pomniejszonego dopisku `<small>` jak w prototypie — core/list-item nie ma na to formatowania).
 * Keywords: artykuł, blog, narzędzia, checklista
 * Viewport Width: 720
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"className":"art-tools"} -->
<div class="wp-block-group art-tools">

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Czego potrzebujesz', 'qutlet-theme' ); ?></h4>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li><?php esc_html_e( 'Narzędzie/materiał — szacunkowy koszt', 'qutlet-theme' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e( 'Narzędzie/materiał — szacunkowy koszt', 'qutlet-theme' ); ?></li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

</div>
<!-- /wp:group -->
