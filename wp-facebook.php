<?php
/*
Plugin Name: WP Facebook
Description: Allow users to create an account and sign in to your site with Facebook, adds your posts/pages to the Facebook Open Graph, and makes the new Facebook Social Plugins easy to use in widget form.
Version: 0.3.6
Author: UpThemes
Author URI: http://upthemes.com/
*/

/**
 *  Load Translations
 */
load_plugin_textdomain( 'wpfb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

$root = dirname(dirname(dirname(dirname(__FILE__))));
require_once($root . '/wp-includes/registration.php');

define('FBOAUTH_APP_ID', 'fbOauth_app_id');
define('FBOAUTH_APP_SECRET', 'fbOauth_app_secret');

fbOauth_init();

function fbOauth_init() {
	
	add_theme_support('post-thumbnails');
	
  /**
   *  Install the admin menu.
   */
  add_action('admin_menu', 'fbOauth_admin_menu');

  /**
   *
   */
  register_deactivation_hook(__FILE__, 'fbOauth_deactivation_hook');

  /**
   * Are we active?
   */
  if (!fbOauth_plugin_activated()) {
			add_action('admin_notices','wpfb_not_configured_message');
      return;
  }

  /**
   * Gimme those widgets!
   */
	include_once('widgets/fb-facepile-widget.php');
	include_once('widgets/fb-like-button-widget.php');
	include_once('widgets/fb-login-with-faces-widget.php');
	include_once('widgets/fb-recommendations-widget.php');
	include_once('widgets/fb-comments-widget.php');
	include_once('widgets/fb-livestream-widget.php');
	include_once('widgets/fb-activity-feed-widget.php');

  /**
   * Add the xmlns:fb namespace to the page.  This is necessary  necessary for
   *   xfbml to work in IE.
   */
  add_filter('language_attributes', 'fbOauth_language_attributes');

  /**
   *  Includes any necessary data needed at the end of the page
   */
  add_action('wp_head', 'fbOauth_header');
  add_action('login_head', 'fbOauth_header');
  add_action('login_head','check_login_form');

  /**
   * Add action to display login button
   */
  add_action('fbOauth_login_button', 'fbOauth_login_button');
  add_action('login_form', 'fbOauth_login_button');

  /**
   * Add action to display comment checkbox
   */
  add_action('comment_form','fb_post_this_field');

  /**
   *  Includes any necessary data needed at the end of the page
   */
  add_action('wp_footer', 'fbOauth_footer');
  add_action('login_form', 'fbOauth_footer');

  /**
   * Init the plugin for users
   */
  add_action('init', 'fbOauth_init_auth', 20);
  		
	/**
	 * Determine whether to use/add Facebook commenting to posts
	 */
		
	add_action('init','wpfb_comments');

}

function wpfb_not_configured_message(){

	fbOauth_message(__("WP Facebook has been activated and <a href='options-general.php?page=wp-facebook/wp-facebook.php'>needs to be configured</a> in order to work properly.",'wpfb'));

}

function fbOauth_admin_menu() {
    if (!function_exists('add_options_page')) {
        return;
    }

    add_options_page('WP-Facebook',
                     'WP-Facebook',
                     8,
                     __FILE__,
                     'fbOauth_admin_options');
}

function admin_styles(){

	if(is_admin() && $_REQUEST['page'] == 'wp-facebook/wp-facebook.php'){
		wp_enqueue_style('wpfb-admin',get_bloginfo('wpurl') . '/wp-content/plugins/wp-facebook/styles/wpfb-admin.css');	
	}

}

if($_REQUEST['page'] == 'wp-facebook/wp-facebook.php')
  add_action('init','admin_styles');


function fbOauth_admin_options() {
	
	$FBOAUTH_APP_ID = FBOAUTH_APP_ID;
	$FBOAUTH_APP_SECRET = FBOAUTH_APP_SECRET;
	
	if (!empty($_POST[$FBOAUTH_APP_ID]) && !empty($_POST[$FBOAUTH_APP_SECRET])) {
			if (fbOauth_is_config_valid($_POST[$FBOAUTH_APP_ID], $_POST[$FBOAUTH_APP_SECRET], $errorMessage)) {

					foreach($_POST as $option => $i):
						
						update_option($option,$_POST[$option]);
						
					endforeach;

					fbOauth_message(__("Options saved.",'wpfb'));
					
			} else {
				
					fbOauth_message(__("Invalid Application ID/Secret:",'wpfb') . " . " . $errorMessage);
					
			}
	}
	

	$app_id     		= get_option($FBOAUTH_APP_ID);
	$app_secret 		= get_option($FBOAUTH_APP_SECRET);
	$comments			= get_option('fboauth_add_comments');
	$comments_width		= get_option('fboauth_comments_width');
	$comments_to_show	= get_option('fboauth_comments_to_show');

	$add_comments_selected = '';
	$replace_comments_selected = '';
	
	if($comments=='replace_comments')
		$replace_comments_selected = ' selected';
	elseif($comments=='add_comments')
		$add_comments_selected = ' selected';

	echo '<h2>' . __('WP Facebook Settings','wpfb') . '</h2>';

if((!$app_id && !$app_secret) || !fbOauth_is_config_valid($_POST[$FBOAUTH_APP_ID], $_POST[$FBOAUTH_APP_SECRET], $errorMessage)):
	echo '<p>' . __('To use Facebook OAuth you will first need to setup a Facebook Application:','wpfb') . '</p>';

    echo '<ol><li><a target="_blank" href="http://www.facebook.com/developers/createapp.php?version=new">' . __('Visit the Facebook application registration page','wpfb') . '</a>.
<li>' . __('Copy the displayed Application ID and Secret into this form.','wpfb') . '</li>
</ol>';
endif;
 
echo '<form method="post"><fieldset>';
echo '<legend>Your Facebook Application Settings</legend>';
echo '<label for="'.$FBOAUTH_APP_ID.'">' . __('Application ID:','wpfb') . '</label>
<input type="text" name="'.$FBOAUTH_APP_ID.'" size="35" value="'.$app_id.'" />
<label for="'.$FBOAUTH_APP_SECRET.'">' . __('Application Secret:','wpfb') . '</label>
<input type="text" name="'.$FBOAUTH_APP_SECRET.'" size="35" value="'.$app_secret.'" />
</fieldset>
<fieldset>
<legend>' . __('Comment Settings','wpfb') . '</legend>
<label for="fboauth_add_comments">' . __('Facebook comments?','wpfb') . '</label>
<select name="fboauth_add_comments" value="'.$comments.'" />
<option>' . __('None','wpfb') . '</option>
<option value="replace_comments"' . $replace_comments_selected . '>' . __('Replace WordPress comments','wpfb') . '</option>
<option value="add_comments"' . $add_comments_selected . '>' . __('Add Facebook comments below WordPress comments','wpfb') . '</option>
</select>
<label for="fboauth_comments_width">' . __('Width:','wpfb') . '</label>
<input type="text" name="fboauth_comments_width" size="35" value="'.$comments_width.'" />
<label for="fboauth_comments_to_show">' . __('Comments to Show:','wpfb') . '</label>
<input type="text" name="fboauth_comments_to_show" size="3" value="'.$comments_to_show.'" />
</fieldset>
<input type="submit" class="button-primary save alignright" value="' . __('Save Changes','wpfb') . '" />
<div class="clear"></div>
</form>';

echo '<div id="buy-themes"><h2>' . __('Buy a WordPress Theme from UpThemes','wpfb') . '</h2><iframe src="http://upthemes.com/buy-themes/" frameborder="0"></iframe></div>';

}

function fbOauth_message($message) {
    echo '<div class="updated"><p><strong>' . $message . '</strong></p></div>';
}

function fbOauth_is_config_valid($app_id, $app_secret, &$error) {
    //TODO: Figure out a way to test the tokens
    return true;
    
    try {
        require_once 'facebook.php';
        
        $facebook = new Facebook(array(
                    'appId'  => $app_id,
                    'secret' => $app_secret,
                    'cookie' => false));

        $response = $facebook->api('/me');

        $success = true;
    } catch(Exception $e) {
        $success = false;
        $error = $e->getMessage();
    }

    return $success;
}

function fbOauth_deactivation_hook() {
    fbOauth_clear_config();
}

function fbOauth_clear_config() {
    update_option(FBOAUTH_APP_ID, null);
    update_option(FBOAUTH_APP_SECRET, null);
}

function fbOauth_plugin_activated() {
    $app_id     = get_option(FBOAUTH_APP_ID);
    $app_secret = get_option(FBOAUTH_APP_SECRET);
    
    return !empty($app_id) && !empty($app_secret);
}

function fbOauth_language_attributes($output) {
  return $output . ' xmlns:fb="http://www.facebook.com/2008/fbmld"';
}

function fbOauth_init_auth() {
    require_once 'facebook.php';

    global $fbOauth_facebook, $facebook_session;

    $fbOauth_facebook = new Facebook(array(
        'appId'  => get_option(FBOAUTH_APP_ID),
        'secret' => get_option(FBOAUTH_APP_SECRET),
        'cookie' => true));

    $facebook_session = $fbOauth_facebook->getSession();

    if ($facebook_session) {
        try {
            $user = $fbOauth_facebook->api('/me');
            fbOauth_sync_auth();
            add_action('comment_post', 'fbOauth_comment_post');
        } catch (Exception $e) {
            $fbOauth_facebook->setSession();
            $facebook_session = null;
        }
    }

    fbOauth_init_javascript_sdk();

    do_action('bp_fb_twiter_add_all_potential_friends');
}

function fbOauth_init_javascript_sdk() {
	
    global $facebook_session;

    $app_id     = get_option(FBOAUTH_APP_ID);
		
	if(FB_LOGIN_FORM==true)
		$location = '"' . get_bloginfo('wpurl') . '"';
	else
		$location = window.location;

    $fb_login = <<<EOF
  FB.Event.subscribe('auth.login', function(response) {
    window.location = $location;
  });
EOF;

	$redirect = wp_logout_url( get_bloginfo('wpurl') );

	$wpurl = get_bloginfo('wpurl');
	
	$fb_logout_redirect = $wpurl . $_SERVER['REQUEST_URI'];

  $fb_logout = <<<EOF
	jQuery("a[href*=logout]").live("click",function(e){
		e.preventDefault();
	
		FB.logout(function(response) {
			if(response.success)
				window.location = '$fb_logout_redirect';
		});
	
	});
EOF;

	if($facebook_session && is_user_logged_in())
		$fb_login = $fb_logout;
	
    $data = <<<EOF
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
  FB.init({appId: '{$app_id}', status: true, cookie: true, xfbml: true});
  {$fb_login}  
</script>
EOF;

    _fbOauth_footer_register($data, true);
}

function fbOauth_header() {
	global $post;
    /**
     * Get us the meta tag for fb:app_id
     */
    $app_id = get_option(FBOAUTH_APP_ID);
?>
	<meta property="fb:app_id" content="<?php echo $app_id; ?>"/>
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>"/>
    
    <?php
    if(is_single()): 
		$type = 'article';
	elseif(is_front_page() || is_home() || is_page()):
		$type = 'blog';
	endif;
	
	$type = apply_filters('facebook_og_type',$type);
	
	?>
    
    <meta property="og:type" content="<?php echo $type ?>" />

	<?php if(wp_title(' ', false)): ?>
        <meta property="og:title" content="<?php wp_title() ?>" />
    <?php endif; ?>
    
	<?php if(is_single() || is_page() || is_category() || is_tag() || is_archive() || is_attachment): ?>
        <meta property="og:url" content="<?php the_permalink() ?>"/>
        <?php if(has_post_thumbnail($post->ID)): ?>
        	<meta property="og:image" content="<?php echo wpfb_get_the_post_thumbnail_src($post->ID) ?>" />
        <?php endif; ?>
	<?php elseif(is_home() || is_front_page()): ?>
        <meta property="og:url" content="<?php bloginfo('wpurl') ?>"/>
	<?php endif;
}

function fbOauth_footer() {
    echo _fbOauth_flush_footer_data();
}

function _fbOauth_footer_register($data, $prepend=false) {
    global $footerData;
    
    if (!$footerData) {
        $footerData = array();
    }

    if ($prepend) {
        array_unshift($footerData, $data);
    } else {
        $footerData[] = $data;
    }
}

function _fbOauth_flush_footer_data() {
    global $footerData;
    $footerData[] = '';

    $data = implode("\n", $footerData);
    $footerData = null;

    return $data;
}

function fbOauth_login_button() {
    global $facebook_session;

    if (!$facebook_session && !is_user_logged_in()) {
		echo "<fb:login-button perms='email,publish_stream'></fb:login-button>";
    }
}

function fbOauth_sync_auth() {
    global $fbOauth_facebook;

    try {
        $fbuid = $fbOauth_facebook->getUser();
    } catch (Exception $e) {}

    $assoc_fbuid = 0;
    $user = wp_get_current_user();
    if ( 0 != $user->ID ) {
        $assoc_fbuid = get_usermeta($user->ID, 'fbuid');
    }

    if ($assoc_fbuid) {
        if ($fbuid == $assoc_fbuid) {
            // user is already logged in to both
            return;
        } else {
            //wp session, no fbsession = logout of wp and reload page
            // or, user is logged in under a different fb account
            wp_logout();
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }
    } else {
        if ($user->ID) {
            // wpuser not associated w/ fb.  do nothing
            return;
        } else if($fbuid) {
            // not a wp user, but logged into facebook
            $res = fbOauth_login();
            if ($res > 0) {
                $wp_uid = fbOauth_get_user_by_fbuid($fbuid);
                wp_set_current_user($wp_uid);
            } else {
                //login error
            }
        } else {
            // neither facebook nor wordpress, do nothing
        }
    }
}

function fbOauth_login($allow_link=false) {
    global $fbOauth_facebook;

    try {
        $fbuid = $fbOauth_facebook->getUser();
    } catch (Exception $e) {}

    if ($fbuid) {
        $wpuid = fbOauth_get_user_by_fbuid($fbuid);
        if (!$wpuid) {
            // There is no wp user associated w/ this fbuid

            $user = wp_get_current_user();
            $wpuid = $user->ID;
            if ($wpuid && $allow_link) {
                // User already has a wordpress account, link to this facebook account
                update_usermeta($wpuid, 'fbuid', "$fbuid");
            } else {
                // Create a new wordpress account
                $wpuid = fbOauth_create_user($fbuid);
                if ($wpuid === FBC_ERROR_USERNAME_EXISTS) {
                    return FBC_ERROR_USERNAME_EXISTS;
                }
            }

        } else {
            // Already have a linked wordpress account, fall through and set
            // login cookie
        }

        wp_set_auth_cookie($wpuid, true, false);

        return $fbuid;
    }

    return 0;
}

function fbOauth_get_user_by_fbuid($value) {
    global $wpdb;

    $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'fbuid' AND meta_value = '%s'";

    return $wpdb->get_var($wpdb->prepare($sql, $value));
}

function fbOauth_create_user($fbuid) {
    global $fbOauth_facebook;

    try {
        $fbuid = $fbOauth_facebook->getUser();
        $userinfo = $fbOauth_facebook->api('/me');
    } catch (Exception $e) {
        return 0;
    }

    if ($userinfo === null) {
        error_log('fbOauth: empty query result for user ' . $fbuid);
    }

    $fbusername = 'fb' . $fbuid;
    if (username_exists($fbusername)) {
        return FBC_ERROR_USERNAME_EXISTS;
    }

    $userdata = fbOauth_fb_to_wp($userinfo);
    $userdata['user_pass'] = wp_generate_password();
    $userdata['user_login'] = $fbusername;

    $wpuid = wp_insert_user($userdata);
    if($wpuid) {
        update_usermeta($wpuid, 'fbuid', "$fbuid");
    }

    return $wpuid;
}

function fbOauth_fb_to_wp($userinfo) {
    return array(
        'display_name' => $userinfo['name'],
        'user_url' => $userinfo['link'],
        'user_email' => $userinfo['email'],
        'first_name' => $userinfo['first_name'],
        'last_name' => $userinfo['last_name'],
    );
}

function fb_post_this_field(){

	global $facebook_session;
	
	if($facebook_session):
		echo '<fieldset class="fb_post_this"><input id="fb_post_this" name="fb_post_this" type="checkbox"/> <label for="fb_post_this">' . __('Post comment to Facebook?','wpfb') . '</label></fieldset>';
	endif;

}

function fbOauth_comment_post($comment_ID) {

    global $fbOauth_facebook;

    if(!isset($_REQUEST["fb_post_this"])) {
        return;
    }

    $comment = get_comment($comment_ID);
    $post_title = strip_tags(get_the_title( $comment->comment_post_ID ));
    $blog_title = get_bloginfo('name');

    $permalink = '';
    //Use the comment link if it is approved, otherwise use the post link.
    if($comment->comment_approved == 1) {
        $permalink = get_comment_link($comment);
    } else {
        $permalink = get_permalink($comment->comment_post_ID);
    }

    if(!empty($permalink)) {
        try {
            $fbOauth_facebook->api('/me/feed', 'post', array('message' => $comment->comment_content, 'name' => __('commented on:','wpfb') . ' ' . $post_title, 'link' => $permalink));
        } catch (Exception $e) {
        }
    }
}

function check_login_form(){
	define('FB_LOGIN_FORM',true);	
}

function wpfb_comments(){
	
	$add_comments = get_option('fboauth_add_comments');
	
	if($add_comments == 'replace_comments'):

		$comment_option = get_option('fboauth_add_comments');
		add_filter('comments_template','wpfb_comments_template');

	elseif($comment_option == 'add_comments'):
		
		add_action('comment_template','wpfb_comments_template');

	endif;
	
}

// Hook into existing template and inject WP Facebook comment system
function wpfb_comments_template( $file ) {
	if ( !is_singular() )
		return $file;
	
	update_option( "wpfb_comment_template_file", $file );
	return dirname( __FILE__ ) . '/wpfb-comment-template.php';
}

// Get Post Thumbnail Source
function wpfb_get_the_post_thumbnail_src( $post_id = NULL ) {
	global $id;
	$post_id = ( NULL === $post_id ) ? $id : $post_id;
	
	$post_thumbnail_id = get_post_thumbnail_id( $post_id );
	$url = false;
	
	if ( $post_thumbnail_id ) {
		$size = apply_filters( 'post_thumbnail_size', 'post-thumbnail' );
		$image = wp_get_attachment_image_src( $post_thumbnail_id, $size, false );
		
		if ( $image ) {
			list( $src, $width, $height ) = $image; 
			$url = $src;
		}
	}
	
	return $url;
}