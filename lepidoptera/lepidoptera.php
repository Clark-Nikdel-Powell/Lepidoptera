<?
/*
Plugin Name: Lepidoptera
Plugin URI: http://clarknikdelpowell.com/plugins/social-boxes
Description: Social media tools and functions.
Author: Josh Nederveld
Author URI: http://clarknikdelpowell.com
Version: 0.1

Copyright 2013  Josh Nederveld  (email : joshn@clarknikdelpowell.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

/** ===============================================================================
 *
 * Social Media in Settings
 * 
**/

add_action('admin_init', 'social_info');
function social_info() {
	
	add_settings_section( 'GENERAL_social', 'Social Media Info', 'GENERAL_social_callback', 'general' );
	
	add_settings_field( 'facebook_url', 'Facebook URL', 'facebook_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'twitter_handle', 'Twitter Handle', 'twitter_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'youtube_url', 'YouTube URL', 'youtube_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'vimeo_url', 'Vimeo URL', 'vimeo_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'flickr_url', 'Flickr URL', 'flickr_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'rss_url', 'RSS URL', 'rss_callback', 'general', 'GENERAL_social' );
	
	register_setting( 'general', 'facebook_url' );
	register_setting( 'general', 'twitter_handle' );
	register_setting( 'general', 'youtube_url' );
	register_setting( 'general', 'vimeo_url' );
	register_setting( 'general', 'flickr_url' );
	register_setting( 'general', 'rss_url' );
	
} // social_info()

function GENERAL_social_callback() { echo ''; }

function facebook_callback($args) {
	echo '<input type="text" id="facebook_url" name="facebook_url" value="'.get_option('facebook_url').'" style="width:70%" />';
}
function twitter_callback($args) {
	echo '@<input type="text" id="twitter_handle" name="twitter_handle" value="'.get_option('twitter_handle').'" style="width:70%" />';
}
function youtube_callback($args) {
	echo '<input type="text" id="youtube_url" name="youtube_url" value="'.get_option('youtube_url').'" style="width:70%" />';
}
function vimeo_callback($args) {
	echo '<input type="text" id="vimeo_url" name="vimeo_url" value="'.get_option('vimeo_url').'" style="width:70%" />';
}
function flickr_callback($args) {
	echo '<input type="text" id="flickr_url" name="flickr_url" value="'.get_option('flickr_url').'" style="width:70%" />';
}
function rss_callback($args) {
	echo '<input type="text" id="rss_url" name="rss_url" value="'.get_option('rss_url').'" style="width:70%" /><p class="description">Default &rarr; '.get_bloginfo('rss2_url').'</p>';
}

/** ===============================================================================
 *
 * Facebook Like Button
 * 
 * Arguments:
 *   'href'        : URL to like
 *   'send'        : 'true', 'false' : Send button
 *   'layout'      : 'standard', 'button_count', 'box_count' : Standard is crap, button count is small, box count is big
 *   'width'       : idk, this doesn't seem to do much..
 *   'show-faces'  : 'true', 'false' : Takes up too much space
 *   'font'        : 'arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'
 *   'colorscheme' : 'light', 'dark'
 *   'action'      : 'like', 'recommend'
 * 
**/

// pull in facebook javascript sdk only once
function FB_SDK() { include_once('fb-sdk.php'); }

// returns button
function LEPI_get_fb_button($args=0) {
	
	$defaults = array(
		'href'        => get_option('facebook_url')
	,	'send'        => 'false'
	,	'layout'      => 'button_count'
	,	'width'       => 100
	,	'show-faces'  => 'false'
	,	'font'        => ''
	,	'colorscheme' => 'light'
	,	'action'      => 'like'
	);
	$vars = wp_parse_args($args, $defaults);
	
	// build button div
	$button = '<div class="fb-like" data-href="'.$vars['href'].'" data-send="'.$vars['send'].'" data-width="'.$vars['width'].'" data-show-faces="'.$vars['show-faces'].'"';
	if ($vars['layout'] != 'standard') { $button .= ' data-layout="'.$vars['layout'].'"'; }
	if ($vars['font'] != $defaults['font']) { $button .= ' data-font="'.$vars['font'].'"'; }
	if ($vars['colorscheme'] != $defaults['colorscheme']) { $button .= ' data-colorscheme="'.$vars['colorscheme'].'"'; }
	if ($vars['action'] != $defaults['action']) { $button .= ' data-action="'.$vars['action'].'"'; }
	$button .= '></div>'.PHP_EOL;
	
	// put sdk in the footer, return button
	add_action('wp_footer', 'FB_SDK');
	return $button;
	
} // LEPI_get_fb_button()

// displays button
function LEPI_fb_button($args=0) {
	
	echo LEPI_get_fb_button($args);
	
} // LEPI_fb_button()

/** ===============================================================================
 *
 * Twitter Share Button
 * 
 * Arguments:
 *   'text'     : text of tweet
 *   'url'      : URL to share
 *   'hashtags' : list of hashtags separated by commas
 *   'via'      : reference a twitter handle
 *   'count'    : 'none', 'horizontal', 'vertical'
 * 
**/

// returns button
function LEPI_get_tw_button($args=0) {
	global $post;
	
	$defaults = array(
		'text'     => get_the_title($post->ID)
	,	'url'      => get_permalink($post->ID)
	,	'hashtags' => ''
	,	'via'      => get_option('twitter_handle')
	,	'count'    => 'horizontal'
	);
	$vars = wp_parse_args($args, $defaults);
	
	// build button div
	$button = '<a href="https://twitter.com/share" class="twitter-share-button" data-text="'.$vars['text'].'"';
	if ($vars['url']) { $button .= ' data-url="'.$vars['url'].'"'; }
	if ($vars['hashtags']) { $button .= ' data-hashtags="'.$vars['hashtags'].'"'; }
	if ($vars['via']) { $button .= ' data-via="'.$vars['via'].'"'; }
	if ($vars['count'] != $defaults['count']) { $button .= ' data-count="'.$vars['count'].'"'; }
	$button .= '>Tweet</a>';
	$button .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>'.PHP_EOL;
	
	// return button
	return $button;
	
} // LEPI_get_tw_button()

// displays button
function LEPI_tw_button($args=0) {
	
	echo LEPI_get_tw_button($args);
	
} // LEPI_tw_button()

/*
===============================================================================

  Site's Social Media Boxes

===============================================================================
*/

function social_boxes($args = array()) {
	global $post;

	$defaults = array(
		'ID'				=> $post->ID,
		'fb_btn_format' 	=> 'button_count',
		'fb_btn_width' 		=> "300",
		'fb_btn_faces' 		=> false,
		'gp_btn_size'       => 'medium',
		'gp_btn_annotation' => 'bubble',
		'gp_btn_width'		=> '300',
		'fb_box_width'		=> '292',
		'fb_box_height'		=>	'220',
		'fb_box_faces'			=> false,
		'fb_box_stream'		=> false,		
		'fb_box_header'		=> true,
		'fb_box_border'		=> '#AAAAAA',
		'twitter_handle'		=> get_option('twitter_handle'),
		'youtube_url'			=> get_option('youtube_url'),
		'facebook_url'			=> get_option('facebook_url'),
		'gplus_url'				=> get_option('gplus_url')
	);
	
	/* 
	Quick Notes
	-----------
	
	1. Facebook button formats: button_count or box_count
	2. Google+ button options: 
			Size: small medium standard tall
			Annotation: none bubble inline
		https://developers.google.com/+/plugins/+1button/
	
	*/
	
	$args = wp_parse_args( $args, $defaults );
	
	// Get URLs, Handles, Links
	$gp_url 		= 	$args['gplus_url'];
	$fb_url 		= 	$args['facebook_url'];
	$tw_handle 	= 	$args['twitter_handle'];
	$yt_url 		= 	$args['youtube_url'];
	$page_url 	=  get_permalink($args['ID']);

	if (is_home()) {$page_url = WP_SITEURL;}
	
	// Boot Up the Javascript!
	$social_data['js']['facebook'] = '<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, "script", "facebook-jssdk"));</script>';

	$social_data['js']['google'] = '<script type="text/javascript">
	  (function() {
	    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
	    po.src = "https://apis.google.com/js/plusone.js";
	    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>';


	// Set up some buttons	
	$social_data['button']['facebook'] = '<div class="fb-like" data-href="'.$fb_url.'" data-send="false" data-layout="'.$args['fb_btn_format'].'" data-width="'.$args['fb_btn_width'].'" data-show-faces="'.$args['fb_btn_faces'].'"></div>';
	$social_data['button']['twitter'] = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$page_url.'" data-dnt="true">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	$social_data['button']['gplus'] = '<div class="g-plusone" data-size="'.$args['gp_btn_size'].'" data-annotation="'.$args['gp_btn_annotation'].'" data-width="'.$args['gp_btn_width'].'"></div>';
	
	
	// Facebook Like Box
	$social_data['box']['facebook'] = '<div class="fb-like-box" data-href="'.$fb_url.'" data-width="'.$args['fb_box_width'].'" data-height="'.$args['fb_box_height'].'" data-show-faces="'.$args['fb_box_faces'].'" data-stream="'.$args['fb_box_stream'].'" data-header="'.$args['fb_box_header'].'" data-border-color="'.$args['fb_box_border'].'"></div>';
	
	
	// Ye Mighty Twitter Widget Data, now with more caching!
	
	// Display tweets from the cache.
	$cached_tweets = get_transient( $tw_handle.'_twitter_search_results' );
	
	if ($cached_tweets) {
	
		$social_data['tweets'] = $cached_tweets;
	
	} else {
	
	// Cache file not found, or old. Fetch recent tweets from Twitter.
	$date_limit = date('Y-m-d', strtotime('9 days ago'));
	$tweets_result = file_get_contents('http://search.twitter.com/search.json?q=from:'.$tw_handle.'%20since:'.$date_limit);
	$social_data['tweets'] = json_decode($tweets_result);
	
	set_transient( $tw_handle.'_twitter_search_results', $social_data['tweets'], 1 * HOUR_IN_SECONDS );
	
	}
	
	// Links
	$social_data['link']['youtube'] = '<a class="youtube" href="'.$yt_url.'">Youtube</a>';	
	
	return $social_data;

}

function get_tweets($number, $handle) {
	$ancestor = highest_ancestor();

	//Get tweets, condense variable
	$social_data = social_boxes('twitter_handle='.$handle);
	$tweets = $social_data['tweets']->results;
	
	// Set up a count based on user input
	$count = $number-1;
	if (count($tweets) < $count) {$count = count($tweets)-1;}
	for ($i = 0; $i <= $count; $i++) {
	
	if ($tweets[$i]) {
	
		$reply_filter = strpos($tweets[$i]->text, '@', 0);
		$rt_filter = strpos($tweets[$i]->text, 'RT', 0);
	
			if (($reply_filter !== 0 && $rt_filter !== 0) || ($reply_filter === false && $rt_filter === false)) {
	
			$tweet = $tweets[$i]->text;
	
			// Add hyperlink html tags to any urls, twitter ids or hashtags in the tweet.
			$tweet = preg_replace('/(\.\.\.+)/', '…', $tweet);
			$tweet = preg_replace('/(https?:\/\/[^\s"<>…]+)/','<a href="$1">$1</a>', $tweet);
			$tweet = preg_replace('/(^|[\n\s])@([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/$2">@$2</a>', $tweet);
			$tweet = preg_replace('/(^|[\n\s])#([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/search?q=%23$2">#$2</a>', $tweet);

			// Store timestamp and formatted tweet
			$tweet_data[$i]['timestamp'] = strtotime($tweets[$i]->created_at);	
			$tweet_data[$i]['text'] = $tweet;
			
			} elseif ($count + 1 < count($tweets)) {$count++;}
		} //endif
	} //endfor
	
	return $tweet_data;

}

