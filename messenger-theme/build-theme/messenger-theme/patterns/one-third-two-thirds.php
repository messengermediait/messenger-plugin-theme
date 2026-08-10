<?php
/**
 * Title: One Third Two Thirds Section
 * Slug: messengertheme/onethirdtwothirds
 * Categories: page
 * Block Types: core/post-content
 * Post Types: page
 */
?>

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading text-[23px] sm:text-[38px]">One Third Two Thirds Section</h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
    <!-- wp:column {"width":"33.33%"} -->
    <div class="wp-block-column" style="flex-basis:33.33%">
        <!-- wp:heading {"level":3} -->
        <h3 class="wp-block-heading text-[19px] sm:text-[31px]">Left hand side</h3>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p class="text-[16px] sm:text-[27px]">Sample text...</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph -->
        <p class="text-[16px] sm:text-[27px]"><br><a href="#">Back to top of page</a></p>
        <!-- /wp:paragraph -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"66.66%"} -->
    <div class="wp-block-column" style="flex-basis:66.66%">
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
        <figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder.jpg' ) ); ?>" alt="Placeholder image" title="Placeholder image"/></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:column -->
</div>
<!-- /wp:columns -->