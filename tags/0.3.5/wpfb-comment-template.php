<?php
/***
 * Replace comment form with Facebook comments.
 */

global $post;

$numposts = get_option('fboauth_comments_num_posts');
$width	  = get_option('fboauth_comments_width');

?>

<div class="facebook_comment_form">

	<h3><?php _e('Facebook Comments','wpfb'); ?></h3>

	<fb:comments id="<?php echo get_the_ID() ?>" numposts="<?php if($numposts) echo $numposts; else echo '10'; ?>" width="<?php if($width) echo $width; else echo '500'; ?>" />

</div>