<?php

namespace COMMDP;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Comm_Dp
 * @subpackage Comm_Dp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Comm_Dp
 * @subpackage Comm_Dp/public
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Front {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/comm-dp-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/comm-dp-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
     * Register rewrite url
     * Hooked via action init, priority 1
     * @return void
     */
    public function register_rewrite_url()
    {
		add_rewrite_tag ('%commdp-action%', '([^/]*)');
        add_rewrite_rule('^commdp/([^/]*)/?',
               'index.php?script=commdp&commdp-action=$matches[1]',
               'top'
           );
    }

	/**
     * Register custom query vars
     * @param  array $vars
     * @return array
     */
    public function register_query_vars($vars)
    {
        $vars[] = 'script';
        $vars[] = 'commdp-action';


        return $vars;
    }

    /**
     * Check rest url API
     * Hooked via template_redirect, priority 1
     * @return void
     */
    public function check_request_url()
    {
        global $wp_query;

        if(isset($wp_query->query['script']) && 'commdp' === $wp_query->query['script']) :

            $action     = $wp_query->query['commdp-action'];
            do_action('commdp/'.$action);
            exit;
        endif;
    }

	/**
	 * Register widgets
	 * Hooked via action widgets_init, priority 999
	 * @return void
	 */
	public function register_widgets() {
		register_widget( '\COMMDP\Front\PollWidget' );
	}

}
