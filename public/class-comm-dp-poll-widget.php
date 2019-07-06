<?php

namespace COMMDP\Front;

class PollWidget extends \WP_Widget {

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
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		echo esc_html__( 'Hello, World!', 'text_domain' );
		echo $args['after_widget'];
	}

    /**
     * Back-end widget form
     * @param  array $instance Previously saved values from database
     * @return void
     */
    public function form($instance) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
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
