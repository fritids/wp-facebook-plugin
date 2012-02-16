<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_fb_recommendations_widget' );

/**
 * Register our widget.
 * 'FB_recommendations_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_fb_recommendations_widget() {
	register_widget( 'FB_recommendations_Widget' );
}

/**
 * Facebook Like Button class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class FB_recommendations_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function FB_recommendations_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'fb-recommendations', 'description' => __('A widget that displays the Facebook recommendations box.','wpfb') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'fb-recommendations' );

		/* Create the widget. */
		$this->WP_Widget( 'fb-recommendations', __('Facebook Recommendations','wpfb'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$domain =	$instance['domain'];
		$width = $instance['width'];
		$height = $instance['height'];
		$header =  isset( $instance['header'] ) ? $instance['header'] : false;
		$font = $instance['font'];
		$border_color = isset( $instance['border_color'] ) ? $instance['border_color'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		
		$button_wrap_open = '<div class="button_wrap">';
				
		$button = "<fb:recommendations ";

		/* Display domain from widget settings if one was entered. */
		if ( $domain )
			 $button .= 'site="' . $domain . '" ';

		/* If width was selected, add width to widget. */
		if ( $width && $width != '300' )
			 $button .= 'width="' . $width . '" ';

		/* If height was selected, add height to widget. */
		if ( $height && $height != '300' )
			 $button .= 'height="' . $height . '" ';

		/* If show header was unchecked, hide it. */
		if ( $header == true )
			 $button .= 'header="true" ';
		else
			 $button .= 'header="false" ';
			 
		/* If font was selected, add correct font. */
		if ( $font )
			 $button .= 'font="' . $font . '" ';

		/* If border was defined, add it. */
		if ( $border_color )
			 $button .= 'border_color="' . $border_color . '" ';
			 
		$button .= "/>";
		
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
		$instance['domain'] = strip_tags( $new_instance['domain'] );
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['height'] = strip_tags( $new_instance['height'] );
		$instance['header'] = $new_instance['header'];
		$instance['font'] = $new_instance['font'];
		$instance['border_color'] = strip_tags( $new_instance['border_color'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_layout_style() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Recommendations for','wpfb') . ' ' . get_bloginfo('name'), 'domain' => get_bloginfo('wpurl'), 'width' => '200', 'height' => '300', 'header' => true, 'font' => 'arial', 'border_color' => 'black' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title -->	
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title (leave blank for none):','wpfb'); ?></label>
			<input value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" />
		</p>

		<!-- Layout Style -->
		<p>
			<label for="<?php echo $this->get_field_id( 'domain' ); ?>"><?php _e('Domain:','wpfb'); ?></label> 
			<input id="<?php echo $this->get_field_id( 'domain' ); ?>" name="<?php echo $this->get_field_name( 'domain' ); ?>" value="<?php echo $instance['domain']; ?>" /> 
		</p>

		<!-- Width Style -->
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" /> 
		</p>

		<!-- Height Style -->
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" /> 
		</p>

		<!-- Header -->
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'header' ); ?>" name="<?php echo $this->get_field_name( 'header' ); ?>" <?php if ( $instance['header'] == true ) echo 'checked"'; ?>/> 
      <label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e('Show Header?','wpfb'); ?></label> 
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

		<!-- Border Color -->
		<p>
			<label for="<?php echo $this->get_field_id( 'border_color' ); ?>"><?php _e('Border Color:','wpfb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'border_color' ); ?>" name="<?php echo $this->get_field_name( 'border_color' ); ?>" value="<?php echo $instance['border_color']; ?>" /> 
		</p>

	<?php
	}
}

		$instance['domain'] = strip_tags( $new_instance['domain'] );
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['height'] = strip_tags( $new_instance['height'] );
		$instance['header'] = $new_instance['header'];
		$instance['font'] = $new_instance['font'];
		$instance['border_color'] = strip_tags( $new_instance['border_color'] );

function show_fb_recommendations($atts){

	extract(shortcode_atts(array(
		'domain' => get_bloginfo('wpurl'),
		'width' => '200',
		'height' => '400',
		'header' => 'true',
		'font' => 'arial',
		'border_color' => '#000'), $atts));

	return "<fb:recommendations domain='{$domain}' width='{$width}' height='{$height}' header='{$header}' font='{$font}' border_color='{$border_color}'></fb:recommendations>";
}

add_shortcode('fb-recommendations', 'show_fb_recommendations');

?>