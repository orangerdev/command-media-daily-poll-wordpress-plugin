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
     * Cookie key name
     * @var string
     */
    protected $cookie_key_name = 'comm-dp';

    /**
     * Cookie data
     * @var mixed
     */
    protected $cookie_data = false;

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
     * Get current cookie data
     * @return void
     */
    protected function check_cookie_data() {

        $answer_data       = \Delight\Cookie\Cookie::get($this->cookie_key_name);
        $answer_data       = maybe_unserialize(stripslashes($answer_data));
        $this->cookie_data = $answer_data;

    }

    /**
     * Update cookie data
     * @param  integer $poll_id     Given poll ID
     * @param  string  $answer      User answer
     * @return void
     */
    protected function update_cookie_answer($poll_id,$answer) {

        if(!is_array($this->cookie_data)) :
            $this->cookie_data = array();
        endif;

        $this->cookie_data[$poll_id] = $answer;

        $cookie = new \Delight\Cookie\Cookie($this->cookie_key_name);
        $cookie->setValue(serialize($this->cookie_data));
        $cookie->setMaxAge(YEAR_IN_SECONDS);
        $cookie->setPath('/');
        $cookie->save();
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

        $this->check_cookie_data();

        if(wp_verify_nonce($post_data['commdp-nonce'],'commdp-submit-answer')) :

            $answer  = sanitize_text_field($post_data['answer']);
            $poll_id = intval($post_data['poll_id']);
            $poll    = get_post($poll_id);

            if(isset($this->cookie_data[$poll_id])) :
                $this->is_submit_valid = false;
                $this->messages[]      = __('You already voted for this poll','comm-dp');
            endif;

            if(!is_a($poll,'WP_Post')) :
                $this->is_submit_valid = false;
                $this->messages[]      = __('Wrong poll data','comm-dp');
            endif;

            if(!in_array($answer,['a','b'])) :
                $this->is_submit_valid = false;
                $this->messages[]      = __('Invalid answer','comm-dp');
            endif;

            if(false !== $this->is_submit_valid) :
                $answers = get_post_meta($poll_id,'poll_answers',true);
                $answers = wp_parse_args($answers,[
                    'a' => 0,
                    'b' => 0
                ]);

                $answers[$answer]++;

                update_post_meta($poll_id,'poll_answers',$answers);

                $this->update_cookie_answer($poll_id,$answer);
                $this->messages[] = __('Vote success','comm-dp');

            endif;
        else :
            $this->is_submit_valid = false;
            $this->messages[]      = __('Something wrong with the process','comm-dp');
        endif;
    }
}
