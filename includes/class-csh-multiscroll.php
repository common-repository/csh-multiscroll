<?php
class Csh_Multiscroll {
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * @var      class    $public_instance    instance of public Class.
	 */
	protected $public_instance;

	/**
	 * @var      class    $admin_instance    instance of admin Class.
	 */
	protected $admin_instance;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		/* Set plugin information */
		if ( defined( 'CSHMS_PLUGIN_VERSION' ) ) {
			$this->version = CSHMS_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'csh-multiscroll';
		
		// Load assets for plugin.
		$this->load_dependencies();

		$this->do_public();
		$this->do_admin();
		//$this->build_setting_fields();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Csh_Multiscroll_Loader. Orchestrates the hooks of the plugin.
	 * - Csh_Multiscroll_i18n. Defines internationalization functionality.
	 * - Csh_Multiscroll_Admin. Defines all hooks for the admin area.
	 * - Csh_Multiscroll_Public. Defines all hooks for the public side of the site.
	 *
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once CSHMS_PLUGIN_PUBLIC_DIR . 'class-csh-multiscroll-public.php';
		$this->public_instance = new Csh_Multiscroll_Public( $this->get_plugin_name(), $this->get_version() );

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once CSHMS_PLUGIN_ADMIN_DIR . 'class-csh-multiscroll-admin.php';
		$this->admin_instance = new Csh_Multiscroll_Admin( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Front end handle.
	 */
	public function do_public() {

	}

	/**
	 * Build admin setting page.
	 */
	public function do_admin() {
		 $admin_page = $this->admin_instance;
		 $admin_page->register_postypes();
		 $admin_page->add_meta_box();
	}

	/**
	 * add fields to page.
	 */
	public function build_setting_fields(){
//		$admin_setting = $this->admin_instance;
//		//Add Sections.
//		$admin_setting->add_section('cshms-tab1', 'Main Setting');
//		//Add Fields
//		$admin_setting->add_field_of_section('cshms-tab1', 'tab1_checkbox', 'Enable', 'checkbox', array(
//		    'desc' => 'Check if you want to enable notice in frontend.'
//		));
	}

}
$plugin = new Csh_Multiscroll; // Control public and admin class.





