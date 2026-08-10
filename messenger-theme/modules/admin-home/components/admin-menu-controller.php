<?php

namespace MessengerTheme\Modules\AdminHome\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use MessengerTheme\Includes\Script;
use MessengerTheme\Includes\Utils;

class Admin_Menu_Controller {

	const MENU_PAGE_ICON = 'dashicons-plus-alt';
	const MENU_PAGE_POSITION = 59.9;
	const AI_SITE_PLANNER_SLUG = '-ai-site-planner';
	const THEME_BUILDER_SLUG = '-theme-builder';

	public function admin_menu(): void {
		add_menu_page(
			__( 'Messenger', 'messenger-theme' ),
			__( 'Messenger', 'messenger-theme' ),
			'manage_options',
			EHP_THEME_SLUG,
			[ $this, 'render_home' ],
			self::MENU_PAGE_ICON,
			self::MENU_PAGE_POSITION
		);

		add_submenu_page(
			EHP_THEME_SLUG,
			__( 'Home', 'messenger-theme' ),
			__( 'Home', 'messenger-theme' ),
			'manage_options',
			EHP_THEME_SLUG,
			[ $this, 'render_home' ]
		);

		do_action( 'messenger-plus-theme/admin-menu', EHP_THEME_SLUG );
	}

	public function render_home(): void {
		echo '<div id="ehe-admin-home"></div>';
	}

	public function redirect_menus(): void {
		$page = sanitize_key( filter_input( INPUT_GET, 'page', FILTER_UNSAFE_RAW ) );

		switch ( $page ) {
			case EHP_THEME_SLUG . self::AI_SITE_PLANNER_SLUG:
				wp_redirect( Utils::get_ai_site_planner_url() );
				exit;

			case EHP_THEME_SLUG . self::THEME_BUILDER_SLUG:
				wp_redirect( Utils::get_theme_builder_url() );
				exit;

			default:
				break;
		}
	}

	public function admin_enqueue_scripts() {
		$script = new Script(
			'messenger-theme-menu',
		);

		$script->enqueue();
	}

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'redirect_menus' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}
}
