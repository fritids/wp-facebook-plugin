<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_fb_activity_feed_widget' );

/**
 * Register our widget.
 * 'FB_Activity_Feed_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_fb_activity_feed_widget() {
	register_widget( 'FB_Activity_Feed_Widget' );
}

/**
 * Facebook Activity Feed class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class FB_Activity_Feed_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function FB_Activity_Feed_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'fb-activity-feed', 'description' => __('A widget that displays a Facebook activity feed.','wpfb') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'fb-activity-feed' );

		/* Create the widget. */
		$this->WP_Widget( 'fb-activity-feed', __('Facebook Activity Feed','wpfb'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$domain = isset( $instance['domain'] ) ? $instance['domain'] : get_bloginfo('url');
		$width = $instance['width'];
		$height = $instance['height'];
		$show_header = isset( $instance['show_header'] ) ? $instance['show_header'] : false;
		$color_scheme = $instance['color_scheme'];
		$font = $instance['font'];
		$border_color = $instance['border_color'];
		$show_recommendations = isset( $instance['show_recommendations'] ) ? $instance['show_recommendations'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		
		$button_wrap_open = '<div class="button_wrap">';
				
		$button = "<fb:activity ";

		/* Display domain from widget settings if one was input. */
		if ( $domain )
			 $button .= 'domain="' . $domain . '" ';

		/* If width was selected, add width to widget. */
		if ( $width != '300' )
			 $button .= 'width="' . $width . '" ';

		/* If height was selected, add height to widget. */
		if ( $height != '300' )
			 $button .= 'height="' . $height . '" ';

		/* If show header was selected, show recommend text. */
		if ( $show_header == true )
			 $button .= 'header="true" ';
		else
			 $button .= 'header="false" ';
			 
		/* If color scheme was selected, add appropriate colors text. */
		if ( $color_scheme == 'dark' )
			 $button .= 'colorscheme="' . $color_scheme . '" ';

		/* If recommend was selected, show recommend text. */
		if ( $font != "arial" )
			 $button .= 'font="' . $font . '" ';

		/* If color scheme was selected, add appropriate colors text. */
		if ( $border_color )
			 $button .= 'border_color="' . $border_color . '" ';

		/* If show recommendations was selected, show recommendations. */
		if ( $show_recommendations == true )
			 $button .= 'recommendations="true" ';
		else
			 $button .= 'recommendations="false" ';
			 
		$button .= "></fb:activity>";
		
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
		$instance['domain'] = strip_tags($new_instance['domain']);
		$instance['show_header'] = $new_instance['show_header'];
		$instance['color_scheme'] = $new_instance['color_scheme'];
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['height'] = strip_tags( $new_instance['height'] );
		$instance['font'] = $new_instance['font'];
		$instance['border_color'] = strip_tags( $new_instance['border_color'] );
		$instance['show_recommendations'] = $new_instance['show_recommendations'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_layout_style() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Recent Activity','wpfb'), 'domain' => '', 'show_header' => true, 'color_scheme' => '', 'width' => '300', 'height' => '300', 'font' => 'arial', 'border_color' => '', 'show_recommendations' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title (leave blank for none):'); ?></label>
			<input value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" />
		</p>

		<!-- Domain -->
		<p>
			<label for="<?php echo $this->get_field_id( 'domain' ); ?>"><?php _e('Domain:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'domain' ); ?>" name="<?php echo $this->get_field_name( 'domain' ); ?>" value="<?php echo $instance['domain']; ?>" /> 
		</p>

		<!-- Show Header -->
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_header' ); ?>" name="<?php echo $this->get_field_name( 'show_header' ); ?>"  <?php if( $instance['show_header'] == true ) echo "checked";  ?> />
			<label for="<?php echo $this->get_field_id( 'show_header' ); ?>"><?php _e('Show header?','wpfb'); ?></label>
		</p>

		<!-- Color Scheme -->
		<p>
			<label for="<?php echo $this->get_field_id( 'color_scheme' ); ?>"><?php _e('Color scheme:'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'color_scheme' ); ?>" name="<?php echo $this->get_field_name( 'color_scheme' ); ?>" class="widefat">
				<option><?php _e('Light','wpfb'); ?></option>
				<option value="dark" <?php if ( 'dark' == $instance['color_scheme'] ) echo 'selected="selected"'; ?>><?php _e('Dark','wpfb'); ?></option>
			</select>
		</p>

		<!-- Width -->
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" /> 
		</p>

		<!-- Height -->
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" /> 
		</p>

		<!-- Font -->
		<p>
			<label for="<?php echo $this->get_field_id( 'font' ); ?>"><?php _e('Font:'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'font' ); ?>" name="<?php echo $this->get_field_name( 'font' ); ?>" class="widefat">
				<option value="arial" <?php if ( 'arial' == $instance['font'] ) echo 'selected="selected"'; ?>>Arial</option>
				<option value="lucida grande" <?php if ( 'lucida grande' == $instance['font'] ) echo 'selected="selected"'; ?>>Lucida Grande</option>
				<option value="segoe ui" <?php if ( 'segoe ui' == $instance['font'] ) echo 'selected="selected"'; ?>>Segoe UI</option>
				<option value="tahoma" <?php if ( 'tahoma' == $instance['font'] ) echo 'selected="selected"'; ?>>Tahoma</option>
				<option value="trebuchet ms" <?php if ( 'trebuchet ms' == $instance['font'] ) echo 'selected="selected"'; ?>>Trebuchet MS</option>
				<option value="verdana" <?php if ( 'verdana' == $instance['font'] ) echo 'selected="selected"'; ?>>Verdana</option>
			</select>
		</p>

		<!-- Show Recommendations -->
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_recommendations' ); ?>" name="<?php echo $this->get_field_name( 'show_recommendations' ); ?>"  <?php if( $instance['show_recommendations'] == true ) echo "checked";  ?> />
			<label for="<?php echo $this->get_field_id( 'show_recommendations' ); ?>"><?php _e('Show recommendations?','wpfb'); ?></label>
		</p>

		<!-- Border Color -->
		<p>
			<label for="<?php echo $this->get_field_id( 'border_color' ); ?>"><?php _e('Border color:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'border_color' ); ?>" name="<?php echo $this->get_field_name( 'border_color' ); ?>" value="<?php echo $instance['border_color']; ?>" /> 
		</p>

	<?php
	}
}

function show_fb_activity_feed($atts){

	extract(shortcode_atts(array(
		'domain' => get_bloginfo('url'),
		'show_header' => 'true',
		'width' => '300',
		'height' => '300',
		'color_scheme' => 'light',
		'font' => 'arial',
		'show_recommendations' => 'true',
		'border_color' => ''), $atts));

	return "<fb:activity domain='{$domain}' show_header='{$show_header}' width='{$width}' height='{$height}' colorscheme='{$color_scheme}' font='{$font}' recommendations='{$show_recommendations}' border_color='{$border_color}'></fb:activity>";
}

add_shortcode('fb-activity-feed', 'show_fb_activity_feed');

?>