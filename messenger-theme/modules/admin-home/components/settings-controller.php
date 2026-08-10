<?php

namespace MessengerTheme\Modules\AdminHome\Components;

use MessengerTheme\Includes\Script;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Controller {

	const SETTINGS_FILTER_NAME = 'messenger-plus-theme/settings';
	const SETTINGS_PAGE_SLUG = 'messenger-theme-settings';
	const SETTING_PREFIX = 'messenger_theme_settings';
	const SETTINGS = [
		'DESCRIPTION_META_TAG' => '_description_meta_tag',
		'SKIP_LINK'            => '_skip_link',
		'HEADER_FOOTER'        => '_header_footer',
		'PAGE_TITLE'           => '_page_title',
		'MESSENGER_STYLE'          => '_messenger_style',
		'MESSENGER_THEME'          => '_messenger_theme',
	];

	public static function get_settings_mapping(): array {
		return array_map(
			function ( $key ) {
				return self::SETTING_PREFIX . $key;
			},
			self::SETTINGS
		);
	}

	public static function get_settings(): array {

		$settings = array_map( function ( $key ) {
			return self::get_option( $key ) === 'true';
		}, self::get_settings_mapping() );

		return apply_filters( self::SETTINGS_FILTER_NAME, $settings );
	}

	protected static function get_option( string $option_name, $default_value = false ) {
		$option = get_option( $option_name, $default_value );

		return apply_filters( self::SETTINGS_FILTER_NAME . '/' . $option_name, $option );
	}

	public function legacy_register_settings() {
		$this->register_settings();
		$this->apply_settings();
	}

	public function apply_setting( $setting, $tweak_callback ) {
		$option = get_option( $setting );

		if ( isset( $option ) && ( 'true' === $option ) && is_callable( $tweak_callback ) ) {
			$tweak_callback();
		}

	}

	public function apply_settings( $settings_group = self::SETTING_PREFIX, $settings = self::SETTINGS ) {

		$this->apply_setting(
			$settings_group . $settings['DESCRIPTION_META_TAG'],
			function () {
				remove_action( 'wp_head', 'messenger_theme_add_description_meta_tag' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['SKIP_LINK'],
			function () {
				add_filter( 'messenger_theme_enable_skip_link', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['HEADER_FOOTER'],
			function () {
				add_filter( 'messenger_theme_header_footer', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['PAGE_TITLE'],
			function () {
				add_filter( 'messenger_theme_page_title', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['MESSENGER_STYLE'],
			function () {
				add_filter( 'messenger_theme_enqueue_style', '__return_false' );
			}
		);

		$this->apply_setting(
			$settings_group . $settings['MESSENGER_THEME'],
			function () {
				add_filter( 'messenger_theme_enqueue_theme_style', '__return_false' );
			}
		);
	}

	public function register_settings( $settings_group = self::SETTING_PREFIX, $settings = self::SETTINGS ) {
		foreach ( $settings as $setting_value ) {
			register_setting(
				$settings_group,
				$settings_group . $setting_value,
				[
					'default' => '',
					'show_in_rest' => true,
					'type' => 'string',
				]
			);
		}
	}

	public function enqueue_messenger_plus_settings_scripts() {
		$screen = get_current_screen();

		if ( ! str_ends_with( $screen->id, '_page_' . self::SETTINGS_PAGE_SLUG ) ) {
			return;
		}

		$script = new Script(
			'messenger-theme-settings',
			[ 'wp-util' ]
		);

		$script->enqueue();
	}

	public function register_settings_page( $parent_slug ): void {
		add_submenu_page(
			$parent_slug,
			__( 'Settings', 'messenger-theme' ),
			__( 'Settings', 'messenger-theme' ),
			'manage_options',
			self::SETTINGS_PAGE_SLUG,
			[ $this, 'render_settings_page' ]
		);
	}

	public function render_settings_page(): void {
		echo '<div id="ehe-admin-settings"></div>';
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_messenger_plus_settings_scripts' ] );
		add_action( 'messenger-plus-theme/admin-menu', [ $this, 'register_settings_page' ], 10, 1 );
	}
}
