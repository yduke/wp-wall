<!-- WP Wall -->
<?php 	
	echo $before_widget; 
	echo $before_title . $wall_title. $after_title;
?>
<div id="wp_wall" class="text-start">
<div class="wallnav">
<i alt="Previous" id="img_left" class="iconfont ico-angle-left"></i>

<?php if ( $show_all ) : ?>
<a  href="<?php echo get_permalink($pageId) ?>"><?php _e('All','wp-wall') ?></a>
<?php endif; ?>
<i alt="Next" id="img_right" class="iconfont ico-angle-right"></i>
  </div> 
<div id="wallcomments">
<?php echo WPWall_ShowComments(); ?>
</div>
	<?php if ( $rss_feed ): ?>
<p><a href="<?php echo get_post_comments_feed_link($pageId); ?>" id="wall_rss"><img src="<?php echo $wp_wall_plugin_url; ?>/i/feed.png" /> <?php echo $wall_title; ?><?php _e('RSS feed','wp-wall') ?></a></p>			
<?php endif; ?>
<?php if ( ! $disable_new ) : ?>
    <?php if (  $only_registered && !$user_ID) : ?>
    	<p><a href="wp-login.php"><?php _e('Log in to post a comment.','wp-wall') ?></a></p>	
    <?php else : ?>
    	<p><a id="wall_post_toggle" class="btn btn-success"><span class="btn__text"><?php echo $wall_reply; ?></span></a></p>	
    <?php endif; ?>
    <?php endif; ?>
    <div id="wall_post">
    <form action="<?php echo $wp_wall_plugin_url.'wp-wall-ajax.php'; ?>" method="post" id="wallform" class="form--square form--no-labels form--active">
    <?php if ( $user_ID ) : ?>
    <p><?php _e('Logged in as','wp-wall') ?><a href="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>.</p>
    <?php else : ?>
        <div class="input-group flex-nowrap mb-1">
          <span class="input-group-text" id="nickname"><i class="icon iconfont ico-male"></i></span>
          <input type="text" name="wpwall_author" id="wpwall_author" class="form-control validate-required" placeholder="<?php _e('Name','wp-wall') ?>"  tabindex="11" aria-label="Username" aria-describedby="nickname" required >
        </div>
    <?php if ( $show_email ) : ?>
        <div class="input-group flex-nowrap">
          <span class="input-group-text" id="email"><i class="icon iconfont ico-mail"></i></span>
          <input type="email" name="wpwall_email" id="wpwall_email" class="form-control validate-required validate-email" placeholder="<?php _e('Email','wp-wall') ?>"  tabindex="11" aria-label="Email" aria-describedby="email" required>
        </div>
    <?php endif; ?>
<?php endif; ?>

<label for="wpwall_comment"><h6><?php _e('Comment','wp-wall') ?></h4></label>
<textarea class="form-control mb-3" name="wpwall_comment" placeholder="<?php _e('Start typing here...','wp-wall') ?>" id="wpwall_comment" class="rounded text_input" rows="3" tabindex="13" required></textarea>
<input name="submit_wall_post" class="btn btn-primary mb-3" type="submit" id="submit_wall_post" class="button ie6fix" tabindex="14" value="<?php _e('Submit','wp-wall') ?>" />
</form> 								
</div>
</div>
<br />
<?php echo $after_widget; ?>