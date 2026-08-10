<?php
/**
 * Title: Three Thirds Section
 * Slug: messengertheme/threethirds
 * Categories: page
 * Block Types: core/post-content
 * Post Types: page
 */
?>

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading text-[23px] sm:text-[30px]">Three Thirds Section</h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
    <!-- wp:column {"width":"33%"} -->
    <div class="wp-block-column" style="flex-basis:33%">
        <!-- wp:heading {"level":3} -->
        <h3 class="wp-block-heading text-[19px] sm:text-[25px]">Column one</h3>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p class="text-[16px] sm:text-[21px]">Column 1 text...</p>
        <!-- /wp:paragraph -->

         <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
            <figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder.jpg' ) ); ?>" alt="Placeholder image" title="Placeholder image"/></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"33%"} -->
    <div class="wp-block-column" style="flex-basis:33%">
        <!-- wp:heading {"level":3} -->
        <h3 class="wp-block-heading text-[19px] sm:text-[25px]">Column two</h3>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p class="text-[16px] sm:text-[21px]">Column two text...</p>
        <!-- /wp:paragraph -->

        <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
            <figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder.jpg' ) ); ?>" alt="Placeholder image" title="Placeholder image"/></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"33.33%"} -->
    <div class="wp-block-column" style="flex-basis:33.33%">
        <!-- wp:heading {"level":3} -->
        <h3 class="wp-block-heading text-[19px] sm:text-[25px]">Column three</h3>
        <!-- /wp:heading -->

        <!-- wp:paragraph -->
        <p class="text-[16px] sm:text-[21px]">Column three text...</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
            <figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder.jpg' ) ); ?>" alt="Placeholder image" title="Placeholder image"/></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:column -->
</div>
<!-- /wp:columns -->