<?php

namespace COMMDP\Admin;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

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
    		'public'             => false,
    		'publicly_queryable' => false,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
			'exclude_from_search'=> true,
    		'rewrite'            => array( 'slug' => 'commdp-poll' ),
    		'capability_type'    => 'post',
    		'has_archive'        => true,
    		'hierarchical'       => false,
    		'menu_position'      => null,
    		'supports'           => array( 'title' )
    	];

    	register_post_type( 'commdp-poll', $args );
    }

	/**
	 * Register poll's metabox fields
	 * Hooked via action carbon_fields_register_fields, priority 999
	 * @return void
	 */
	public function register_fields() {
		Container::make('post_meta',__('Poll Setup','comm-dp'))
			->add_fields([
				Field::make('separator', 'commdp_display', 		__('Display', 'comm-dp')),
				Field::make('date',		 'commdp_date_active',	__('Active Date', 'comm-dp'))
					->set_storage_format('Y-m-d')
					->set_required(true),
				Field::make('separator', 'commdp_answers', 		__('Answers', 'comm-dp')),
				Field::make('text',		 'commdp_answer_1',		__('Answer #1', 'comm-dp'))
					->set_required(true),
				Field::make('text',		 'commdp_answer_2',		__('Answer #2', 'comm-dp'))
					->set_required(true),
			]);
	}

	/**
     * Set custom column into poll table
     * Hooked via filter manage_commdp-poll_posts_columns, priority 999
     * @param   array   columns
     * @return  array
     */
    public function set_table_columns(array $columns) {
		$columns = [
			'cb'			=> '<input type="checkbox">',
			'title'			=> __('Title', 'snkpo'),
			'active_date'   => __('Active Date', 'snkpo'),
        	'answer'		=> __('Answer', 'snkpo'),
		];

        return $columns;
    }

    /**
     * Display the poll data in table
     * Hooked via action manage_commdp-poll_posts_custom_column, priority 999
     * @param  array  $column
     * @param  int    $post_id
     * @return void
     */
    public function display_data_in_table(string $column, int $post_id) {

		switch($column) :
			case 'active_date' :
				$active_date = date('d F Y',strtotime(carbon_get_post_meta($post_id, 'commdp_date_active')));
				echo $active_date;
				break;

			case 'answer' :
				$answer_data = get_post_meta($post_id, 'poll_answers', true);
				$answer_data = wp_parse_args($answer_data, [
					'a' => 0,
					'b' => 0
				]);

				printf(__('%s/%s','comm-db'),$answer_data['a'],$answer_data['b']);
				break;
		endswitch;
    }
}
