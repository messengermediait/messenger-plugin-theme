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
		add_menu_page('Messenger Admin', 'Messenger Admin', 'manage_options', 'messenger-admin-options', [$this, 'DisplayMainAdmin']);
	}

	public function handle_create_package() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to create packages.', 'messenger-plugin-theme' ) );
		}

		check_admin_referer( 'messenger_create_package', 'messenger_create_package_nonce' );

		$name = isset( $_POST['package_name'] ) ? sanitize_text_field( wp_unslash( $_POST['package_name'] ) ) : '';
		$type = isset( $_POST['package_type'] ) ? sanitize_text_field( wp_unslash( $_POST['package_type'] ) ) : '';
		$slug = isset( $_POST['package_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['package_slug'] ) ) : '';
		$slug_pattern = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

		if ( '' === $name || ! in_array( $type, array( 'Theme', 'Plugin' ), true ) || ! preg_match( $slug_pattern, $slug ) ) {
			wp_die( esc_html__( 'Enter a package name, type, and a lowercase hyphen-separated slug.', 'messenger-plugin-theme' ) );
		}

		global $wpdb;
		$packages_table = $wpdb->prefix . 'messenger_packages';
		$duplicate_slug = $wpdb->get_var(
			$wpdb->prepare( "SELECT slug FROM `$packages_table` WHERE slug = %s", $slug )
		);
		if ( $duplicate_slug ) {
			wp_die( esc_html__( 'A package with this slug already exists.', 'messenger-plugin-theme' ) );
		}

		$inserted = $wpdb->insert(
			$packages_table,
			array(
				'name' => $name,
				'type' => $type,
				'slug' => $slug,
			),
			array( '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			wp_die( esc_html__( 'The package could not be created.', 'messenger-plugin-theme' ) );
		}

		$redirect_url = add_query_arg(
			array(
				'page'              => 'messenger-admin-options',
				'package_created'   => '1',
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	public function handle_delete_package() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to delete packages.', 'messenger-plugin-theme' ) );
		}

		$package_id = isset( $_POST['package_id'] ) ? absint( $_POST['package_id'] ) : 0;
		check_admin_referer( 'messenger_delete_package_' . $package_id, 'messenger_delete_package_nonce' );

		if ( ! $package_id ) {
			wp_die( esc_html__( 'The selected package does not exist.', 'messenger-plugin-theme' ) );
		}

		global $wpdb;
		$packages_table = $wpdb->prefix . 'messenger_packages';
		$releases_table = $wpdb->prefix . 'messenger_package_releases';
		$release_files = $wpdb->get_col(
			$wpdb->prepare( "SELECT file_path FROM `$releases_table` WHERE package_id = %d", $package_id )
		);

		if ( false === $wpdb->delete( $packages_table, array( 'id' => $package_id ), array( '%d' ) ) ) {
			wp_die( esc_html__( 'The package could not be deleted.', 'messenger-plugin-theme' ) );
		}

		foreach ( $release_files as $release_file ) {
			wp_delete_file( $release_file );
		}

		$redirect_url = add_query_arg(
			array(
				'page' => 'messenger-admin-options',
				'package_deleted' => '1',
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	public function handle_publish_package_release() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to publish package releases.', 'messenger-plugin-theme' ) );
		}

		$package_id = isset( $_POST['package_id'] ) ? absint( $_POST['package_id'] ) : 0;
		check_admin_referer( 'messenger_publish_release_' . $package_id, 'messenger_publish_release_nonce' );

		$version = isset( $_POST['release_version'] ) ? sanitize_text_field( wp_unslash( $_POST['release_version'] ) ) : '';
		$notes   = isset( $_POST['release_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['release_notes'] ) ) : '';
		$semver_pattern = '/^(0|[1-9][0-9]*)\\.(0|[1-9][0-9]*)\\.(0|[1-9][0-9]*)(?:-([0-9A-Za-z-]+(?:\\.[0-9A-Za-z-]+)*))?(?:\\+([0-9A-Za-z-]+(?:\\.[0-9A-Za-z-]+)*))?$/';

		if ( ! $package_id || ! preg_match( $semver_pattern, $version ) ) {
			wp_die( esc_html__( 'Enter a valid semantic version and package.', 'messenger-plugin-theme' ) );
		}

		$notes_length = function_exists( 'mb_strlen' ) ? mb_strlen( $notes, 'UTF-8' ) : strlen( $notes );
		if ( $notes_length > 5000 ) {
			wp_die( esc_html__( 'Release notes must be 5,000 characters or fewer.', 'messenger-plugin-theme' ) );
		}

		if ( ! isset( $_FILES['release_zip']['error'] ) || UPLOAD_ERR_OK !== $_FILES['release_zip']['error'] ) {
			wp_die( esc_html__( 'Choose a ZIP file to upload.', 'messenger-plugin-theme' ) );
		}

		global $wpdb;
		$packages_table = $wpdb->prefix . 'messenger_packages';
		$releases_table = $wpdb->prefix . 'messenger_package_releases';
		$package_exists = $wpdb->get_var(
			$wpdb->prepare( "SELECT id FROM `$packages_table` WHERE id = %d", $package_id )
		);

		if ( ! $package_exists ) {
			wp_die( esc_html__( 'The selected package does not exist.', 'messenger-plugin-theme' ) );
		}

		$file_path = $this->store_private_release_zip( $_FILES['release_zip'] );
		if ( is_wp_error( $file_path ) ) {
			wp_die( esc_html( $file_path->get_error_message() ) );
		}

		$inserted = $wpdb->insert(
			$releases_table,
			array(
				'package_id'   => $package_id,
				'version'      => $version,
				'release_date' => current_time( 'mysql', true ),
				'release_notes' => $notes,
				'file_path'    => $file_path,
			),
			array( '%d', '%s', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			wp_delete_file( $file_path );
			wp_die( esc_html__( 'The release could not be saved.', 'messenger-plugin-theme' ) );
		}

		$redirect_url = add_query_arg(
			array(
				'page'            => 'messenger-admin-options',
				'package_id'      => $package_id,
				'release_created' => '1',
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	public function handle_create_package_installation() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to create package installations.', 'messenger-plugin-theme' ) );
		}

		$package_id = isset( $_POST['package_id'] ) ? absint( $_POST['package_id'] ) : 0;
		check_admin_referer( 'messenger_create_installation_' . $package_id, 'messenger_create_installation_nonce' );

		$site = isset( $_POST['site_url'] ) ? esc_url_raw( wp_unslash( $_POST['site_url'] ) ) : '';
		if ( ! $package_id || ! filter_var( $site, FILTER_VALIDATE_URL ) ) {
			wp_die( esc_html__( 'Enter a valid site URL and package.', 'messenger-plugin-theme' ) );
		}

		global $wpdb;
		$packages_table = $wpdb->prefix . 'messenger_packages';
		$installs_table = $wpdb->prefix . 'messenger_installs';
		$package_exists = $wpdb->get_var(
			$wpdb->prepare( "SELECT id FROM `$packages_table` WHERE id = %d", $package_id )
		);

		if ( ! $package_exists ) {
			wp_die( esc_html__( 'The selected package does not exist.', 'messenger-plugin-theme' ) );
		}

		$install_key = wp_generate_password( 64, true );
		$inserted = $wpdb->insert(
			$installs_table,
			array(
				'package_id' => $package_id,
				'site' => $site,
				'install_key' => $install_key,
			),
			array( '%d', '%s', '%s' )
		);

		if ( false === $inserted ) {
			wp_die( esc_html__( 'The package installation could not be created.', 'messenger-plugin-theme' ) );
		}

		$redirect_url = add_query_arg(
			array(
				'page'              => 'messenger-admin-options',
				'package_id'        => $package_id,
				'installation_added' => '1',
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	public function handle_download_package_release() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to download package releases.', 'messenger-plugin-theme' ) );
		}

		$release_id = isset( $_GET['release_id'] ) ? absint( $_GET['release_id'] ) : 0;
		check_admin_referer( 'messenger_download_release_' . $release_id );

		global $wpdb;
		$releases_table = $wpdb->prefix . 'messenger_package_releases';
		$release = $wpdb->get_row(
			$wpdb->prepare( "SELECT id, version, file_path FROM `$releases_table` WHERE id = %d", $release_id )
		);

		if ( ! $release ) {
			wp_die( esc_html__( 'The requested release could not be found.', 'messenger-plugin-theme' ) );
		}

		$private_directory = $this->get_private_release_directory();
		if ( is_wp_error( $private_directory ) ) {
			wp_die( esc_html( $private_directory->get_error_message() ) );
		}

		$file_path = realpath( $release->file_path );
		if ( false === $file_path || dirname( $file_path ) !== $private_directory || ! is_file( $file_path ) || ! is_readable( $file_path ) || 'zip' !== strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) ) ) {
			wp_die( esc_html__( 'The release file is unavailable.', 'messenger-plugin-theme' ) );
		}

		nocache_headers();
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( 'package-' . $release->id . '-v' . $release->version . '.zip' ) . '"' );
		header( 'Content-Length: ' . filesize( $file_path ) );
		readfile( $file_path );
		exit;
	}

	private function store_private_release_zip( $file ) {
		if ( ! isset( $file['tmp_name'], $file['name'], $file['size'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'invalid_upload', __( 'The uploaded file is invalid.', 'messenger-plugin-theme' ) );
		}

		if ( 'zip' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
			return new WP_Error( 'invalid_extension', __( 'Only ZIP files can be uploaded.', 'messenger-plugin-theme' ) );
		}

		$file_handle = fopen( $file['tmp_name'], 'rb' );
		if ( false === $file_handle ) {
			return new WP_Error( 'unreadable_upload', __( 'The uploaded file could not be read.', 'messenger-plugin-theme' ) );
		}
		$signature = fread( $file_handle, 4 );
		fclose( $file_handle );
		if ( ! in_array( $signature, array( "PK\x03\x04", "PK\x05\x06", "PK\x07\x08" ), true ) ) {
			return new WP_Error( 'invalid_zip', __( 'The uploaded file is not a valid ZIP archive.', 'messenger-plugin-theme' ) );
		}

		$real_upload_directory = $this->get_private_release_directory( true );
		if ( is_wp_error( $real_upload_directory ) ) {
			return $real_upload_directory;
		}

		@chmod( $real_upload_directory, 0750 );
		$temporary_handle = fopen( $file['tmp_name'], 'rb' );
		if ( false === $temporary_handle ) {
			return new WP_Error( 'unreadable_upload', __( 'The uploaded file could not be read.', 'messenger-plugin-theme' ) );
		}

		$file_path = $real_upload_directory . DIRECTORY_SEPARATOR . wp_generate_uuid4() . '.zip';
		$destination_handle = @fopen( $file_path, 'xb' );
		if ( false === $destination_handle ) {
			fclose( $temporary_handle );
			return new WP_Error( 'file_store_failed', __( 'A unique release file could not be created.', 'messenger-plugin-theme' ) );
		}

		$bytes_copied = stream_copy_to_stream( $temporary_handle, $destination_handle );
		fclose( $temporary_handle );
		fclose( $destination_handle );

		if ( false === $bytes_copied || (int) $file['size'] !== $bytes_copied ) {
			wp_delete_file( $file_path );
			return new WP_Error( 'file_store_failed', __( 'The release file could not be stored completely.', 'messenger-plugin-theme' ) );
		}

		@chmod( $file_path, 0640 );
		return $file_path;
	}

	private function get_private_release_directory( $create = false ) {
		$document_root = isset( $_SERVER['DOCUMENT_ROOT'] ) ? realpath( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) : realpath( ABSPATH );
		if ( false === $document_root ) {
			return new WP_Error( 'private_directory_unavailable', __( 'A private upload directory could not be determined.', 'messenger-plugin-theme' ) );
		}

		$private_parent = dirname( $document_root );
		if ( $private_parent === $document_root ) {
			return new WP_Error( 'private_directory_unavailable', __( 'A private upload directory could not be determined.', 'messenger-plugin-theme' ) );
		}

		$directory = $private_parent . DIRECTORY_SEPARATOR . '.messenger-plugin-theme' . DIRECTORY_SEPARATOR . 'releases';
		if ( $create && ! wp_mkdir_p( $directory ) ) {
			return new WP_Error( 'private_directory_unavailable', __( 'The private upload directory could not be created.', 'messenger-plugin-theme' ) );
		}

		$real_directory = realpath( $directory );
		$document_root_prefix = trailingslashit( $document_root );
		if ( false === $real_directory || $real_directory === $document_root || 0 === strpos( $real_directory, $document_root_prefix ) ) {
			return new WP_Error( 'public_upload_directory', __( 'The upload directory is not outside the web root.', 'messenger-plugin-theme' ) );
		}

		return $real_directory;
	}

	public function DisplayMainAdmin() {
		global $wpdb;

		$packages_table = $wpdb->prefix . 'messenger_packages';
		$installs_table = $wpdb->prefix . 'messenger_installs';
		$releases_table = $wpdb->prefix . 'messenger_package_releases';
		$packages       = $wpdb->get_results(
			"SELECT packages.*, (SELECT COUNT(*) FROM `$installs_table` AS installs WHERE installs.package_id = packages.id) AS installs, (SELECT releases.version FROM `$releases_table` AS releases WHERE releases.package_id = packages.id ORDER BY releases.release_date DESC, releases.id DESC LIMIT 1) AS version FROM `$packages_table` AS packages ORDER BY packages.name ASC"
		);
		$package_id     = isset( $_GET['package_id'] ) ? sanitize_text_field( wp_unslash( $_GET['package_id'] ) ) : null;
		$package        = null;
		$latest_release  = null;
		$package_installs = array();

		if ( null !== $package_id ) {
			$package = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT packages.*, (SELECT releases.version FROM `$releases_table` AS releases WHERE releases.package_id = packages.id ORDER BY releases.release_date DESC, releases.id DESC LIMIT 1) AS version FROM `$packages_table` AS packages WHERE packages.id = %s",
					$package_id
				)
			);

			if ( $package ) {
				$latest_release = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT id, version, release_notes, file_path FROM `$releases_table` WHERE package_id = %d ORDER BY release_date DESC, id DESC LIMIT 1",
						absint( $package_id )
					)
				);
				$package_installs = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT id, package_id, site, install_key FROM `$installs_table` WHERE package_id = %s ORDER BY id ASC",
						$package_id
					)
				);
			}
		}
		?>
		<style>
			.messenger-package-container table {
			border: 1px solid rgba(0, 0, 0, 0.5);
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
		a.btn, button.btn {
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
		a.btn.btn-warn {
			background: red;
		}
		.new-version-form {
			transition: all 0.25s;
			opacity: 0;
			max-height: 0px;
			overflow: hidden;
		}
		</style>
		<div class="wrap">
			<h2>Manage Messenger Plugin and Theme Installations</h2>
			<?php if ( null === $package_id ) { ?>
			<div class="messenger-plugin-theme-admin">
				<h3>Packages</h3>
				<?php if ( isset( $_GET['package_created'] ) && '1' === $_GET['package_created'] ) { ?>
					<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Package created.', 'messenger-plugin-theme' ); ?></p></div>
				<?php } ?>
				<?php if ( isset( $_GET['package_deleted'] ) && '1' === $_GET['package_deleted'] ) { ?>
					<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Package deleted.', 'messenger-plugin-theme' ); ?></p></div>
				<?php } ?>
					<div class="messenger-package-container">
						<table>
							<tr><th>Name</th><th>Slug</th><th>Type</th><th>Version</th><th>Installs</th><th>Actions</th></tr>
						<?php
						foreach ( $packages as $package_item ) { ?>
							<tr><td><?php echo esc_html( $package_item->name ); ?></td><td><?php echo esc_html( $package_item->slug ); ?></td><td><?php echo esc_html( $package_item->type ); ?></td><td><?php echo esc_html( $package_item->version ); ?></td><td><?php echo esc_html( $package_item->installs ); ?></td><td><a href="?page=messenger-admin-options&amp;package_id=<?php echo rawurlencode( $package_item->id ); ?>" class="btn">View</a><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline"><input type="hidden" name="action" value="messenger_delete_package" /><input type="hidden" name="package_id" value="<?php echo esc_attr( $package_item->id ); ?>" /><?php wp_nonce_field( 'messenger_delete_package_' . $package_item->id, 'messenger_delete_package_nonce' ); ?><button type="submit" class="btn btn-warn" onclick="return confirm('<?php echo esc_js( sprintf( __( 'Delete package %s and all associated releases and installations?', 'messenger-plugin-theme' ), $package_item->name ) ); ?>')">Delete</button></form></td></tr>
						<?php
						} ?>
						</table>
					</div>
					<div><button type="button" class="btn" onclick="showNewPackageForm()">Create New Package</button></div>
					<div id="new-package-form-container" class="new-version-form">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="messenger_create_package" />
							<?php wp_nonce_field( 'messenger_create_package', 'messenger_create_package_nonce' ); ?>
							<div><label for="package-name">Name: </label><input type="text" id="package-name" name="package_name" required /></div>
							<div><label for="package-slug">Slug: </label><input type="text" id="package-slug" name="package_slug" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="my-package" required /></div>
							<div><label for="package-type">Type: </label><select id="package-type" name="package_type" required><option value="Theme">Theme</option><option value="Plugin">Plugin</option></select></div>
							<div><button type="submit">Create Package</button></div>
						</form>
					</div>
					<script>function showNewPackageForm() {
						const container = document.getElementById('new-package-form-container');
						container.style.opacity = 1;
						container.style.maxHeight = '500px';
					}</script>
			</div>
			<?php } elseif ( ! $package ) { ?>
				<p><?php esc_html_e( 'Package not found.', 'messenger-plugin-theme' ); ?></p>
			<?php } else { ?>
			<div class="messenger-plugin-theme-admin">
				<?php if ( isset( $_GET['release_created'] ) && '1' === $_GET['release_created'] ) { ?>
					<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Package release published.', 'messenger-plugin-theme' ); ?></p></div>
				<?php } ?>
				<?php if ( isset( $_GET['installation_added'] ) && '1' === $_GET['installation_added'] ) { ?>
					<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Package installation created.', 'messenger-plugin-theme' ); ?></p></div>
				<?php } ?>
				<h3><?php echo esc_html( $package->name ); ?> - <?php echo esc_html( $package->type ); ?></h3>
				<div class="messenger-package-container">
					<p><strong>Slug:</strong> <?php echo esc_html( $package->slug ); ?></p>
					<p><strong>Version: </strong> <?php echo esc_html( $package->version ); ?></p>
					<?php if ( $latest_release ) { ?>
						<div class="release-notes"><?php echo wp_kses_post( wpautop( esc_html( $latest_release->release_notes ) ) ); ?></div>
						<p><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'messenger_download_package_release', 'release_id' => $latest_release->id ), admin_url( 'admin-post.php' ) ), 'messenger_download_release_' . $latest_release->id ) ); ?>" class="btn">Download Zip</a></p>
					<?php } else { ?>
						<p><?php esc_html_e( 'No release has been published yet.', 'messenger-plugin-theme' ); ?></p>
					<?php } ?>
					<p><em><a href="#">Show previous versions...</a></em></p>
				</div>
				<div class="messenger-package-container">
					<p><strong><button onClick="showNewForm()">Publish New Version...</button></strong></p>
					<div id="new-version-form-container" class="new-version-form">
						<form id="new-version-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
							<input type="hidden" name="action" value="messenger_publish_package_release" />
							<input type="hidden" name="package_id" value="<?php echo esc_attr( $package->id ); ?>" />
							<?php wp_nonce_field( 'messenger_publish_release_' . $package->id, 'messenger_publish_release_nonce' ); ?>
							<div><label for="new-version-number">Version Number: </label><input type="text" id="new-version-number" name="release_version" placeholder="1.0.0" pattern="(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(-[0-9A-Za-z.-]+)?(\+[0-9A-Za-z.-]+)?" required /></div>
							<div><label for="new-version-notes">Release Notes: </label><textarea id="new-version-notes" name="release_notes" rows="6"></textarea></div>
							<div><label for="new-version-zip">ZIP File: </label><input type="file" id="new-version-zip" name="release_zip" accept=".zip,application/zip" required /></div>
							<div><button type="submit">Publish</button></div>
						</form>
					</div>
					<script>function showNewForm() {
						const container = document.getElementById('new-version-form-container');
						container.style.opacity = 1;
						container.style.maxHeight = '500px';
					}</script>
				</div>
				<div class="messenger-package-container">
					<p><strong>Package API Endpoint:</strong> <a href="<?php echo esc_url( rest_url( 'messenger-plugin-theme/v1/package/' . $package->slug ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( rest_url( 'messenger-plugin-theme/v1/package/' . $package->slug ) ); ?></a></p>
					<h4>Installations <button type="button" class="btn" onclick="showNewInstallationForm()">Create New Installation</button></h4>
					<div id="new-installation-form-container" class="new-version-form">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="messenger_create_package_installation" />
							<input type="hidden" name="package_id" value="<?php echo esc_attr( $package->id ); ?>" />
							<?php wp_nonce_field( 'messenger_create_installation_' . $package->id, 'messenger_create_installation_nonce' ); ?>
							<div><label for="installation-site-url">Site URL: </label><input type="url" id="installation-site-url" name="site_url" placeholder="https://example.com" required /></div>
							<div><button type="submit">Create Installation</button></div>
						</form>
					</div>
					<script>function showNewInstallationForm() {
						const container = document.getElementById('new-installation-form-container');
						container.style.opacity = 1;
						container.style.maxHeight = '500px';
					}</script>
				</div>
				<div class="messenger-package-container">
					<table>
						<tr><th>Site</th><th>Key</th><th>Actions</th></tr>
						<?php if ( empty( $package_installs ) ) { ?>
							<tr><td colspan="3">No package installations currently exist</td></tr>
						<?php } else { ?>
							<?php foreach ( $package_installs as $install ) { ?>
								<tr><td><?php echo esc_html( $install->site ); ?></td><td><?php echo esc_html( $install->install_key ); ?></td><td><a href="#" class="btn">Edit</a><a href="#" class="btn btn-warn">Delete</a></td></tr>
							<?php } ?>
						<?php } ?>
					</table>
				</div>
			</div>

			<?php }  ?>
		</div>
		<?php
	}

}
