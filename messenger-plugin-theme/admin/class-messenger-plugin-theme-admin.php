<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Messenger_Plugin_Theme
 * @subpackage Messenger_Plugin_Theme/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Messenger_Plugin_Theme
 * @subpackage Messenger_Plugin_Theme/admin
 * @author     Your Name <email@example.com>
 */
class Messenger_Plugin_Theme_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Messenger_Plugin_Theme_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Messenger_Plugin_Theme_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/messenger-plugin-theme-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Messenger_Plugin_Theme_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Messenger_Plugin_Theme_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/messenger-plugin-theme-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function setup_menu() {
		add_menu_page('Messenger Admin', 'Messenger Admin', 'read', 'messenger-admin-options', [$this, 'DisplayMainAdmin']);
	}

	public function DisplayMainAdmin() {
		$packages = [
			(object)["name" => "Messenger Theme", "type" => "Theme", "version" => "0.1.2", "installs" => 1],
			(object)["name" => "Cargo Tracking", "type" => "Plugin", "version" => "1.18", "installs" => 3],
			(object)["name" => "Cargo Tracking", "type" => "Theme", "version" => "1.1.6", "installs" => 3],
		];
		?>
		<style>
			.messenger-package-container table {
			border: 1px solid rgba(0, 0, 0, 0.5);
			border-radius: 0.5em;
			width: 100%;
			box-shadow: 0px 1px 4px rgba(128, 128, 128, 0.5);
		}
		.messenger-package-container tr:nth-child(odd) {
			background: rgba(255, 255, 255, 0.75);
		}
		.messenger-package-container th {
			background: #fff;
			padding: 0.5em;
			font-size: 1.2em;
			border: 1px solid #fff;
		}
		.messenger-package-container td {
			padding: 0.5em 1.2em;
			font-size: 1.2em;
		}
		a.btn {
			margin: 1em;
			display: inline-block;
			box-shadow: 0px 1px 2px #000;
			border-radius: 4px;
			background: #3858e9;
			color: #fff;
			padding: 0.5em;
			text-shadow: 0 1px 1px #000;
			text-decoration: none;
		}
		</style>
		<div class="wrap">
			<h2>Manage Messenger Plugin and Theme Installations</h2>
			<div class="messenger-plugin-theme-admin">
				<h3>Packages</h3>
					<div class="messenger-package-container">
						<table>
							<tr><th>Name</th><th>Type</th><th>Version</th><th>Installs</th><th></th></tr>
						<?php
						foreach ($packages as $package) { ?>
							<tr><td><?php echo $package->name; ?></td><td><?php echo $package->type; ?></td><td><?php echo $package->version; ?></td><td><?php echo $package->installs; ?></td><td><a href="#" class="btn">View</a></td></tr>
						<?php
						} ?>
						</table>
					</div>
					<div><a href="#" class="btn">Create New Package</a></div>
			</div>
		</div>
		<?php
	}

}
