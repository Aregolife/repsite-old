<?php
class Repsite_Widget extends WP_Widget {
	// Main constructor
	public function __construct() {
		parent::__construct(
			'repsite_widget',
			__( 'Replicated Dist Info', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}
	// The widget form (for the backend )
	public function form( $instance ) {
		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'guestOf'     => 'You are a guest of: ',
			'email' => 'Email: ',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

<?php // Widget Title ?>
<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
        <?php _e( 'Widget Title', 'text_domain' ); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<?php // GuestOf Field ?>
<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'guestOf' ) ); ?>">
        <?php _e( 'Guest Of Text:', 'text_domain' ); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'guestOf' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'guestOf' ) ); ?>" type="text" value="<?php echo esc_attr( $guestOf ); ?>" />
</p>

<?php // Textarea Field ?>
<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>">
        <?php _e( 'Email Text:', 'text_domain' ); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>" />
</p>


<?php }
	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['guestOf']     = isset( $new_instance['guestOf'] ) ? wp_strip_all_tags( $new_instance['guestOf'] ) : '';
		$instance['email'] = isset( $new_instance['email'] ) ? wp_strip_all_tags( $new_instance['email'] ) : '';

		return $instance;
	}
	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );
		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$guestOf     = isset( $instance['guestOf'] ) ? $instance['guestOf'] : '';
		$email = isset( $instance['email'] ) ?$instance['email'] : '';

		// WordPress core before_widget hook (always include )
		echo $before_widget;
		// Display the widget
        
        echo '<div class="widget-text wp_widget_plugin_box">';
			// Display widget title if defined
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
        
        echo '<div class="repsite"><div class="repsite-photo"></div>' .
            '<div class="repsite-info"><div class="repsite-guest-of">';
        
        if ( $guestOf ) {
         echo '<span class="repsite-guestOf" data-text="' . $guestOf . '"></span> ';
        }
        
        echo '<span class="repsite-name"></span></div>';
        
        if ($email){
            echo '<div class="repsite-contact-info"><span class="repsite-email" data-text="' . $email . '"> <a></a></span></div>';
        }

		echo '</div></div></div>';
        
		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}
}
