<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_fb_facepile_widget' );

/**
 * Register our widget.
 * 'FB_Facepile_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_fb_facepile_widget() {
	register_widget( 'FB_Facepile_Widget' );
}

/**
 * Facebook Login with Faces class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class FB_Facepile_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function FB_Facepile_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'fb-facepile', 'description' => __('Facebook facepile widget.','wpfb') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'fb-facepile' );

		/* Create the widget. */
		$this->WP_Widget( 'fb-facepile', __('Facebook Facepile','wpfb'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$width = $instance['width'];
		$num_rows = $instance['num_rows'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		
		$button_wrap_open = '<div class="button_wrap">';
				
		$button = "<fb:facepile ";

		/* If width was selected, add width to widget. */
		if ( $width )
			 $button .= 'width=' . $width . ' ';

		/* Show max rows value. */
		if ( $num_rows )
			 $button .= 'num-rows="' . $num_rows . '" ';
			 
		$button .= "></fb:facepile>";
		
		$button_wrap_close = '</div>';
		
		echo $button_wrap_open . $button . $button_wrap_close;

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
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['num_rows'] = strip_tags( $new_instance['num_rows'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_layout_style() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Your Facebook Friends on','wpfb') . " " . get_bloginfo('name'), 'width' => '200', 'num_rows' => '1' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title (leave blank for none):','wpfb'); ?></label>
			<input value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" /> 
		</p>

		<!-- Number of Rows -->
		<p>
			<label for="<?php echo $this->get_field_id( 'num_rows' ); ?>"><?php _e('Max Rows:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'num_rows' ); ?>" name="<?php echo $this->get_field_name( 'num_rows' ); ?>" value="<?php echo $instance['num_rows']; ?>" /> 
		</p>


	<?php
	}
}

function show_fb_facepile($atts){

	extract(shortcode_atts(array(
		'width' => '200',
		'num-rows' => '3'), $atts));

	return "<fb:facepile width='{$width}' num-rows='{$numposts}'></fb:facepile>";
}

add_shortcode('fb-facepile', 'show_fb_facepile');

?>