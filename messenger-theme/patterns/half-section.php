<?php
/**
 * Title: Half Section
 * Slug: messengertheme/hero
 * Categories: page
 * Block Types: core/post-content
 * Post Types: page
 */
?>

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading text-[23px] sm:text-[29px]">One Third Two Thirds Section</h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
    <!-- wp:column -->
    <div class="wp-block-column">
        <!-- wp:heading {"level":3} -->
        <h3 class="wp-block-heading text-[19px] sm:text-[24px]">Left hand side</h3>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p class="text-[16px] sm:text-[20px]">Sample text</p>
        <!-- /wp:paragraph -->

    </div>
    <!-- /wp:column -->

    <!-- wp:column -->
    <div class="wp-block-column">
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
            <figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder.jpg' ) ); ?>" alt="Placeholder image" title="Placeholder image"/></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:column -->
</div>
<!-- /wp:columns -->