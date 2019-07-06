<?php

namespace COMMDP\Front;

class PollWidget extends \WP_Widget {

    /**
     * Check if poll exists
     * @var bool|WP_Post
     */
    protected $poll = false;

    /**
     * Cookie key name
     * @var string
     */
    protected $cookie_key_name = 'comm-dp';

    /**
     * Cookie data
     * @var mixed
     */
    protected $cookie_data = [];

    /**
     * Set if current active poll is already answered or no
     * @var bool
     */
    protected $is_poll_answered = false;

    /**
     * User poll answer
     * @var string
     */
    protected $poll_answer = '';

    /**
     * All poll answers dta
     * @var array
     */
    protected $all_poll_answer = [
        'a' => 0,
        'b' => 0
    ];

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {

        $widget_ops = [
            'classname'     => 'comm_daily_poll',
            'description'   => __('Display daily poll', 'comm-dp')
        ];

        parent::__construct(
            'comm_daily_poll',
            'CM - Daily Poll',
            $widget_ops
        );

    }

    /**
     * Check if poll exists by current date
     * @return void
     */
    protected function check_poll() {

        $poll = wp_cache_get('poll','commdp');

        if(false === $poll) :
            $posts = get_posts([
                'numberpost' => 1,
                'post_type'  => 'cst_poll',
                'meta_key'   => '_commdp_date_active',
                'meta_value' => current_time('Y-m-d')
            ]);

            if(isset($posts[0])) :
                $this->poll = $posts[0];
                wp_cache_set('poll', $this->poll, 'commdp', HOUR_IN_SECONDS);
            endif;
        else :
            $this->poll = $poll;
        endif;
    }

    /**
     * Check if current visitor already answered
     * @return void
     */
    protected function check_answer() {
        $answer_data       = \Delight\Cookie\Cookie::get($this->cookie_key_name);
        $answer_data       = maybe_unserialize(stripslashes($answer_data));
        $this->cookie_data = $answer_data;

        if(is_a($this->poll,'WP_Post') && isset($this->cookie_data[$this->poll->ID])) :

            $this->is_poll_answered = true;
            $this->poll_answer      = $this->cookie_data[$this->poll->ID];
            $answers                = get_post_meta($this->poll->ID,'poll_answers',true);
            $this->all_poll_answer  = wp_parse_args($answers,$this->all_poll_answer);

        endif;
    }

    /**
     * Display poll data
     * @return void
     */
    protected function display_poll() {

        if(is_a($this->poll, 'WP_Post')) :

            ?>
            <p><?php echo $this->poll->post_title; ?></p>
            <?php

            $this->display_answer();
        else :
            ?><p><?php _e('No active poll today.' ,'comm-dp'); ?></p><?php
        endif;
    }

    /**
     * Display poll answer
     * @return void
     */
    protected function display_answer() {

        ?><div class="comm-dp-poll-anwser"><?php

        // Display all answers
        if(false !== $this->is_poll_answered) :
        ?>
        <form class="" action="<?php echo home_url('commdp/submit-poll'); ?>" method="post">
            <label name='answer-1'>
                <span><?php echo carbon_get_post_meta($this->poll->ID, 'commdp_answer_1'); ?></span>
                <?php printf(__('- %s vote(s)','comm-db'),$this->all_poll_answer['a']); ?>
                <?php if('a' === $this->poll_answer) : ?>
                <?php _e('( You voted )','comm-db'); ?>
                <?php endif; ?>
            </label>

            <label name='answer-2'>
                <span><?php echo carbon_get_post_meta($this->poll->ID, 'commdp_answer_2'); ?></span>
                <?php printf(__('- %s vote(s)','comm-db'),$this->all_poll_answer['b']); ?>
                <?php if('b' === $this->poll_answer) : ?>
                <?php _e('You voted','comm-db'); ?>
                <?php endif; ?>
            </label>
        </form>
        <?php

        // Dislay answer form when current visitor hasn't answerd
        else :
        ?>
            <form class="" action="<?php echo home_url('commdp/submit-poll'); ?>" method="post">
                <label name='answer-1'>
                    <input type='radio' name='answer' value='a' />
                    <span><?php echo carbon_get_post_meta($this->poll->ID, 'commdp_answer_1'); ?></span>
                </label>

                <label name='answer-2'>
                    <input type='radio' name='answer' value='b' />
                    <span><?php echo carbon_get_post_meta($this->poll->ID, 'commdp_answer_2'); ?></span>
                </label>
                <input type="hidden" name="poll_id" value="<?php echo $this->poll->ID; ?>">
                <?php wp_nonce_field('commdp-submit-answer', 'commdp-nonce'); ?>
                <button type="submit" name="button">Vote</button>
            </form>
        <?php
        endif;

        ?></div><?php
    }

    /**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )  :
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		endif;

        $this->check_poll();
        $this->check_answer();
        $this->display_poll();

		echo $args['after_widget'];
	}

    /**
     * Back-end widget form
     * @param  array $instance Previously saved values from database
     * @return void
     */
    public function form($instance) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'comm-dp' );
		?>
		<p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'comm-dp' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
    }

    /**
     * Sanitize widget from values when are saved
     * @param  array  $new_instance Values just sent to saved
     * @param  array  $old_instance Previously saved values from database
     * @return array  Updated safe values to be saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
    }
}
