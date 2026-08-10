<?php
/**
 * Theme functions and definitions
 *
 * @package MessengerTheme
 */

/* ################## */
/*    TONY UPDATES    */
/* ################## */

require get_template_directory() . '/contact-form.php';

function my_dequeue_styles() {
    wp_dequeue_style( 'messenger-theme' );
    wp_dequeue_style( 'messenger-theme-theme-style' );
    wp_dequeue_style( 'messenger-theme-header-footer' );
}
//add_action( 'wp_enqueue_scripts', 'my_dequeue_styles', 100 );
	

/* ORGINAL */
/* --------*/
	
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'MESSENGER_THEME_VERSION', '0.1.0' );
define( 'EHP_THEME_SLUG', 'messenger-theme' );

define( 'MESSENGER_THEME_PATH', get_template_directory() );
define( 'MESSENGER_THEME_URL', get_template_directory_uri() );
define( 'MESSENGER_THEME_ASSETS_PATH', MESSENGER_THEME_PATH . '/assets/' );
define( 'MESSENGER_THEME_ASSETS_URL', MESSENGER_THEME_URL . '/assets/' );
define( 'MESSENGER_THEME_SCRIPTS_PATH', MESSENGER_THEME_ASSETS_PATH . 'js/' );
define( 'MESSENGER_THEME_SCRIPTS_URL', MESSENGER_THEME_ASSETS_URL . 'js/' );
define( 'MESSENGER_THEME_STYLE_PATH', MESSENGER_THEME_ASSETS_PATH . 'css/' );
define( 'MESSENGER_THEME_STYLE_URL', MESSENGER_THEME_ASSETS_URL . 'css/' );
define( 'MESSENGER_THEME_IMAGES_PATH', MESSENGER_THEME_ASSETS_PATH . 'images/' );
define( 'MESSENGER_THEME_IMAGES_URL', MESSENGER_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'messenger_theme_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function messenger_theme_setup() {
		if ( is_admin() ) {
			messenger_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'messenger_theme_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'messenger-theme' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'messenger-theme' ) ] );
		}

		if ( apply_filters( 'messenger_theme_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'messenger_theme_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'assets/css/editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'messenger_theme_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'messenger_theme_setup' );

function messenger_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'messenger_theme_version';
	// The theme version saved in the database.
	$messenger_theme_db_version = get_option( $theme_version_option_name );

	// If the 'messenger_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $messenger_theme_db_version || version_compare( $messenger_theme_db_version, MESSENGER_THEME_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, MESSENGER_THEME_VERSION );
	}
}

if ( ! function_exists( 'messenger_theme_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function messenger_theme_display_header_footer() {
		$messenger_theme_header_footer = true;

		return apply_filters( 'messenger_theme_header_footer', $messenger_theme_header_footer );
	}
}

if ( ! function_exists( 'messenger_theme_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function messenger_theme_scripts_styles() {
		/*if ( apply_filters( 'messenger_theme_enqueue_style', true ) ) {
			wp_enqueue_style(
				'messenger-theme',
				MESSENGER_THEME_STYLE_URL . 'reset.css',
				[],
				MESSENGER_THEME_VERSION
			);
		}

		if ( apply_filters( 'messenger_theme_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'messenger-theme-theme-style',
				MESSENGER_THEME_STYLE_URL . 'theme.css',
				[],
				MESSENGER_THEME_VERSION
			);
		}*/

		wp_enqueue_style(
			'messenger-theme-theme-style',
			MESSENGER_THEME_URL . '/style.css',
			[],
			MESSENGER_THEME_VERSION
		);

		if ( messenger_theme_display_header_footer() ) {
			wp_enqueue_style(
				'messenger-theme-header-footer',
				MESSENGER_THEME_STYLE_URL . 'header-footer.css',
				[],
				MESSENGER_THEME_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'messenger_theme_scripts_styles' );

function disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );   
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );     
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    //add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );
remove_action( 'wp_head', 'wp_generator' );

if ( ! function_exists( 'messenger_theme_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function messenger_theme_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'messenger_theme_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'messenger_theme_content_width', 0 );

if ( ! function_exists( 'messenger_theme_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function messenger_theme_add_description_meta_tag() {
		if ( ! apply_filters( 'messenger_theme_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'messenger_theme_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

if ( ! function_exists( 'messenger_theme_customizer' ) ) {
	// Customizer controls
	function messenger_theme_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! messenger_theme_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'messenger_theme_customizer' );

/**
 * BC:
 * In v2.7.0 the theme removed the `messenger_theme_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'messenger_theme_body_open' ) ) {
	function messenger_theme_body_open() {
		wp_body_open();
	}
}

add_filter( 'site_transient_update_themes', 'messenger_update_themes' );

function messenger_update_themes( $transient ) {

	$stylesheet = get_template();
	$theme = wp_get_theme();
	$version = $theme->get( 'Version' );
	// connect to a remote server where the update information is stored
	$remote = wp_remote_get(
		'https://dev.messenger.com.au/wp-content/uploads/messenger_theme/info.json',
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept' => 'application/json'
			)
		)
	);

	// do nothing if errors
	if(
		is_wp_error( $remote )
		|| 200 !== wp_remote_retrieve_response_code( $remote )
		|| empty( wp_remote_retrieve_body( $remote ) )
	) {
		return $transient;
	}

	// encode the response body
	$remote = json_decode( wp_remote_retrieve_body( $remote ) );
	
	if( ! $remote ) {
		return $transient; // who knows, maybe JSON is not valid
	}
	
	$data = array(
		'theme' => $stylesheet,
		'url' => $remote->details_url,
		/*'requires' => $remote->requires,
		'requires_php' => $remote->requires_php,*/
		'new_version' => $remote->version,
		'package' => $remote->download_url,
	);

	// check all the versions now
	if(
		$remote
		&& version_compare( $version, $remote->version, '<' )
	) {
	
		$transient->response[ $stylesheet ] = $data;

	} /*else {

		$transient->no_update[ $stylesheet ] = $data;

	}*/

	return $transient;

}

/**
 * Register an email post type.
 */
function messenger_email_init() {
	$labels = array(
		'name'               => _x( 'Email Templates', 'emails'),
		'singular_name'      => _x( 'Email template', 'email' ),
		'menu_name'          => _x( 'Email template', 'admin menu' ),
		'add_new'            => _x( 'Add New', 'email'),
		'add_new_item'       => __( 'Draft New Email' ),
		'new_item'           => __( 'New Email' ),
		'edit_item'          => __( 'Edit Email' ),
		'view_item'          => __( 'View Email' ),
		'all_items'          => __( 'Email templates' ),
		'search_items'       => __( 'Search Emails' ),
		'not_found'          => __( 'No Email templates found.' ),
		'not_found_in_trash' => __( 'No Email templates found in Trash.' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'email' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'custom-fields' )
	);

	register_post_type( 'email_template', $args );
}

add_action( 'init', 'messenger_email_init');

add_action( 'init', function() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%postname%/' );
} );

require MESSENGER_THEME_PATH . '/theme.php';

MessengerTheme\Theme::instance();
