Messenger Plugin and Theme Manager
==================================

External Update API
-------------------

This plugin provides WordPress update metadata and release details for each
package:

  GET https://YOUR-UPDATE-DOMAIN/wp-json/messenger-plugin-theme/v1/package/PACKAGE-SLUG
  GET https://YOUR-UPDATE-DOMAIN/wp-json/messenger-plugin-theme/v1/package/PACKAGE-SLUG/details
  GET https://YOUR-UPDATE-DOMAIN/wp-json/messenger-plugin-theme/v1/package/PACKAGE-SLUG/download

The metadata and details endpoints are public. The download endpoint requires
the installation key as a bearer token. Create an installation for the package
in Messenger Admin and use that installation's key on the site being updated.
Never publish or commit the key.

Example updater integration
---------------------------

Add the following configuration to wp-config.php, replacing the example values.
Use the package's slug from Messenger Admin and the installation key assigned to
this site.

  define( 'ACME_UPDATE_DOMAIN', 'dev.messenger.com.au' );
  define( 'ACME_UPDATE_PACKAGE_SLUG', 'your-package-slug' );
  define( 'ACME_UPDATE_INSTALL_KEY', 'replace-with-this-sites-installation-key' );

The domain is a host name only: do not include https:// or a path. The default
domain shown above is the development update server; change it for another
server. Use a unique prefix instead of ACME in your own code to avoid naming
conflicts.

Shared helpers
--------------

Add these helpers to the plugin's main PHP file or the theme's functions.php.
The package check helper retrieves and validates the public update metadata.
The download helper intercepts only this package's download URL, then makes the
request with the installation key.

  function acme_get_package_update_data() {
	  $domain = defined( 'ACME_UPDATE_DOMAIN' ) ? trim( ACME_UPDATE_DOMAIN ) : '';
	  $slug = defined( 'ACME_UPDATE_PACKAGE_SLUG' ) ? sanitize_title( ACME_UPDATE_PACKAGE_SLUG ) : '';
	  if ( '' === $domain || '' === $slug ) {
		  return false;
	  }

	  $url = 'https://' . $domain . '/wp-json/messenger-plugin-theme/v1/package/' . rawurlencode( $slug );
	  $response = wp_remote_get( $url, array(
		  'timeout' => 10,
		  'headers' => array( 'Accept' => 'application/json' ),
	  ) );

	  if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		  return false;
	  }

	  $data = json_decode( wp_remote_retrieve_body( $response ) );
	  if ( ! is_object( $data ) || ! isset( $data->type, $data->version, $data->details, $data->download_url ) ) {
		  return false;
	  }

	  return $data;
  }

  function acme_download_package_update( $reply, $package, $upgrader, $hook_extra ) {
	  if ( false !== $reply || ! defined( 'ACME_UPDATE_DOMAIN' ) || ! defined( 'ACME_UPDATE_PACKAGE_SLUG' ) ) {
		  return $reply;
	  }

	  $url = wp_parse_url( $package );
	  $slug = sanitize_title( ACME_UPDATE_PACKAGE_SLUG );
	  $expected_path = '/wp-json/messenger-plugin-theme/v1/package/' . $slug . '/download';
	  if (
		  ! is_array( $url )
		  || 'https' !== ( $url['scheme'] ?? '' )
		  || strtolower( ACME_UPDATE_DOMAIN ) !== strtolower( $url['host'] ?? '' )
		  || $expected_path !== ( $url['path'] ?? '' )
	  ) {
		  return $reply;
	  }

	  if ( ! defined( 'ACME_UPDATE_INSTALL_KEY' ) || '' === trim( ACME_UPDATE_INSTALL_KEY ) ) {
		  return new WP_Error( 'acme_update_key_missing', __( 'The package installation key is not configured.' ) );
	  }

	  $temp_file = wp_tempnam( 'acme-package-update.zip' );
	  if ( ! $temp_file ) {
		  return new WP_Error( 'acme_update_temp_file', __( 'A temporary update file could not be created.' ) );
	  }

	  $response = wp_remote_get( $package, array(
		  'timeout' => 300,
		  'stream' => true,
		  'filename' => $temp_file,
		  'headers' => array(
			  'Accept' => 'application/zip',
			  'Authorization' => 'Bearer ' . ACME_UPDATE_INSTALL_KEY,
		  ),
	  ) );

	  if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		  wp_delete_file( $temp_file );
		  return is_wp_error( $response ) ? $response : new WP_Error( 'acme_update_download_failed', __( 'The package update download failed.' ) );
	  }

	  return $temp_file;
  }

  add_filter( 'upgrader_pre_download', 'acme_download_package_update', 10, 4 );

Plugin update check
-------------------

For a plugin, add this check to its main PHP file. The Version header is read
from that file and the transient response is keyed by its plugin basename.

  add_filter( 'pre_set_site_transient_update_plugins', 'acme_check_plugin_update' );

  function acme_check_plugin_update( $transient ) {
	  if ( ! is_object( $transient ) || empty( $transient->checked ) ) {
		  return $transient;
	  }

	  $plugin_file = plugin_basename( __FILE__ );
	  $plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), 'plugin' );
	  $remote = acme_get_package_update_data();
	  if ( ! $remote || 'plugin' !== $remote->type || empty( $plugin_data['Version'] ) ) {
		  return $transient;
	  }

	  if ( version_compare( $plugin_data['Version'], $remote->version, '<' ) ) {
		  if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			  $transient->response = array();
		  }

		  $transient->response[ $plugin_file ] = array(
			  'slug' => ACME_UPDATE_PACKAGE_SLUG,
			  'plugin' => $plugin_file,
			  'new_version' => $remote->version,
			  'url' => $remote->details,
			  'package' => $remote->download_url,
		  );
	  }

	  return $transient;
  }

Theme update check
------------------

For a theme, add this check to functions.php. The installed theme stylesheet
slug is used as the transient response key.

  add_filter( 'pre_set_site_transient_update_themes', 'acme_check_theme_update' );

  function acme_check_theme_update( $transient ) {
	  if ( ! is_object( $transient ) ) {
		  return $transient;
	  }

	  $stylesheet = get_stylesheet();
	  $theme = wp_get_theme();
	  $remote = acme_get_package_update_data();
	  if ( ! $remote || 'theme' !== $remote->type ) {
		  return $transient;
	  }

	  if ( version_compare( $theme->get( 'Version' ), $remote->version, '<' ) ) {
		  if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			  $transient->response = array();
		  }

		  $transient->response[ $stylesheet ] = array(
			  'theme' => $stylesheet,
			  'new_version' => $remote->version,
			  'url' => $remote->details,
			  'package' => $remote->download_url,
		  );
	  }

	  return $transient;
  }

Test the integration from Dashboard > Updates after configuring the domain,
package slug, and site-specific installation key. The update details endpoint
returns the latest release notes; the download endpoint serves the latest ZIP
only when the bearer key belongs to an installation of that package.
