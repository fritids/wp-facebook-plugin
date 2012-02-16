<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_fb_livestream_widget' );

/**
 * Register our widget.
 * 'FB_Comments_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_fb_livestream_widget() {
	register_widget( 'FB_Live_Stream_Widget' );
}

/**
 * Facebook Comments class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class FB_Live_Stream_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function FB_Live_Stream_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'fb-livestream', 'description' => __('A widget that displays a Facebook live stream.','wpfb') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 400, 'height' => 500, 'id_base' => 'fb-livestream', 'xid' => '' );

		/* Create the widget. */
		$this->WP_Widget( 'fb-livestream', __('Facebook Live Stream','wpfb'), $widget_ops, $control_ops );
	}
			
	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		
		$app_id = get_option(FBOAUTH_APP_ID);
		$height = $instance['height'];
		$width = $instance['width'];
		$xid = $instance['xid'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		
		$livestream_open = '<div class="livestream_wrap">';

		$fbml = "<fb:live-stream ";

		/* Add App ID. */
		$fbml .= 'event_app_id="' . $app_id . '" ';

		/* If height was selected, add height to widget. */
		if ( $height )
			 $fbml .= 'height="' . $height . '" ';

		/* If width was selected, add width to widget. */
		if ( $width )
			 $fbml .= 'width="' . $width . '" ';
			 
		/* If xid was selected, add xid to widget. */
		if ( $xid )
			 $fbml .= 'xid="' . $xid . '" ';
			 
		$fbml .= "/>";
		
		$livestream_close = '</div>';
		
		echo $livestream_open . $fbml . $livestream_close;

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		/* Strip tags for title and layout_style to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['xid'] = strip_tags( $new_instance['xid'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_layout_style() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Facebook Live Stream','wpfb'), 'width' => '400', 'height' => '500', 'xid' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title (leave blank for none):','wpfb'); ?></label>
			<input value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" />
		</p>

		<!-- Height -->
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" /> 
		</p>

		<!-- Width -->
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" /> 
		</p>

		<!-- XID -->
		<p>
			<label for="<?php echo $this->get_field_id( 'xid' ); ?>"><?php _e('XID (unique id for multiple live streams on one page):','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'xid' ); ?>" name="<?php echo $this->get_field_name( 'xid' ); ?>" value="<?php echo $instance['xid']; ?>" /> 
		</p>

	<?php
	}
}

/**
 * Shortcode for live stream widget.
 */
 
function show_fb_livestream($atts) {
	
	extract(shortcode_atts(array(
		'app_id' => get_option(FBOAUTH_APP_ID),
		'height' => '500',
		'width' => '400',
		'xid' => ''), $atts));
	
	return "<fb:live-stream event_app_id='{$app_id}' height='{$height}' width='{$width}' xid='{$xid}'></fb-live-stream>";
	
}

add_shortcode('fb-livestream', 'show_fb_livestream');

?>