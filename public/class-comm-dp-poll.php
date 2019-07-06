<?php

namespace COMMDP\Front;

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
     * Check if submit process valid
     * @var bool
     */
    protected $is_submit_valid = true;

    /**
     * Set messages when form submitted
     * @var array
     */
    protected $messages = array();

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
     * Submit answer
     * Hooked via action commd/submit-poll, priority 999l
     * @return void
     */
    public function submit_answer() {

        $post_data = wp_parse_args($_POST,[
            'answer'       => NULL,
            'poll_id'      => NULL,
            'commdp-nonce' => NULL
        ]);

        if(wp_verify_nonce($post_data['commdp-nonce'],'commdp-submit-answer')) :

            $poll_id = intval($post_data['poll_id']);
            $answer  = intval($post_data['answer']);
            $poll    = get_post($poll_id);

            if(!is_a($poll,'WP_Post')) :
                $this->is_submit_valid = false;
                $this->messages[]      = __('Wrong poll data','comm-dp');
            endif;

            if(1 > $answer || 2 < $answer) :
                $this->is_submit_valid = false;
                $this->messages[]      = __('Invalid answer','comm-dp');
            endif;
        else :
            $this->is_submit_valid = false;
            $this->messages[]      = __('Something wrong with the process','comm-dp');
        endif;
    }
}
