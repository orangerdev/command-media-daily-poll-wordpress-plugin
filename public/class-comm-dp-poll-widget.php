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
     * Set if current active poll is already answered or no
     * @var bool
     */
    protected $is_poll_answered = false;

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

        $posts = get_posts([
            'numberpost' => 1,
            'post_type'  => 'commdp-poll',
            'meta_key'   => '_commdp_date_active',
            'meta_value' => current_time('Y-m-d')
        ]);

        if(isset($posts[0])) :
            $this->poll = $posts[0];
        endif;
    }

    /**
     * Check if current visitor already answered
     * @return void
     */
    protected function check_answer() {

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

        // Display all answer
        if(false !== $this->is_poll_answered) :
        else :
        ?>
            <form class="" action="<?php echo home_url('commdp/submit-poll'); ?>" method="post">
                <label name='answer-1'>
                    <input type='radio' name='answer' value=1 />
                    <span><?php echo carbon_get_post_meta($this->poll->ID, 'commdp_answer_1'); ?></span>
                </label>

                <label name='answer-2'>
                    <input type='radio' name='answer' value=2 />
                    <span><?php echo carbon_get_post_meta($this->poll->ID, 'commdp_answer_2'); ?></span>
                </label>
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
