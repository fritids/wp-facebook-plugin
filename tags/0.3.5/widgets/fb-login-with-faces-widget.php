<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_fb_login_with_faces_widget' );

/**
 * Register our widget.
 * 'FB_Login_With_Faces_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_fb_login_with_faces_widget() {
	register_widget( 'FB_Login_With_Faces_Widget' );
}

/**
 * Facebook Login with Faces class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class FB_Login_With_Faces_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function FB_Login_With_Faces_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'fb-login-with-faces', 'description' => __('A widget that displays a Facebook login with faces box.','wpfb') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'fb-login-with-faces' );

		/* Create the widget. */
		$this->WP_Widget( 'fb-login-with-faces', __('Facebook Login with Faces','wpfb'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		global $facebook_session;
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$show_faces = isset( $instance['show_faces'] ) ? $instance['show_faces'] : false;
		$width = $instance['width'];
		$max_rows = $instance['max_rows'];
    
		if( !$facebook_session ):
	
			/* Before widget (defined by themes). */
			echo $before_widget;
	
			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title )
				echo $before_title . $title . $after_title;
			
			$button_wrap_open = '<div class="button_wrap">';
					
			$button = "<fb:login-button ";
	
			/* If show faces was selected, show faces. */
			if ( $show_faces )
				 $button .= 'show-faces="' . $show_faces . '" ';
	
			/* If width was selected, add width to widget. */
			if ( $width )
				 $button .= 'width=' . $width . ' ';
	
			/* Show max rows value. */
			if ( $max_rows )
				 $button .= 'max-rows="' . $max_rows . '" ';
				 
			$button .= "></fb:login-button>";
			
			$button_wrap_close = '</div>';
			
			echo $button_wrap_open . $button . $button_wrap_close;
	
			/* After widget (defined by themes). */
			echo $after_widget;
		
		endif;

	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		/* Strip tags for title and layout_style to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['show_faces'] = $new_instance['show_faces'];
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['max_rows'] = strip_tags( $new_instance['max_rows'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_layout_style() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Login with Facebook','wpfb'), 'show_faces' => true, 'width' => '200', 'max_rows' => '1' );
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

		<!-- Show Faces -->
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_faces' ); ?>" name="<?php echo $this->get_field_name( 'show_faces' ); ?>"  <?php if( $instance['show_faces'] == true ) echo "checked";  ?> />
			<label for="<?php echo $this->get_field_id( 'show_faces' ); ?>"><?php _e('Show faces?','wpfb'); ?></label>
		</p>

		<!-- Max Rows -->
		<p>
			<label for="<?php echo $this->get_field_id( 'max_rows' ); ?>"><?php _e('Max Rows:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'max_rows' ); ?>" name="<?php echo $this->get_field_name( 'max_rows' ); ?>" value="<?php echo $instance['max_rows']; ?>" /> 
		</p>


	<?php
	}
}

function show_fb_login($atts){

	extract(shortcode_atts(array(
		'width' => '200',
		'show_faces' => 'true',
		'max_rows' => '3'), $atts));

	return "<fb:login-button width='{$width}' show-faces='{$show_faces}' max-rows='{$max_rows}'></fb:login-button>";
}

add_shortcode('fb-login-button', 'show_fb_login');

?>