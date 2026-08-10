<?php

namespace MessengerTheme\Modules\AdminHome\Rest;

use MessengerTheme\Modules\AdminHome\Module;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Rest_Base {
	const ROUTE_NAMESPACE = 'messenger-theme/v1';

	abstract public function register_routes();

	public function permission_callback(): bool {
		return current_user_can( 'manage_options' );
	}

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}
}
