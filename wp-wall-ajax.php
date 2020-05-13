<?php

require_once("../../../wp-config.php");

$submit_wall_post = isset($_POST['submit_wall_post']) ? $_POST['submit_wall_post']: '';
$refresh = isset($_GET['refresh']) ? $_GET['refresh']: '';

if ($submit_wall_post){
	
	$options = WPWall_GetOptions();
			
	$comment_post_ID=$options['pageId'];
	$actual_post=get_post($comment_post_ID);
	
	// sanity check to see if our page exists
	if (!$comment_post_ID || !$actual_post || ($comment_post_ID!=$actual_post->ID) )
	{
		wp_die( __('Sorry, there was a problem posting your comment. Please try again.','wp-wall') );
	}
	
	if ($options['disable_new'])
	{
		wp_die( __('Sorry, the comments are disabled at the moment.') );
	}
	
	// extract data we need	
	
	
	$comment_author       = trim(strip_tags(  isset($_POST['wpwall_author'])? $_POST['wpwall_author'] : '' ));
	$comment_content      = trim(   isset( $_POST['wpwall_comment'] ) ? $_POST['wpwall_comment'] : ''  );
	$comment_author_email = trim(  isset( $_POST['wpwall_email'] )? $_POST['wpwall_email'] : '' );
	$comment_author_url = trim(  isset( $_POST['wpwall_url'] )? $_POST['wpwall_url'] : '' );
	
	// If the user is logged in get his name	
	$user = wp_get_current_user();
	if ( $user->ID ) {
		$comment_author  = esc_sql($user->display_name);		
	  $comment_author_email = esc_sql($user->user_email);
	  
	}
	else if (get_user_by('login',$comment_author))	
			wp_die( __('Sorry, you have to pick another name.','wp-wall') );	
	else if ( $options['only_registered'] )
		wp_die( __('Sorry, you must be logged in to post a comment.','wp-wall') );
	
	// check if the fields are filled		
	if ( '' == $comment_author )
		wp_die(__('Error: please type a name.','wp-wall') );
	
	if ( '' == $comment_content )
		wp_die(__('Error: please type a comment.','wp-wall'));
		
	if ($options['clickable_links'])
	 $comment_content=WPWall_MakeClickable($comment_content);
	
	// insert the comment
//	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'user_ID');
	$commentdata = array(
    'comment_post_ID'      => $comment_post_ID,             
    'comment_author'       => $comment_author,
    'comment_author_email' => $comment_author_email,
    'comment_author_url'   => $comment_author_url,
    'comment_content'      => $comment_content,
    'comment_type'         => '',
    'comment_parent'       => 0,
);
	
	
	$comment_id = wp_new_comment( $commentdata );
		
	// check if the comment is approved
	$comment = wp_get_comment_status($comment_id);
	
	if ('approved' != $comment)
		wp_die( __('Your comment is awaiting moderation.','wp-wall'));
	
	// return status
	nocache_headers();
	die ( WPWall_ShowComments() );
}
else if ($refresh)
{
	nocache_headers();
	die ( WPWall_ShowComments($refresh) );
}

?>