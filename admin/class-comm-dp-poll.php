<?php

namespace COMMDP\Admin;

class Poll {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @param  string    $plugin_name  The name of this plugin.
	 * @param  string    $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

    /**
     * Register poll post type
     * Hooked via action init, priority 999
     * @return  void
     */
    public function register_post_type() {
		$labels = [
    		'name'               => _x( 'Polls', 'post type general name', 'commdp' ),
    		'singular_name'      => _x( 'Poll', 'post type singular name', 'commdp' ),
    		'menu_name'          => _x( 'Polls', 'admin menu', 'commdp' ),
    		'name_admin_bar'     => _x( 'Poll', 'add new on admin bar', 'commdp' ),
    		'add_new'            => _x( 'Add New', 'poll', 'commdp' ),
    		'add_new_item'       => __( 'Add New Poll', 'commdp' ),
    		'new_item'           => __( 'New Poll', 'commdp' ),
    		'edit_item'          => __( 'Edit Poll', 'commdp' ),
    		'view_item'          => __( 'View Poll', 'commdp' ),
    		'all_items'          => __( 'All Polls', 'commdp' ),
    		'search_items'       => __( 'Search Polls', 'commdp' ),
    		'parent_item_colon'  => __( 'Parent Polls:', 'commdp' ),
    		'not_found'          => __( 'No polls found.', 'commdp' ),
    		'not_found_in_trash' => __( 'No polls found in Trash.', 'commdp' )
    	];

    	$args = [
    		'labels'             => $labels,
    		'description'        => __( 'Description.', 'commdp' ),
    		'public'             => true,
    		'publicly_queryable' => true,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
    		'rewrite'            => array( 'slug' => 'commdp-poll' ),
    		'capability_type'    => 'post',
    		'has_archive'        => true,
    		'hierarchical'       => false,
    		'menu_position'      => null,
    		'supports'           => array( 'title', 'thumbnail' )
    	];

    	register_post_type( 'commdp-poll', $args );
    }
}
