<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Comm_Dp
 * @subpackage Comm_Dp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Comm_Dp
 * @subpackage Comm_Dp/includes
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Comm_Dp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Comm_Dp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'COMM_DP_VERSION' ) ) {
			$this->version = COMM_DP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'comm-dp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Comm_Dp_Loader. Orchestrates the hooks of the plugin.
	 * - Comm_Dp_i18n. Defines internationalization functionality.
	 * - Comm_Dp_Admin. Defines all hooks for the admin area.
	 * - Comm_Dp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-comm-dp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-comm-dp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-comm-dp-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-comm-dp-poll.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-comm-dp-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-comm-dp-poll.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-comm-dp-poll-widget.php';

		$this->loader = new Comm_Dp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Comm_Dp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Comm_Dp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new COMMDP\Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'after_setup_theme',		$plugin_admin, 'load_carbon_fields', 999);

		$poll 	= new COMMDP\Admin\Poll( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init',										$poll, 'register_post_type', 	999);
		$this->loader->add_action( 'carbon_fields_register_fields',				$poll, 'register_fields',	 	999);
		$this->loader->add_filter( 'manage_commdp-poll_posts_columns',			$poll, 'set_table_columns',	 	999);
		$this->loader->add_action( 'manage_commdp-poll_posts_custom_column', 	$poll, 'display_data_in_table', 999, 2);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new COMMDP\Front( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', 	$plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', 	$plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init',					$plugin_public, 'register_rewrite_url', 1);
		$this->loader->add_filter( 'query_vars',			$plugin_public, 'register_query_vars',	999);
		$this->loader->add_action( 'template_redirect',		$plugin_public, 'check_request_url', 	1);
		$this->loader->add_action( 'widgets_init',			$plugin_public, 'register_widgets', 	100);

		$poll = new COMMDP\Front\Poll ( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'commdp/submit-poll',	$poll, 'submit_answer', 999);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Comm_Dp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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

}
