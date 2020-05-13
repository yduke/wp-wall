<?php

/*
 * Plugin Name: WP Wall
 * Version: 2.0
 * Description: "Wall" widget that appears in your blog's side bar. Readers can add a quick comment about the blog as a whole, and the comment will appear in the sidebar immediately, without reloading the page.
 * Author: Vladimir Prelovac
 * Author URI: http://www.prelovac.com/vladimir
 * Plugin URI: http://www.prelovac.com/vladimir/wordpress-plugins/wp-wall
 * Text Domain: wp-wall
 * Domain Path: /languages
*/


/*  
Copyright 2008  Vladimir Prelovac  (email : vprelovac@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('WPINC')) {
    die;
}
/* Load plugin textdomain. */ 
function plugin_load_textdomain() { load_plugin_textdomain( 'wp-wall', false, basename( dirname( __FILE__ ) ) . '/languages/' ); } 
add_action( 'init', 'plugin_load_textdomain' );


function wp_wall_init() {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'wp-wall', false, $plugin_dir );
}
add_action('plugins_loaded', 'wp_wall_init');


global $wp_version;	

$exit_msg='WP Wall requires WordPress 2.3 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';

if (version_compare($wp_version,"2.3","<"))
{
	exit ($exit_msg);
}

$wp_wall_plugin_url = trailingslashit(plugins_url(null, __FILE__)); 

function WPWall_WidgetControl() {
	
	// get saved options
	$options = WPWall_GetOptions();
	
	// handle user input
	if ( $_POST["wall_submit"] ) {
		$options['wall_title'] = strip_tags( stripslashes( $_POST["wall_title"] ) );
		
		update_option('wp_wall', $options);
	}
	
	$title = $options['wall_title'];
	
	// print out the widget control		
	include('wp-wall-widget-control.php');
}

                                                                               
function WPWall_Widget($args = array() ) {	
	
	global  $user_ID,  $user_identity, $wp_wall_plugin_url, $wpdb;		
	
	// extract the parameters
	extract($args);
	
	// get our options
	$options=WPWall_GetOptions();	

	extract($options);
	
	// include our widget
	include('wp-wall-widget.php');
	
		
}

function WPWall_Init() {

	// register widget
//	register_sidebar_widget('WP Wall', 'WPWall_Widget');	

	// alternative way
$widget_options = array('classname' => 'WPWall_Widget', 'description' => "A comment 'Wall' for your sidebar." );
wp_register_sidebar_widget('WP Wall', 'WP Wall', 'WPWall_Widget', $widget_options);

	// register widget control
	wp_register_widget_control(395, 'WP Wall', 'WPWall_WidgetControl', array());

	$options = WPWall_GetOptions();
	
	// get our wall pageId
	$pageId=$options['pageId'];
	
	// check if the actual post exists
	$actual_post=get_post($pageId);
	
	// check if the page is already created	
	if (!$pageId || !$actual_post || ($pageId!=$actual_post->ID) )
	{
		// create the page and save it's ID
		$options['pageId']=	WPWall_CreatePage();
		
		update_option('wp_wall', $options);
	}			
}

add_action('init', 'WPWall_Init');

function WPWall_CreatePage() {

	// create post object
	class mypost {
		var $post_title;
		var $post_content;
		var $post_status; // draft, published
		var $post_type; // can be 'page' or 'post' 
		var $comment_status; // open or closed for commenting
	}
	
	// initialize the post object
	$mypost = new mypost();
	
	// fill it with data
	$mypost->post_title = 'WP Wall Guestbook';
	$mypost->post_content =  'Welcome to my <a href="http://www.prelovac.com/vladimir/wordpress-plugins/wp-wall">WP Wall</a> Guestbook!';
	$mypost->post_status = 'draft';
	$mypost->post_type = 'page';
	$mypost->comment_status = 'open';
	
	// insert the post and return it's ID
	return wp_insert_post($mypost);
}

add_action('wp_head', 'WPWall_HeadAction' );

function WPWall_HeadAction()
{
	global $wp_wall_plugin_url;
	
	echo '<link rel="stylesheet" href="'.$wp_wall_plugin_url.'wp-wall.css" type="text/css" />'; 
}

add_action('wp_print_scripts', 'WPWall_ScriptsAction');

function WPWall_ScriptsAction() 
{
	if (!is_admin())
	{
		global $wp_wall_plugin_url;

		$options = WPWall_GetOptions();	
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('wp_wall_script', $wp_wall_plugin_url.'wp-wall.js', array('jquery', 'jquery-form')); 
		wp_localize_script( 'wp_wall_script', 'WPWallSettings', array(
        'refreshtime' => $options['refresh_time'] * 1000,
        'expand_box' => $options['expand_box'],
		'del_comfirm' => __('Are you sure you want to delete this comment?','wp-wall'),
		'thanks_message' => __('Thank you for your comment!','wp-wall'),
		'err_message' => __('An error occurred, please notify the administrator.','wp-wall'),
		'required_message' => __('Please fill in the required fields.','wp-wall')
      ) );

	}
}

function WPWall_ShowComments($page = 1) {
	global $wpdb, $wp_wall_plugin_url;

	// get our page id	
	$options = WPWall_GetOptions();					
	$pageId=$options['pageId'];	
	
	// number of comments to display
	if ( !$number = (int) $options['wall_comments'] )
		$number = 5;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 25 )
		$number = 25;
		
	$wordwrap=$options['wordwrap'];	
	
	$result='';


	// get comments from WordPress database	
	$count = $wpdb->get_var("
											SELECT COUNT(*)
											FROM $wpdb->comments 
											WHERE comment_approved = '1' AND comment_post_ID=$pageId AND NOT (comment_type = 'pingback' OR comment_type = 'trackback')											
										");			
	
	if ($count > $number)
	{
			$nav=1;
			$pages=ceil($count/$number);
	}
	else {
		$nav=0;
		$pages=0;
	}
	
		
	$getnumber=$number*$page;
										
	// get comments from WordPress database	
	$comments = $wpdb->get_results("
											SELECT *
											FROM $wpdb->comments 
											WHERE comment_approved = '1' AND comment_post_ID=$pageId AND NOT (comment_type = 'pingback' OR comment_type = 'trackback')
											ORDER BY comment_date_gmt DESC 
											LIMIT $getnumber
										");										
	
	
	$comments=array_slice($comments, $getnumber-$number, $number);
	if ( $comments ) {  
	
		$count=1;
		$oddcomment='';
		
		 if (  $options['latest'] ) 
		 {				
				$latest_comment=date($options['latest'], strtotime($comments[0]->comment_date));
 				$result='<div id="wp_latest">' . __('Most Recent: ', 'wp-wall') . $latest_comment. '</div>' . $result;
 			}
	
		
		// display comments one by one
		if ($options['reverse_order']=='on')
			$comments=array_reverse($comments);
		
		foreach ($comments as $comment) {			
			//$comment->comment_content = convert_smilies(WPWall_GetExcerpt($comment->comment_content, $options['wall_wrap'])); ($comment->comment_content);//apply_filters('convert_smilies', $comment->comment_content );
			if ( current_user_can( 'edit_post', $comment->comment_post_ID ) ) 
				$deleteurl= '<a href="javascript:verify(\''.esc_url( wp_nonce_url(get_bloginfo('wpurl')."/wp-admin/comment.php?action=deletecomment".( $options['delete_spam'] ? "&dt=spam" : "")."&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) ).'\', \'\')">'.htmlspecialchars  ($comment->comment_author).'</a>'; 
			else
				$deleteurl=htmlspecialchars  ($comment->comment_author);
		
			//$author_data=get_userdatabylogin();
			$author_data=get_user_by('login', $comment->comment_author) ;
									
			if ($author_data)							
				$highlight=$author_data->wp_capabilities['administrator']==1 ? "wall-admin" : "wall-registered";			
			else 
				$highlight="";	
			
			$result.= '<p class="wall-'.$count.$oddcomment.' '.$highlight.'">'.WPWall_Gravatar($comment).'<span class="wallauthor" title="'.htmlspecialchars($comment->comment_author).' on '.$comment->comment_date.'" >' . $deleteurl.'</span><span class="wallcomment" title="'.htmlspecialchars(strip_tags($comment->comment_content)).'" >: '.
			convert_smilies(WPWall_OneWordwrap(WPWall_GetExcerpt($comment->comment_content, $options['wall_wrap']), $wordwrap)).'</span></p>';
			$count++;
			if ($oddcomment == ' wall-alt') $oddcomment = '';
			else $oddcomment = ' wall-alt';

		}			
	}
		
		$result.='<input type="hidden" name="wallpage" id="wallpage" value="'.$page.'" />';
		
		$result.='<input type="hidden" name="page_left" id="page_left" value="'.(($page > 1) ? ($page-1) : $page).'" />';

		$result.='<input type="hidden" name="page_right" id="page_right" value="'.(($page < $pages) ? ($page+1) : $page).'" />';
		
		 
 

	return $result;
}

function WPWall_GetOptions()
{
	
 $options = array(
	 'wall_title' => 'Wall',
	 'wall_reply' => 'Leave a reply',
	 'wall_comments' => 5,
	 'wall_wrap' => 25,
	 'disable_new' => '',
	 'only_registered' => '', 
	 'refresh_time' => 0,
	 'rss_feed' => '', 
	 'pageId' => '', 
	 'delete_spam' => '', 
	 'show_all' => '',
	 'latest' => 'D, h:i a',
	 'clickable_links' => '',
	 'allow_html' => '',
	 'show_email' => '',
	 'wordwrap' => 0,
	 'expand_box' => '',
	 'reverse_order' => '',
	 'gravatar' => ''
	 );
  
 $saved = get_option('wp_wall');
 
 
 if (!empty($saved)) {
	 foreach ($saved as $key => $option)
 			$options[$key] = $option;
 }
	
 if ($saved != $options)	
 	update_option('wp_wall', $options);
 	
 return $options;
}

add_action('admin_menu', 'WPWall_AdminMenu');

	// Hook the options mage
function WPWall_AdminMenu() {
	add_options_page('WP Wall Options', 'WP Wall ', 'manage_options', basename(__FILE__),'WPWall_Options');	
} 


function WPWall_MakeClickable($text)
{
$ret = ' ' . $text;

$ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-:=?@\[\]+]*)#is", "\\1<a href=\"\\2\">\\2</a>", $ret);
$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-:=?@\[\]+]*)#is", "\\1<a href=\"http://\\2\">\\2</a>", $ret);
$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

$ret = substr($ret, 1);
return $ret;
} 

function WPWall_Options()
{
	global $wp_wall_plugin_url;
	$options = WPWall_GetOptions();
	
	if ( isset($_POST['submitted']) ) {
		check_admin_referer('wp-wall');
		
		//print_r($_POST);
			
		$options['disable_new']=$_POST['disable_new'];					
		$options['only_registered']=$_POST['only_registered'];		
		$options['wall_title']=strip_tags( stripslashes($_POST['wall_title']));		
		$options['wall_reply']=strip_tags( stripslashes($_POST['wall_reply']));		
		$options['wall_comments']=(int) ($_POST['wall_comments']);		
		$options['wall_wrap']=(int) ($_POST['wall_wrap']);	
		$options['wordwrap']=(int) ($_POST['wordwrap']);	
		$options['refresh_time']=(int) ($_POST['refresh_time']);
		$options['rss_feed']=$_POST['rss_feed'];							
		$options['delete_spam']=$_POST['delete_spam'];	
		$options['show_all']=$_POST['show_all'];	
		$options['clickable_links']=$_POST['clickable_links'];	
		$options['allow_html']=$_POST['allow_html'];	
		$options['show_email']=$_POST['show_email'];	
		$options['latest']=trim($_POST['latest']);	
		$options['expand_box']=$_POST['expand_box'];	
		$options['reverse_order']=$_POST['reverse_order'];	
		$options['gravatar']=$_POST['gravatar'];	
		
		if (!empty($_POST['pageId']))
			$options['pageId']=(int) ($_POST['pageId']);	
		
	
		update_option('wp_wall', $options);
		echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
	}

	
	$action_url = $_SERVER['REQUEST_URI'];	

	$disable_new=$options['disable_new']=='on'?'checked':'';
	$only_registered=$options['only_registered']=='on'?'checked':'';
	$rss_feed=$options['rss_feed']=='on'?'checked':'';
	$delete_spam=$options['delete_spam']=='on'?'checked':'';
	$show_all=$options['show_all']=='on'?'checked':'';
	$clickable_links=$options['clickable_links']=='on'?'checked':'';
	$allow_html=$options['allow_html']=='on'?'checked':'';
	$show_email=$options['show_email']=='on'?'checked':'';
	$expand_box=$options['expand_box']=='on'?'checked':'';
	$gravatar=$options['gravatar']=='on'?'checked':'';
	$reverse_order=$options['reverse_order']=='on'?'checked':'';
	
	$wall_title=$options['wall_title'];
	$wall_comments=$options['wall_comments'];
	$wall_reply=$options['wall_reply'];
	$wall_wrap=$options['wall_wrap'];
	$wordwrap=$options['wordwrap'];
	$refresh_time=$options['refresh_time'];
	$latest=$options['latest'];
	
	$pageId=$options['pageId'];
			
	$imgpath=$wp_wall_plugin_url.'/i';	
	$nonce=wp_create_nonce('wp-wall');
	
	echo <<<END

<div class="wrap" style="">
	<h2>WP Wall</h2>
				
	<div id="poststuff" style="margin-top:10px;">
	

	
	 <div id="mainblock" style="width:710px">
	 
		<div class="dbx-content">
		 	<form name="wpwallform" action="$action_url" method="post">
					<input type="hidden" name="submitted" value="1" /> 
					<input type="hidden" id="_wpnonce" name="_wpnonce" value="$nonce" />
					<h2> General Options</h2>
					<p>WP Wall allows you to have a comments wall on your blog.</p>						
					
					<input type="text" name="wall_title" size="15" value="$wall_title"/>
					<label for="wall_title">Title for the widget</label> <br /><br />			
					
					<input type="text" name="wall_reply" size="15" value="$wall_reply"/>
					<label for="wall_reply">Leave a reply text</label> <br /><br />	
									
					<input type="text" name="wall_comments" size="15" value="$wall_comments"/>
					<label for="wall_comments">Number of comments to show (max 25)</label> <br /><br />								
					
					<input type="checkbox" name="show_all"  $show_all/><label for="show_all"> Show 'All' link - Shows the link to your wall guestbook page. You need to publish your Wall page (<a href="page.php?action=edit&post=$pageId">go there</a>). If you do not see any comments, your theme does not show comments on pages and needs to be modified</label>  <br />
					<br />
					<input type="checkbox" name="show_email"  $show_email/><label for="show_email"> Show email field in the form</label>  <br />
					<br />
					<input type="checkbox" name="gravatar"  $gravatar/><label for="gravatar"> Show gravatar images (requires email field option checked) </label>  <br />
					<br />
					<input type="checkbox" name="allow_html"  $allow_html/><label for="allow_html"> Allow HTML in comments (use with CAUTION)</label>  <br />
					&nbsp;&nbsp;&nbsp;<input type="checkbox" name="clickable_links"  $clickable_links/><label for="clickable_links"> Make links clickable ('www.prelovac.com' would become clickable link)</label>  <br />
					<br />
					<input type="checkbox" name="expand_box"  $expand_box/><label for="expand_box"> Show post comment box expanded by default </label>  <br />
					<br />
					
					<input type="checkbox" name="reverse_order"  $reverse_order/><label for="reverse_order"> Reverse order of displayed comments</label>  <br />
					
					
					<h2>Comments</h2>
					<input type="checkbox" name="disable_new"  $disable_new/><label for="disable_new"> Disable new comments</label>  <br />
					<input type="checkbox" name="only_registered"  $only_registered/><label for="only_registered"> Only registered users can post</label> <br />
					<input type="checkbox" name="delete_spam"  $delete_spam/><label for="delete_spam"> Treat admin deleted comments as spam</label> <br /> <br/>
					<input type="text" name="latest" size="15" value="$latest"/>
					<label for="latest">If you wish to show time of latest comment, type in <a href="http://www.php.net/date">date format</a>. D, H:i will do for start.</label> <br />
					
					<h2>RSS Feed</h2>
					
					<input type="checkbox" name="rss_feed"  $rss_feed/><label for="rss_feed"> Show Wall's RSS feed link</label> <br />
					
					
					<h2>Interactive Wall</h2>	
					<p>You may specify a refresh time in seconds for automatic (Ajax) reload of the Wall. Turning this feature can affect server performance on a very busy blog. Do not set to less then 5 seconds.</p><p>Set to 0 to turn this feature off.</p>
					
					<input type="text" name="refresh_time" size="15" value="$refresh_time"/>
					<label for="refresh_time">Refresh time</label>  <br />																								
							
					<h2>Wordwrap</h2>
					<p>WP Wall can automatically wrap long words so they do not break your output.</p>
					<p>Set to 0 to turn off this feature. </p> 
					
					<input type="text" name="wordwrap" size="15" value="$wordwrap"/>
					<label for="wordwrap">Wrap long words after this many characters</label> <br /><br />		
					
					<h2>Excerpts</h2>
					<p>If comments are too long you can choose to display a comment excerpt instead. The whole comment is still available when the user hovers the mouse over it (tooltip).</p>
					<p>Set to 0 to turn off this feature. Default is 25.</p> 
					
					<input type="text" name="wall_wrap" size="15" value="$wall_wrap"/>
					<label for="wall_wrap">Excerpt length in words</label> <br />
		
					<h2>Appearance</h2>	
					<p>You can style the look of your Wall by editing wp-wall.css file of the plugin. Be sure to make backup if you upgrade to newer version. </p>
					
					<h2>Smilies</h2>
					<p>WP Wall can show smilies using built in WordPress functionality if you enable it in your administration panel (Settings-> Writing-> Convert emoticons...). You can find more information about using smilies <a href="http://codex.wordpress.org/Using_Smilies">here</a>.</p>
				
					
					<h2>Advanced</h2>
					<p>Do not touch this if you do not know what are you doing.</p>		
					
					<input type="text" name="pageId" size="15" value="$pageId"/>
					<label for="pageId">Wall Page ID</label> <br />
				
					<div class="submit"><input class="button button-primary" type="submit" name="Submit" value="Update options" /></div>
						</form>
					</div>
					
	 </div>

	</div>
	
</div>
END;
}

function WPWall_OneWordwrap($string,$width){
	
	if (!$width)
		return $string;
		
  $s=explode(" ", $string);
  foreach ($s as $k=>$v) {
    $cnt=strlen($v);
    if($cnt>$width) $v=wordwrap($v, $width, " ", true);
      $new_string.="$v ";
  }
  return $new_string;
}

function WPWall_GetExcerpt($text, $length = 25)
{
		if (!$length)
			return $text;
		
		$options=WPWall_GetOptions();
			
		if ($options['allow_html']=='')
			$text = strip_tags($text);	
				
		$words = mb_strlen( $text, 'utf-8' );
		if ($words > $length) {
			$text = mb_substr($text,0,$length,'utf-8');
			$text = $text . '...';
		}	
		return $text;
}

function WPWall_Gravatar($comment)
{ 
	$options=WPWall_GetOptions();
	
	if (!$options['gravatar'])
		return '';
		
	$return= '<span class="wall-gravatar">';
	$size=25;
	$email=strtolower(trim($comment->comment_author_email));
	$rating = "G"; 
	   if (function_exists('get_avatar')) {
      $return.= get_avatar($email, $size);
   } else {      
      $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=
         " . md5($emaill) . "&size=" . $size."&rating=".$rating;
      $return.= "<img src='$grav_url'/>";
   }
   $return.= '</span>';
   
   return $return;
}
//require_once("recent-comments-widget.php");
?>