<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_fb_like_button_widget' );

/**
 * Register our widget.
 * 'FB_Like_Button_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_fb_like_button_widget() {
	register_widget( 'FB_Like_Button_Widget' );
}

/**
 * Facebook Like Button class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class FB_Like_Button_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function FB_Like_Button_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'fb-like-button', 'description' => __('A widget that displays the Facebook Like button.','wpfb') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'fb-like-button' );

		/* Create the widget. */
		$this->WP_Widget( 'fb-like-button', __('Facebook Like Button','wpfb'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$show_faces = isset( $instance['show_faces'] ) ? $instance['show_faces'] : false;
		$layout_style = isset( $instance['layout_style'] ) ? $instance['layout_style'] : false;
		$verb_to_display = isset( $instance['verb_to_display'] ) ? $instance['verb_to_display'] : false;
		$width = $instance['width'];
		$font = $instance['font'];

		if(!is_category() && !is_archive() && !is_tag()):
	
			/* Before widget (defined by themes). */
			echo $before_widget;
	
			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title )
				echo $before_title . $title . $after_title;
			
			$button_wrap_open = '<div class="button_wrap">';
					
			$button = "<fb:like ";
	
			/* Display layout style from widget settings if one was input. */
			if ( $layout_style )
				 $button .= 'layout="' . $layout_style . '" ';
	
			/* If show faces was selected, show faces. */
			if ( $show_faces )
				 $button .= 'showfaces="' . $show_faces . '" ';
	
			/* If width was selected, add width to widget. */
			if ( $width )
				 $button .= 'width="' . $width . '" ';
	
			/* If recommend was selected, show recommend text. */
			if ( $verb_to_display )
				 $button .= 'action="' . $verb_to_display . '" ';
	
			/* If recommend was selected, show recommend text. */
			if ( $font )
				 $button .= 'font="' . $font . '" ';
				 
			$button .= "/>";
			
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
		$instance['layout_style'] = $new_instance['layout_style'];
		$instance['verb_to_display'] = $new_instance['verb_to_display'];
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['font'] = $new_instance['font'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_layout_style() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Like This?','wpfb'), 'show_faces' => true, 'layout_style' => false, 'verb_to_display' => false, 'width' => '200', 'font' => 'arial' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title (leave blank for none):','wpfb'); ?></label>
			<input value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" />
		</p>

		<!-- Layout Style -->
		<p>
			<label for="<?php echo $this->get_field_id( 'layout_style' ); ?>"><?php _e('Layout Style:','wpfb'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'layout_style' ); ?>" name="<?php echo $this->get_field_name( 'layout_style' ); ?>" class="widefat">
				<option value="standard">Standard</option>
				<option value="button_count" <?php if ( 'button_count' == $instance['layout_style'] ) echo 'selected="selected"'; ?>><?php _e('Button Count','wpfb'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" /> 
		</p>

		<!-- Verb to Display Style -->
		<p>
			<label for="<?php echo $this->get_field_id( 'verb_to_display' ); ?>"><?php _e('Verb to Display:','wpfb'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'verb_to_display' ); ?>" name="<?php echo $this->get_field_name( 'verb_to_display' ); ?>" class="widefat">
				<option value="like"><?php _e('Like','wpfb'); ?></option>
				<option value="recommend" <?php if ( 'recommend' == $instance['verb_to_display'] ) echo 'selected="selected"'; ?>><?php _e('Recommend','wpfb'); ?></option>
			</select>
		</p>

		<!-- Show Faces -->
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_faces' ); ?>" name="<?php echo $this->get_field_name( 'show_faces' ); ?>"  <?php if( $instance['show_faces'] == true ) echo "checked";  ?> />
			<label for="<?php echo $this->get_field_id( 'show_faces' ); ?>"><?php _e('Show faces?','wpfb'); ?></label>
		</p>

		<!-- Font -->
		<p>
			<label for="<?php echo $this->get_field_id( 'font' ); ?>"><?php _e('Font:','wpfb'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'font' ); ?>" name="<?php echo $this->get_field_name( 'font' ); ?>" class="widefat">
				<option value="arial" <?php if ( 'arial' == $instance['font'] ) echo 'selected="selected"'; ?>>Arial</option>
				<option value="lucida grande" <?php if ( 'lucida grande' == $instance['font'] ) echo 'selected="selected"'; ?>>Lucida Grande</option>
				<option value="segoe ui" <?php if ( 'segoe ui' == $instance['font'] ) echo 'selected="selected"'; ?>>Segoe UI</option>
				<option value="tahoma" <?php if ( 'tahoma' == $instance['font'] ) echo 'selected="selected"'; ?>>Tahoma</option>
				<option value="trebuchet ms" <?php if ( 'trebuchet ms' == $instance['font'] ) echo 'selected="selected"'; ?>>Trebuchet MS</option>
				<option value="verdana" <?php if ( 'verdana' == $instance['font'] ) echo 'selected="selected"'; ?>>Verdana</option>
			</select>
		</p>

	<?php
	}
}

function show_fb_like_button($atts){

	extract(shortcode_atts(array(
		'show_faces' => 'true',
		'layout_style' => 'standard',
		'verb_to_display' => 'like',
		'width' => '200',
		'font' => 'arial'), $atts));

	return "<fb:like show-faces='{$show_faces}' layout-style='{$layout_style}' verb-to-display='{$verb_to_display}' width='{$width}' font='{$font}'></fb:like>";
}

add_shortcode('fb-like-button', 'show_fb_like_button');

?>