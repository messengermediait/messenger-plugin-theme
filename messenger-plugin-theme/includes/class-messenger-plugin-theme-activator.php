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
		$releases_table  = $wpdb->prefix . 'messenger_package_releases';

		$packages_sql = "CREATE TABLE $packages_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			type varchar(50) NOT NULL,
			UNIQUE KEY id (id)
		) ENGINE=InnoDB $charset_collate;";

		$installs_sql = "CREATE TABLE $installs_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			package_id mediumint(9) NOT NULL,
			site varchar(255) NOT NULL,
			install_key varchar(255) NOT NULL,
			UNIQUE KEY id (id),
			KEY package_id (package_id)
		) ENGINE=InnoDB $charset_collate;";

		$releases_sql = "CREATE TABLE $releases_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			package_id mediumint(9) NOT NULL,
			version varchar(50) NOT NULL,
			release_date datetime NOT NULL,
			release_notes varchar(5000) NOT NULL,
			file_path varchar(2048) NOT NULL,
			UNIQUE KEY id (id),
			KEY package_id (package_id)
		) ENGINE=InnoDB $charset_collate;";

		dbDelta( $packages_sql );
		dbDelta( $installs_sql );
		dbDelta( $releases_sql );

		$installs_foreign_key = 'mpt_install_package_' . substr( md5( $installs_table ), 0, 12 );
		$wpdb->query( "ALTER TABLE `$installs_table` ADD CONSTRAINT `$installs_foreign_key` FOREIGN KEY (package_id) REFERENCES `$packages_table` (id) ON DELETE CASCADE" );

		$releases_foreign_key = 'mpt_release_package_' . substr( md5( $releases_table ), 0, 12 );
		$wpdb->query( "ALTER TABLE `$releases_table` ADD CONSTRAINT `$releases_foreign_key` FOREIGN KEY (package_id) REFERENCES `$packages_table` (id) ON DELETE CASCADE" );
	}

}
