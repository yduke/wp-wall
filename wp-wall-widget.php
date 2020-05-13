<!-- WP Wall -->
<?php 	
	echo $before_widget; 
	echo $before_title . $wall_title. $after_title;
?>
<div id="wp_wall">
<div class="wallnav">
<i alt="Previous" id="img_left" class="iconfont ico-interface-up-open-big"></i>
<i alt="Next" id="img_right" class="iconfont ico-arrowdown"></i>
<?php if ( $show_all ) : ?>
<a  href="<?php echo get_permalink($pageId) ?>">所有留言</a>
<?php endif; ?>
  </div> 
<div id="wallcomments">
<?php echo WPWall_ShowComments(); ?>
</div>
	<?php if ( $rss_feed ): ?>
<p><a href="<?php echo get_post_comments_feed_link($pageId); ?>" id="wall_rss"><img src="<?php echo $wp_wall_plugin_url; ?>/i/feed.png" /> <?php echo $wall_title; ?> RSS Feed</a></p>			
<?php endif; ?>
<?php if ( ! $disable_new ) : ?>
<?php if (  $only_registered && !$user_ID) : ?>
	<p><a href="wp-login.php">Log in to post a comment.</a></p>	
<?php else : ?>
	<p><a id="wall_post_toggle" class="button ie6fix"><?php echo $wall_reply; ?></a></p>	
<?php endif; ?>
<?php endif; ?>
<div id="wall_post">
<form action="<?php echo $wp_wall_plugin_url.'wp-wall-ajax.php'; ?>" method="post" id="wallform" class="form--square form--no-labels form--active">
<?php if ( $user_ID ) : ?>
<p>身份：<a href="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>.</p>
<?php else : ?>
<p>
<div class="input-with-icon"><i class="icon iconfont ico-icon-Male-2"></i><input type="text" name="wpwall_author" id="wpwall_author" value="" placeholder="称呼" tabindex="11" class="validate-required" /></div>
</p>
<?php if ( $show_email ) : ?>
<p>
<div class="input-with-icon"><i class="icon iconfont ico-icon-Mail-2"></i><input type="text" name="wpwall_email" id="wpwall_email" value="" placeholder="Email" tabindex="12" class="validate-required validate-email" /></div>
</p>
<?php endif; ?>
<?php endif; ?>
<p>
<label for="wpwall_comment"><h4>留言</h4></label>
<textarea name="wpwall_comment" placeholder="对博主说....." id="wpwall_comment" class="rounded text_input" rows="3" tabindex="13" ></textarea>
</p>	
<p><input name="submit_wall_post" type="submit" id="submit_wall_post" class="button ie6fix" tabindex="14" value="发送" /></p>
</form> 								
</div>
<div id="wallresponse"></div>
</div>
<br />
<?php echo $after_widget; ?>