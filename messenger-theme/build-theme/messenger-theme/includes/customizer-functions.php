<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Customizer controls for header & footer.
 *
 * @return void
 */
function messenger_customizer_register( $wp_customize ) {
	require_once get_template_directory() . '/includes/customizer/customizer-action-links.php';

	$wp_customize->add_section(
		'messenger-options',
		[
			'title' => esc_html__( 'Header & Footer', 'messenger-theme' ),
			'capability' => 'edit_theme_options',
		]
	);

	$wp_customize->add_setting(
		'messenger-header-footer',
		[
			'sanitize_callback' => false,
			'transport' => 'refresh',
		]
	);

	$wp_customize->add_control(
		new MessengerTheme\Includes\Customizer\Messenger_Customizer_Action_Links(
			$wp_customize,
			'messenger-header-footer',
			[
				'section' => 'messenger-options',
				'priority' => 20,
			]
		)
	);
}
add_action( 'customize_register', 'messenger_customizer_register' );

/**
 * Enqueue Customizer CSS.
 *
 * @return void
 */
function messenger_customizer_styles() {
	wp_enqueue_style(
		'messenger-theme-customizer',
		MESSENGER_THEME_STYLE_URL . 'customizer.css',
		[],
		MESSENGER_THEME_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'messenger_customizer_styles' );
