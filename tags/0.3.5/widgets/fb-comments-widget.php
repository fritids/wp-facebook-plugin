<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_fb_comments_widget' );

/**
 * Register our widget.
 * 'FB_Comments_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_fb_comments_widget() {
	register_widget( 'FB_Comments_Widget' );
}

/**
 * Facebook Comments class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class FB_Comments_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function FB_Comments_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'fb-comments', 'description' => __('A widget that displays the Facebook comment form.','wpfb') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'fb-comments' );

		/* Create the widget. */
		$this->WP_Widget( 'fb-comments', __('Facebook Comments','wpfb'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		
		if(is_single() || is_page()):
			
			global $post;
			$uid = get_the_permalink();
			
		else:

			$uid = $instance['uid'];

		endif;

		$numposts = $instance['numposts'];
		$width = $instance['width'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		
		$comments_open = '<div class="comments_wrap">';
				
		$fbml = "<fb:comments ";

		/* If number of comments was set, set limit. */
		if ( $numposts )
			 $fbml .= 'numposts="' . $numposts . '" ';
		else
			 $fbml .= 'numposts="10" ';

		/* If width was selected, add width to widget. */
		if ( $width )
			 $fbml .= 'width="' . $width . '" ';
			 
		$fbml .= "/>";
		
		$comments_close = '</div>';
		
		echo $comments_open . $fbml . $comments_close;

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
		$instance['numposts'] = strip_tags($new_instance['numposts']);
		$instance['width'] = strip_tags( $new_instance['width'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_layout_style() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Comments from Facebook','wpfb'), 'numposts' => '10', 'width' => '200' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title (leave blank for none):','wpfb'); ?></label>
			<input value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" />
		</p>

		<!-- Width -->
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" /> 
		</p>

		<!-- Number of Comments to Show -->
		<p>
			<label for="<?php echo $this->get_field_id( 'numposts' ); ?>"><?php _e('Number of Comments:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'numposts' ); ?>" name="<?php echo $this->get_field_name( 'numposts' ); ?>" value="<?php echo $instance['numposts']; ?>" /> 
		</p>

	<?php
	}
}

function show_fb_comments($atts){

	extract(shortcode_atts(array(
		'width' => '500',
		'numposts' => '10'), $atts));

	return "<fb:comments width='{$width}' numposts='{$numposts}'></fb:comments>";
}

add_shortcode('fb-comments', 'show_fb_comments');

?>