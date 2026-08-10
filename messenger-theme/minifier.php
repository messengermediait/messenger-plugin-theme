<?php

require_once(  "/src/Minify.php" );
require_once( "/src/CSS.php" );
require_once( "/src/JS.php" );
//require_once( "/../path-converter/ConverterInterface.php" );
//require_once( '/../path-converter/Converter.php' );

// Register admin menu
//add_action( 'admin_menu', 'wpsmy_add_admin_menu' );
function wpsmy_add_admin_menu() {
	// add_menu_page( 'WP Super Minify Settings', 'WP Super Minify', 'manage_options', 'wp-super-minify', 'wpsmy_admin_options', plugins_url('assets/images/wpsmy-icon-24x24.png', __FILE__) );
	// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	add_options_page( 'WP Super Minify', 'WP Super Minify', 'manage_options', 'wp-super-minify', 'wpsmy_admin_options' );
}

function wpsmy_minify_html( $buffer ) {
	$wpsmy_plugin_version = get_option( 'wpsmy_plugin_version' );
	$initial = strlen( $buffer );
	
	// Include Minify libraries only if not already loaded
	if ( !class_exists( 'Minify_HTML' ) ) {
		require_once( WPSMY_MINIFY_LIBRARY_PATH . "/lib/Minify/HTML.php" );
		require_once( WPSMY_MINIFY_LIBRARY_PATH . "/lib/Minify/Loader.php" );
		Minify_Loader::register();
	}

	// Minify inline JavaScript if enabled
	if ( get_option('wpsmy_combine_js', 1 ) === 'on') {
		$buffer = Minify_HTML::minify( $buffer, array(
			'jsMinifier' => array( 'JSMin', 'minify' )
		));
	}

	// Minify inline CSS if enabled
	if (get_option( 'wpsmy_combine_css', 1 ) === 'on') {
		$buffer = Minify_HTML::minify( $buffer, array(
			'cssMinifier' => array( 'Minify_CSS', 'minify' )
		));
	}

	// Calculate savings
	$final = strlen( $buffer );
	if ($initial > 0) {
		$savings = round((($initial - $final) / $initial * 100), 3);
	} else {
		$savings = 0; // Avoid division by zero
	}

	// Store the comment in a global variable instead of appending to the buffer
	if ( $savings > 0 ) {
		global $wpsmy_minify_comment;
		$wpsmy_minify_comment = PHP_EOL . '<!--' . PHP_EOL . 
			'*** This site runs WP Super Minify plugin v' . esc_html($wpsmy_plugin_version) . ' - http://wordpress.org/plugins/wp-super-minify ***' . PHP_EOL . 
			'*** Total size saved: ' . esc_html($savings) . '% | Size before compression: ' . esc_html($initial) . ' bytes | Size after compression: ' . esc_html($final) . ' bytes. ***' . PHP_EOL . 
			'-->';
	}

	return $buffer;
}

// Start HTML Minification with output buffering
function wpsmy_html_minify_start() {
	if (!is_admin() && !defined('DOING_AJAX')) {
		ob_start('wpsmy_minify_html');
	}
}
add_action( 'template_redirect', 'wpsmy_html_minify_start', 1 );

// Flush the buffer at the end of the page
function wpsmy_html_minify_end() {
	if (!is_admin() && ob_get_length()) {
		ob_end_flush();
	}
}
add_action( 'shutdown', 'wpsmy_html_minify_end', 9999 );

// Output minification comment at the bottom of the page
function wpsmy_print_minify_comment() {
	global $wpsmy_minify_comment;
	if (!empty($wpsmy_minify_comment)) {
		echo $wpsmy_minify_comment;
	}
}
add_action( 'shutdown', 'wpsmy_print_minify_comment', 10000 );

// Hook to process and replace scripts and styles
add_action( 'wp_enqueue_scripts', 'wpsmy_minify_enqueue_scripts', 999 );
function wpsmy_minify_enqueue_scripts() {
	global $wp_styles, $wp_scripts;

	// Minify and replace styles
	if ( !empty( $wp_styles->queue ) && get_option( 'wpsmy_combine_css', 1 ) === 'on' ) {
		foreach ($wp_styles->queue as $handle) {
			$src = $wp_styles->registered[$handle]->src;
			if ($src) {
				$minified_src = wpsmy_minify_file($src, 'css');
				if ($minified_src) {
					$wp_styles->registered[$handle]->src = $minified_src;
				}
			}
		}
	}

	// Minify and replace scripts
	if ( !empty( $wp_scripts->queue ) && get_option( 'wpsmy_combine_js', 1 ) === 'on' ) {
		foreach ($wp_scripts->queue as $handle) {
			$src = $wp_scripts->registered[$handle]->src;
			if ($src) {
				$minified_src = wpsmy_minify_file($src, 'js');
				if ($minified_src) {
					$wp_scripts->registered[$handle]->src = $minified_src;
				}
			}
		}
	}
}

/**
 * Minifies a given CSS or JS file and stores it.
 *
 * @param string $file_url URL of the original file.
 * @param string $type 'css' or 'js'.
 * @return string|false Minified file URL or false if failed.
 */
function wpsmy_minify_file( $file_url, $type ) {
	
	if ( strpos($file_url, '.min.') !== false ) {
		return $file_url; // Skip if already minified
	}

	$cache_filetype_dir = ( $type === 'js' ? 'js/' : 'css/' );
	$cache_url = content_url() . '/cache/wp-super-minify/';

	$file_path = str_replace(home_url(), ABSPATH, $file_url);
	$minified_file_name = md5($file_url) . '.' . $type;
	$minified_file_path = WPSMY_CACHE_DIR . $cache_filetype_dir . $minified_file_name;
	$minified_file_url = $cache_url . $cache_filetype_dir . $minified_file_name;
	$hash_file_path = $minified_file_path . '.hash'; // Store file hash

	// Check if original file exists
	if ( !file_exists( $file_path ) ) {
		return false; // Skip if file is not local
	}
	
	// Get current file hash
	$current_hash = md5_file($file_path);
	
	// Check if minified file exists and hash matches
	if ( file_exists($minified_file_path) && file_exists($hash_file_path) ) {
		$saved_hash = file_get_contents( $hash_file_path );
		if ( $saved_hash === $current_hash ) {
			return $minified_file_url; // Return existing minified file
		}
	}

	try {
		if ( $type === 'css' ) {
			$minifier = new MatthiasMullie\Minify\CSS( $file_path );
		} elseif ( $type === 'js' ) {
			$minifier = new MatthiasMullie\Minify\JS( $file_path );
		} else {
			return false;
		}

		$minifier->minify( $minified_file_path );
		
		// Store hash to track changes
		file_put_contents($hash_file_path, $current_hash);

		// Store in options table
		$stored_files = get_option( 'wpsmy_minified_files', [] );
		$stored_files[$file_url] = $minified_file_url;
		update_option( 'wpsmy_minified_files', $stored_files );

		return $minified_file_url;
	} catch ( Exception $e ) {
		return false;
	}
}

?>