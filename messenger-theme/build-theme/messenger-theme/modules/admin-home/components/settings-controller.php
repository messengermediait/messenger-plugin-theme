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
		$update_domain = get_option( 'messenger_update_domain', 'dev.messenger.com.au' );
		$saved_update_key = get_option( 'messenger_update_key', '' );
		$has_update_key = is_string( $saved_update_key ) && '' !== trim( $saved_update_key );
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Theme Update Settings', 'messenger-theme' ); ?></h2>
			<?php if ( isset( $_GET['messenger_update_settings_saved'] ) && '1' === $_GET['messenger_update_settings_saved'] ) { ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Update settings saved.', 'messenger-theme' ); ?></p></div>
			<?php } ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="messenger_theme_save_update_settings" />
				<?php wp_nonce_field( 'messenger_theme_save_update_settings', 'messenger_theme_update_settings_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="messenger-update-domain"><?php esc_html_e( 'Update server domain', 'messenger-theme' ); ?></label></th>
						<td><input name="messenger_update_domain" type="text" id="messenger-update-domain" class="regular-text" value="<?php echo esc_attr( $update_domain ); ?>" placeholder="dev.messenger.com.au" required /><p class="description"><?php esc_html_e( 'Enter the host name only, without a scheme or path.', 'messenger-theme' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><label for="messenger-update-key"><?php esc_html_e( 'Installation key', 'messenger-theme' ); ?></label></th>
						<td><input name="messenger_update_key" type="password" id="messenger-update-key" class="regular-text" value="" autocomplete="new-password" placeholder="<?php echo esc_attr( $has_update_key ? __( 'Configured; leave blank to keep it', 'messenger-theme' ) : __( 'Enter installation key', 'messenger-theme' ) ); ?>" /><label><input name="messenger_update_key_clear" type="checkbox" value="1" /> <?php esc_html_e( 'Clear the saved key', 'messenger-theme' ); ?></label></td>
					</tr>
				</table>
				<?php submit_button( __( 'Save Update Settings', 'messenger-theme' ) ); ?>
			</form>
		</div>
		<?php
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_messenger_plus_settings_scripts' ] );
		add_action( 'messenger-plus-theme/admin-menu', [ $this, 'register_settings_page' ], 10, 1 );
		add_action( 'admin_post_messenger_theme_save_update_settings', [ $this, 'save_update_settings' ] );
	}

	public function save_update_settings(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to change theme update settings.', 'messenger-theme' ) );
		}

		check_admin_referer( 'messenger_theme_save_update_settings', 'messenger_theme_update_settings_nonce' );

		$domain = isset( $_POST['messenger_update_domain'] ) ? strtolower( trim( sanitize_text_field( wp_unslash( $_POST['messenger_update_domain'] ) ) ) ) : '';
		$key = isset( $_POST['messenger_update_key'] ) && is_string( $_POST['messenger_update_key'] ) ? trim( wp_unslash( $_POST['messenger_update_key'] ) ) : '';
		$clear_key = isset( $_POST['messenger_update_key_clear'] ) && '1' === $_POST['messenger_update_key_clear'];
		$parsed_domain = wp_parse_url( 'https://' . $domain );
		$valid_domain = is_array( $parsed_domain )
			&& ! empty( $parsed_domain['host'] )
			&& ! isset( $parsed_domain['user'] )
			&& ! isset( $parsed_domain['pass'] )
			&& ! isset( $parsed_domain['port'] )
			&& ! isset( $parsed_domain['query'] )
			&& ! isset( $parsed_domain['fragment'] )
			&& ( ! isset( $parsed_domain['path'] ) || '' === $parsed_domain['path'] )
			&& ( false !== filter_var( $parsed_domain['host'], FILTER_VALIDATE_IP ) || false !== filter_var( $parsed_domain['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) );

		if ( ! $valid_domain ) {
			wp_die( esc_html__( 'Enter a valid update server domain.', 'messenger-theme' ) );
		}
		if ( '' !== $key && ! preg_match( '/^[A-Za-z0-9!@#$%^&*()]{1,255}$/D', $key ) ) {
			wp_die( esc_html__( 'The installation key contains unsupported characters.', 'messenger-theme' ) );
		}

		update_option( 'messenger_update_domain', $parsed_domain['host'] );
		if ( $clear_key ) {
			delete_option( 'messenger_update_key' );
		} elseif ( '' !== $key ) {
			update_option( 'messenger_update_key', $key );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => self::SETTINGS_PAGE_SLUG,
					'messenger_update_settings_saved' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}
}
