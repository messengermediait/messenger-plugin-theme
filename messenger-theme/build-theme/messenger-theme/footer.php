<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package MessengerTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
</div> <!-- close inner flex -->
<?php
if ( ! function_exists( 'messenger_theme_do_location' ) || ! messenger_theme_do_location( 'footer' ) ) {
	if ( messenger_theme_display_header_footer() ) {
		get_template_part( 'template-parts/footer' );
	}
}

wp_footer(); ?>

</div> <!-- close outer flex -->

</body>
</html>
