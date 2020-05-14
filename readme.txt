=== WP Wall ===
Contributors: freediver, freeduke
Donate link: https://www.dukeyin.com/donate
Tags:  ajax, comments, posts, jquery, post, widget, sidebar, wall,
Requires at least: 2.3
Tested up to: 5.4.1
Stable tag: trunk

"Wall" widget that appears in your blog's sidebar acting as a cool shoutbox for your visitors. 


== Description == 

WP Wall is a "Wall" widget that appears in your blog's sidebar acting as a cool shoutbox for your visitors. 

The comment will appear in the sidebar immediately, without reloading the page.

All comments are internally handled by WordPress so that means you have normal comment moderation, SPAM protection and new comment notification.

WP Wall is fully customizable with a CSS file and included graphics.

WP Wall features:
* Global commenting "Wall"
* Comments are handled entirely by WordPress
* RSS Feed for the Wall
* Smilies
* Instant delete for admin
* Option to display HTML and make links clickable    
* Navigation through pages
* Interactive Wall: It can automatically refresh turning into a chat

Plugin by Vladimir Prelovac. Also check out <a href="https://managewp.com">ManageWP</a>.


== Changelog ==
= 2.0.0 =Compatible with latest wordpress 5.4.1
= 1.7.3 =
* WordPress 4.1 refresh and comaptibility


= 1.7.2 =
* Removed the credit link

= 1.7 = 
* WordPress 3.0+ approved

= 1.6 =
* Added gravatar image support for comments

= 1.5 =
* Fixed bug with if the blog was not in root directory (thanks Enrico Rossomando)
* Added a special recent comments widget which will not display Wall comments (thanks Enrico Rossomando!)
* Tested in 2.8

= 1.4.1 =
* Change the widget init from init to plugins_loaded hook

= 1.4 =
* Added option to expand post box by default
* Added option to reverse comment display order

== Installation ==

1. Upload the whole plugin folder to your /wp-content/plugins/ folder.
2. Go to the Plugins page and activate the plugin.
3. Use the Options page to change your options
4. Add the widget to your sidebar
5. Optionally add WP Wall recent comments to your sidebar. This will filter out wall comments from appearing on recent comments list


== Screenshots ==

1. WP Wall in Action 
2. WP Wall Admin Panel

== License ==

This file is part of WP Wall.

WP Wall is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WP Wall is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WP Wall. If not, see <http://www.gnu.org/licenses/>.


== Frequently Asked Questions ==

= How does it work? =

It creates a draft page in your blog under which all comments are stored. 

= What if I don't use widgets, how do I code this to appear in the sidebar?

Just put this code somewhere in your theme template, probably sidebar.php

<?php if (function_exists('WPWall_Widget')) WPWall_Widget(); ?>

= How does it combat SPAM ?

The same way your usual blog comments work. You can enable comment moderation. Or you can install one of the spam blocking plugins like Akismet.

= How do I change it into a shoutbox/chat?

Just set the refresh time to 5 seconds in the WP Wall options. The Wall becomes a chat-box :)

= Is it possible to display a all-ArchivePage to show all comments? 

Actually you will find WP Wall in your draft pages. Just edit it to your liking and - publish it! All comments will appear at the end of the page as usual.

= How do I add a border around the comment wall? 

Add this to #wallcomments section of css file:
	
	border: 1px solid #cccccc;
	padding 7px;

= How can I enable smilies?

WP Wall can show smilies using built in WordPress functionality if you enable it in your administration panel (Settings-> Writing-> Convert emoticons...). You can find more information about using smilies here <http://codex.wordpress.org/Using_Smilies>. 

= How did you solve the problem with the commenters email address? usually you have to enter an email address, now doing that in a chat/shoutbox is cumbersome

The plugin does not require one to enter their email address.

