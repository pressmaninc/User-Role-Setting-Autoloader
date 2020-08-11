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
	/**
	 * Holds the values to be used in the fields callbacks.
	 */
	private $options;

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

		wp_enqueue_script( 'axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', array(), '', true );
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
				'', // menu_slug
				array( $this, 'display' ) // callback
			);
		}
	}

	/**
	 * Display page.
	 */
	public function display() {
		$this->options = get_option( 'usa_import_dir_path', USA_IMPORT_DIR_PATH );
?>
		<div class="user-role-setting-autoloader-wrap">
			<!-- title -->
			<h1 style="margin-bottom: 30px;">User Role Setting Autoloader</h1>

			<!-- Import Directory Path Form -->
			<form style="margin-bottom: 30px;" method="post" action="options.php">
				<?php
				settings_fields( 'usa_group' );
				do_settings_sections( 'usa_import_dir_path_section' );
				submit_button();
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
			'<h2>' . __( "Import Directory Path", "user-role-setting-autoloader" ) . '</h2>',
			array( $this, 'print_usa_import_dir_path_info' ),
			'usa_import_dir_path_section'
		);

		add_settings_field(
			'usa-setting-import-dir-field-id',
			'',
			array( $this, 'print_usa_import_dir_path_field' ),
			'usa_import_dir_path_section',
			'usa-setting-import-dir-section-id',
			array(
				'class' => 'usa-import-dir-path-field'
			)
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
		print "<p>$info</p>";
	}

	/**
	 * Print usa dir path field.
	 */
	public function print_usa_import_dir_path_field() {
		printf(
			'<input type="text" id="usa-setting-import-dir-field-id" name="usa_import_dir_path" value="%s" />',
			esc_attr( $this->options )
		);
	}

	/**
	 * Sanitize each setting field as needed.
	 */
	public function sanitize( $input )
	{
		$new_input = array();
		if( $input )
			$new_input = sanitize_text_field( $input );

		return $new_input;
	}

}

new USA_Option();
