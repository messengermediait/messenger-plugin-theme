<?php

namespace MessengerTheme\Modules\AdminHome\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use MessengerTheme\Includes\Utils;
use WP_REST_Server;

class Admin_Config extends Rest_Base {

	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/admin-settings',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_admin_config' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);
	}

	public function get_admin_config() {

		$config = [];

		$config = apply_filters( 'messenger-plus-theme/rest/admin-config', $config );

		$config['config'] = [
			'nonceInstall' => wp_create_nonce( 'updates' ),
		];

		return rest_ensure_response( [ 'config' => $config ] );
	}

}
