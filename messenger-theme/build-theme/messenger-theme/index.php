<?php
/**
 * The site's entry point.
 *
 * Loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 *
 * @package MessengerTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$is_messenger_theme_exist = function_exists( 'messenger_theme_do_location' );

if ( is_singular() ) {
	if ( ! $is_messenger_theme_exist || ! messenger_theme_do_location( 'single' ) ) {
		get_template_part( 'template-parts/single' );
	}
} elseif ( is_archive() || is_home() ) {
	if ( ! $is_messenger_theme_exist || ! messenger_theme_do_location( 'archive' ) ) {
		get_template_part( 'template-parts/archive' );
	}
} elseif ( is_search() ) {
	if ( ! $is_messenger_theme_exist || ! messenger_theme_do_location( 'archive' ) ) {
		get_template_part( 'template-parts/search' );
	}
} else {
	if ( ! $is_messenger_theme_exist || ! messenger_theme_do_location( 'single' ) ) {
		get_template_part( 'template-parts/404' );
	}
}

get_footer();
