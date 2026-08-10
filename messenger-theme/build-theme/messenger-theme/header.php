<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package MessengerTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$viewport_content = apply_filters( 'messenger_theme_viewport_content', 'width=device-width, initial-scale=1' );
$enable_skip_link = apply_filters( 'messenger_theme_enable_skip_link', true );
$skip_link_url = apply_filters( 'messenger_theme_skip_link_url', '#content' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php if ( $enable_skip_link ) { ?>
<a class="skip-link screen-reader-text" href="<?php echo esc_url( $skip_link_url ); ?>"><?php echo esc_html__( 'Skip to content', 'messenger-theme' ); ?></a>
<?php } ?>
<div class="messenger-outer-flex">
	<div class="messenger-inner-flex">
<?php
if ( ! function_exists( 'messenger_theme_do_location' ) || ! messenger_theme_do_location( 'header' ) ) {
	if ( messenger_theme_display_header_footer() ) {
		get_template_part( 'template-parts/header' );
	}
}
