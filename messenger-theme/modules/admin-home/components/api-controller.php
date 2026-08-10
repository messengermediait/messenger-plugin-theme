<?php

namespace MessengerTheme\Modules\AdminHome\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use MessengerTheme\Modules\AdminHome\Rest\Admin_Config;
use MessengerTheme\Modules\AdminHome\Rest\Theme_Settings;

class Api_Controller {

	protected $endpoints = [];

	public function __construct() {
		$this->endpoints['admin-config'] = new Admin_Config();
		$this->endpoints['theme-settings'] = new Theme_Settings();
	}
}
