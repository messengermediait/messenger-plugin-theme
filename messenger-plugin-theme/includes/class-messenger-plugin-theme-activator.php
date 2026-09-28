<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Messenger_Plugin_Theme
 * @subpackage Messenger_Plugin_Theme/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Messenger_Plugin_Theme
 * @subpackage Messenger_Plugin_Theme/includes
 * @author     Your Name <email@example.com>
 */
class Messenger_Plugin_Theme_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$packages_table  = $wpdb->prefix . 'messenger_packages';
		$installs_table  = $wpdb->prefix . 'messenger_installs';

		$packages_sql = "CREATE TABLE $packages_table (
			id varchar(191) NOT NULL,
			name varchar(255) NOT NULL,
			type varchar(50) NOT NULL,
			version varchar(50) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB $charset_collate;";

		$installs_sql = "CREATE TABLE $installs_table (
			install_row_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			id varchar(191) NOT NULL,
			package_id varchar(191) NOT NULL,
			site varchar(255) NOT NULL,
			install_key varchar(255) NOT NULL,
			PRIMARY KEY  (install_row_id),
			KEY package_id (package_id)
		) ENGINE=InnoDB $charset_collate;";

		dbDelta( $packages_sql );
		dbDelta( $installs_sql );

		$foreign_key_name = 'mpt_install_package_' . substr( md5( $installs_table ), 0, 12 );
		$wpdb->query( "ALTER TABLE `$installs_table` ADD CONSTRAINT `$foreign_key_name` FOREIGN KEY (package_id) REFERENCES `$packages_table` (id) ON DELETE CASCADE" );
	}

}
