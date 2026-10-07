<?php

/**
 * Public package update API.
 *
 * @package Messenger_Plugin_Theme
 */
class Messenger_Plugin_Theme_API extends WP_REST_Controller {

	/**
	 * Register the package update API routes.
	 */
	public function register_routes() {
		register_rest_route(
			'messenger-plugin-theme/v1',
			'/package/(?P<package_id>\d+)',
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_package_update' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'messenger-plugin-theme/v1',
			'/package/(?P<package_id>\d+)/details',
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_package_details' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'messenger-plugin-theme/v1',
			'/package/(?P<package_id>\d+)/download',
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'download_package_release' ),
				'permission_callback' => array( $this, 'download_permission' ),
			)
		);
	}

	/**
	 * Return update data for a package.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_package_update( WP_REST_Request $request ) {
		$package_id = absint( $request['package_id'] );
		$package = $this->get_package( $package_id );
		if ( is_wp_error( $package ) ) {
			return $package;
		}

		$latest_release = $this->get_latest_release( $package_id );
		if ( is_wp_error( $latest_release ) ) {
			return $latest_release;
		}

		$download_url = rest_url(
			'messenger-plugin-theme/v1/package/' . $package_id . '/download'
		);
		$details_url = rest_url(
			'messenger-plugin-theme/v1/package/' . $package_id . '/details'
		);

		$data = array(
			'type' => 'Plugin' === $package->type ? 'plugin' : 'theme',
			'version' => $latest_release->version,
			'details' => $details_url,
			'download_url' => $download_url,
		);

		if ( 'Plugin' === $package->type ) {
			$data['plugin'] = $package->name;
		} else {
			$data['theme'] = $package->name;
		}

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Return the latest release notes for a package.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_package_details( WP_REST_Request $request ) {
		$package_id = absint( $request['package_id'] );
		$package = $this->get_package( $package_id );
		if ( is_wp_error( $package ) ) {
			return $package;
		}

		$latest_release = $this->get_latest_release( $package_id );
		if ( is_wp_error( $latest_release ) ) {
			return $latest_release;
		}

		return new WP_REST_Response(
			array(
				'package_id' => $package_id,
				'version' => $latest_release->version,
				'release_notes' => $latest_release->release_notes,
			),
			200
		);
	}

	/**
	 * Require an installation key in the Authorization bearer header.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function download_permission( WP_REST_Request $request ) {
		$authorization = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) ) : '';
		if ( ! preg_match( '/^Bearer\s+(.+)$/i', $authorization, $matches ) ) {
			return false;
		}

		$package_id = absint( $request['package_id'] );
		global $wpdb;
		$installs_table = $wpdb->prefix . 'messenger_installs';
		$install = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT install_key FROM `$installs_table` WHERE package_id = %d AND install_key = %s LIMIT 1",
				$package_id,
				$matches[1]
			)
		);

		if ( empty( $install ) ) {
			return false;
		}

		return function_exists( 'hash_equals' )
			? hash_equals( $install->install_key, $matches[1] )
			: $install->install_key === $matches[1];
	}

	/**
	 * Download the latest release for an authorized installation.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function download_package_release( WP_REST_Request $request ) {
		$package_id = absint( $request['package_id'] );
		$latest_release = $this->get_latest_release( $package_id );
		if ( is_wp_error( $latest_release ) ) {
			return $latest_release;
		}

		$private_directory = $this->get_private_release_directory();
		if ( is_wp_error( $private_directory ) ) {
			return $private_directory;
		}

		$file_path = realpath( $latest_release->file_path );
		if (
			false === $file_path
			|| dirname( $file_path ) !== $private_directory
			|| ! is_file( $file_path )
			|| ! is_readable( $file_path )
			|| 'zip' !== strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) )
		) {
			return new WP_Error( 'release_unavailable', __( 'The release file is unavailable.', 'messenger-plugin-theme' ), array( 'status' => 404 ) );
		}

		nocache_headers();
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( 'package-' . $package_id . '-v' . $latest_release->version . '.zip' ) . '"' );
		header( 'Content-Length: ' . filesize( $file_path ) );
		readfile( $file_path );
		exit;
	}

	/**
	 * Get a package record.
	 *
	 * @param int $package_id Package ID.
	 * @return object|WP_Error
	 */
	private function get_package( $package_id ) {
		global $wpdb;
		$packages_table = $wpdb->prefix . 'messenger_packages';
		$package = $wpdb->get_row(
			$wpdb->prepare( "SELECT id, name, type FROM `$packages_table` WHERE id = %d", $package_id )
		);

		if ( ! $package ) {
			return new WP_Error( 'package_not_found', __( 'The requested package could not be found.', 'messenger-plugin-theme' ), array( 'status' => 404 ) );
		}

		if ( ! in_array( $package->type, array( 'Plugin', 'Theme' ), true ) ) {
			return new WP_Error( 'unsupported_package_type', __( 'The requested package type is not supported.', 'messenger-plugin-theme' ), array( 'status' => 400 ) );
		}

		return $package;
	}

	/**
	 * Get the latest release record for a package.
	 *
	 * @param int $package_id Package ID.
	 * @return object|WP_Error
	 */
	private function get_latest_release( $package_id ) {
		global $wpdb;
		$releases_table = $wpdb->prefix . 'messenger_package_releases';
		$release = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, version, release_notes, file_path FROM `$releases_table` WHERE package_id = %d ORDER BY release_date DESC, id DESC LIMIT 1",
				$package_id
			)
		);

		if ( ! $release ) {
			return new WP_Error( 'release_not_found', __( 'The requested package has no published release.', 'messenger-plugin-theme' ), array( 'status' => 404 ) );
		}

		return $release;
	}

	/**
	 * Get the private release directory outside the document root.
	 *
	 * @return string|WP_Error
	 */
	private function get_private_release_directory() {
		$document_root = isset( $_SERVER['DOCUMENT_ROOT'] ) ? realpath( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) : realpath( ABSPATH );
		if ( false === $document_root ) {
			return new WP_Error( 'private_directory_unavailable', __( 'A private release directory could not be determined.', 'messenger-plugin-theme' ), array( 'status' => 500 ) );
		}

		$private_parent = dirname( $document_root );
		if ( $private_parent === $document_root ) {
			return new WP_Error( 'private_directory_unavailable', __( 'A private release directory could not be determined.', 'messenger-plugin-theme' ), array( 'status' => 500 ) );
		}

		$directory = $private_parent . DIRECTORY_SEPARATOR . '.messenger-plugin-theme' . DIRECTORY_SEPARATOR . 'releases';
		$real_directory = realpath( $directory );
		$document_root_prefix = trailingslashit( $document_root );
		if ( false === $real_directory || 0 === strpos( $real_directory, $document_root_prefix ) ) {
			return new WP_Error( 'private_directory_unavailable', __( 'The release directory is not outside the web root.', 'messenger-plugin-theme' ), array( 'status' => 500 ) );
		}

		return $real_directory;
	}
}
