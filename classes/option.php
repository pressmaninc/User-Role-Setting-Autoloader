<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class USA_Option
 *
 * Class to display the setting screen for User Role Setting Autoloader.
 */
class USA_Option {
	public function __construct() {
		load_theme_textdomain( 'user-role-setting-autoloader', plugin_dir_path( plugin_dir_path( __FILE__ ) ) . 'lang' );
		add_action( 'admin_enqueue_scripts', array( $this, 'my_enqueue' ) );
		add_action( 'admin_menu', array( $this, 'usa_plugin_menu' ) );
		add_action( 'admin_init', array( $this, 'usa_setting' ) );
	}

	/**
	 * Load css and js.
	 */
	public function my_enqueue() {
		$handle = 'user-role-setting-autoloader';
		wp_enqueue_style( $handle, plugin_dir_url( __FILE__ ) . '../assets/css/user-role-setting-autoloader.css', array(), '1.0' );

		wp_enqueue_script( $handle, plugin_dir_url( __FILE__ ) . '../assets/js/user-role-setting-autoloader.js', array('jquery'), '1.0', true );
		wp_localize_script( $handle, 'USA_CONFIG', [
			'api'    => admin_url( 'admin-ajax.php' ),
			'action' => 'export-all-roles-capabilities',
			'nonce' => wp_create_nonce( 'export-all-roles-capabilities' ),
		]);
	}

	/**
	 * Add submenu page.
	 */
	public function usa_plugin_menu() {
		if ( function_exists( 'add_submenu_page' ) ) {
			add_submenu_page(
				'users.php', // parent_slug
				'User Role Setting Autoloader', // page_title
				'User Role Setting Autoloader', // menu_title
				'manage_options', // capability
				'user-role-setting-autoloader', // menu_slug
				array( $this, 'display' ) // callback
			);
		}
	}

	/**
	 * Display page.
	 */
	public function display() {
?>
		<div class="user-role-setting-autoloader-wrap">
			<!-- title -->
			<h1 style="margin-bottom: 30px;">User Role Setting Autoloader</h1>

			<!-- Import Directory Path Form -->
			<form style="margin-bottom: 30px;" method="post" action="options.php">
				<?php
				settings_fields( 'usa_group' );
				do_settings_sections( 'usa_import_dir_path_section' );
				?>
			</form>

			<!-- Export -->
			<div>
				<h2><?php echo __( "Export", "user-role-setting-autoloader" ); ?></h2>
				<p><?php echo __("Export all the user's roles and capabilities as a json file.", "user-role-setting-autoloader" ); ?></p>
				<p class="submit">
					<button id="usa-export-btn" class="button button-primary"><?php echo __( "Export", "user-role-setting-autoloader" ); ?></button>
				</p>
			</div>
		</div>
<?php
	}

	/**
	 * Register and setting.
	 */
	public function usa_setting() {
		add_settings_section(
			'usa-setting-import-dir-section-id',
			'<h2>' . __( "Import", "user-role-setting-autoloader" ) . '</h2>',
			array( $this, 'print_usa_import_dir_path_info' ),
			'usa_import_dir_path_section'
		);

		register_setting(
			'usa_group',
			'usa_import_dir_path',
			array( $this, 'sanitize' ) // Sanitize
		);
	}

	/**
	 * Print usa import dir path info.
	 */
	public function print_usa_import_dir_path_info() {
		$info = __( "When the exported file is placed in the directory,all roles and capabilities is automatically imported.", "user-role-setting-autoloader" );
		$info.= '<br>';
		$info.= __( "This directory can be set up to be overwritten using a hook called 'usa-import-dir'.", "user-role-setting-autoloader" );
		$path = USA_IMPORT_DIR_PATH;
		$path = apply_filters( 'usa-import-dir', $path );
		print "<p>$info</p>";
		print "<p>" . wp_strip_all_tags( $path ) . "</p>";
	}
}

new USA_Option();
