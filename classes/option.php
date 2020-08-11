<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class USA_Option
 *
 * Class to display the setting screen for User Setting Autoloader.
 */
class USA_Option {
	/**
	 * Holds the values to be used in the fields callbacks.
	 */
	private $options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'usa_plugin_menu' ) );
		add_action( 'admin_init', array( $this, 'usa_setting' ) );
	}

	/**
	 * Add submenu page.
	 */
	public function usa_plugin_menu() {
		if ( function_exists( 'add_submenu_page' ) ) {
			add_submenu_page(
				'users.php', // parent_slug
				'User Setting Autoloader', // page_title
				'User Setting Autoloader', // menu_title
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
		$this->options = get_option( 'usa_import_dir_path' );
?>
		<style>
			.usa_import_dir_path_field th {
				display: none;
			}
			.usa_import_dir_path_field td {
				padding-top: 0px;
				padding-left: 0px;
			}
			.usa_import_dir_path_field td input {
				width: 100%;
				max-width: 850px;
			}
		</style>
		<div class="user-setting-autoloader-wrap">
			<!-- title -->
			<h1 style="margin-bottom: 30px;">User Setting Autoloader</h1>

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
				<h2><?php echo __( "Export", "user-setting-autoloader" ); ?></h2>
				<p><?php echo __("Export all the user's roles/capabilities as a json file", "user-setting-autoloader" ); ?></p>
				<p class="submit">
					<button id="usa-export-btn" class="button button-primary">Export</button>
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
			'<h2>' . __( "Import Directory Path", "user-setting-autoloader" ) . '</h2>',
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
				'class' => 'usa_import_dir_path_field'
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
		$info = __( "When the exported file is placed in the directory, all role information is automatically imported.", "user-setting-autoloader" );
		print "<p>$info</p>";
	}

	/**
	 * Print usa dir path field.
	 */
	public function print_usa_import_dir_path_field() {
		$default_import_dir_path = plugin_dir_path( plugin_dir_path( __FILE__ ) ) . 'import';
		printf(
			'<input type="text" id="usa-setting-import-dir-field-id" name="usa_import_dir_path" value="%s" />',
			$this->options ? esc_attr( $this->options ) : $default_import_dir_path
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
