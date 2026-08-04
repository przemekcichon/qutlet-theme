<?php
/**
 * Title: Artykuł — zdjęcie w treści
 * Slug: qutlet-theme/art-figure
 * Categories: qutlet
 * Description: Zdjęcie WEWNĄTRZ prozy artykułu z podpisem, kadrowane 16:9 (P-11.5) — INNE niż hero na górze artykułu (to renderuje dynamiczny blok qutlet/article-header, P-11.3). Port .art-figure/.art-img (design/vanilla/blog-artykul.html), na natywnym core/image — bez wrappera .art-img (kadrowanie 16:9 redaktor włącza sam po wgraniu zdjęcia: panel bloku → Aspect ratio 16:9 + Scale Cover). Bez zdjęcia startowego (pusty placeholder bloku core/image — ground-truth runtime, P-11.5: to JEDYNY kształt `<figure class="wp-block-image"><img alt=""/></figure>` przechodzący walidację bloku, jakikolwiek atrybut typu aspectRatio/sizeSlug bez wybranego zdjęcia łamie walidację).
 * Keywords: artykuł, blog, zdjęcie
 * Viewport Width: 720
 *
 * @package Qutlet\Theme
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:image {"className":"art-figure"} -->
<figure class="wp-block-image art-figure"><img alt="" /></figure>
<!-- /wp:image -->
