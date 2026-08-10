<?php

namespace MessengerTheme\Modules\AdminHome;

use MessengerTheme\Includes\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * class Module
 *
 * @package MessengerPlus
 * @subpackage MessengerPlusModules
 */
class Module extends Module_Base {
	/**
	 * @inheritDoc
	 */
	public static function get_name(): string {
		return 'admin-home';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_component_ids(): array {
		return [
			'Admin_Menu_Controller',
			'Scripts_Controller',
			'Api_Controller',
			'Ajax_Handler',
			'Admin_Top_Bar',
			'Settings_Controller'
		];
	}
}
