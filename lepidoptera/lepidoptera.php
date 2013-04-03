<?php
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
	$button = '<div class="fb-like" data-href="'.urlencode($vars['href']).'" data-send="'.$vars['send'].'" data-width="'.$vars['width'].'" data-show-faces="'.$vars['show-faces'].'"';
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
	if ($vars['url']) { $button .= ' data-url="'.urlencode($vars['url']).'"'; }
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

/** ===============================================================================
 *
 * Pinterest Pin Button
 * 
 * Arguments:
 *   'url'         : URL of pin
 *   'media'       : URL to image to pin
 *   'description' : Text attached to pin
 *   'pin-config'  : 'beside', 'above', 'none'
 * 
**/

// returns button
function LEPI_get_pin_button($args=0) {
	global $post;
	
	$defaults = array(
		'url'         => get_permalink($post->ID)
	,	'media'       => wp_get_attachment_url(get_post_thumbnail_id($post->ID))
	,	'description' => get_the_title($post->ID)
	,	'pin-config'  => 'beside'
	);
	$vars = wp_parse_args($args, $defaults);
	
	// build button div
	$button = '<a href="//pinterest.com/pin/create/button/?url='.urlencode($vars['url']).'&media='.$vars['media'].'&description='.$vars['description'].'" data-pin-config="'.$vars['pin-config'].'" data-pin-do="buttonPin" ><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>'.PHP_EOL;
	
	// put js in the footer, return button
	wp_enqueue_script('pinterest', '//assets.pinterest.com/js/pinit.js', array(), false, true);
	return $button;
	
} // LEPI_get_pin_button()

// displays button
function LEPI_pin_button($args=0) {
	
	echo LEPI_get_pin_button($args);
	
} // LEPI_pin_button()

/** ===============================================================================
 *
 * Get tweets with Twitter's search API
 * 
 * @param int    $tweet_count    Number of tweets to return
 * @param string $twitter_handle Twitter handle.. duh
 * 
**/

function LEPI_get_tweets($tweet_count, $twitter_handle) {
	
	$transient_name = $twitter_handle.'_twitter_search_results';
	
	$tweets = get_transient($transient_name);
	
	if (!$tweets) {
			
		$date_limit = date('Y-m-d', strtotime('10 days ago'));
		$tweets_search = file_get_contents('http://search.twitter.com/search.json?q=from:'.$twitter_handle.'%20since:'.$date_limit.'%20exclude:retweets%20exclude:replies&rpp=10&result_type=recent');
		$tweets_search = json_decode($tweets_search);
		
		$tweets = array();
		foreach ($tweets_search->results as $tweet) {
			$tweet_text = $tweet->text;
	
			// Add hyperlink html tags to any urls, twitter ids or hashtags in the tweet.
			$tweet_text = preg_replace('/(\.\.\.+)/', '…', $tweet_text);
			$tweet_text = preg_replace('/(https?:\/\/[^\s"<>…]+)/','<a href="$1">$1</a>', $tweet_text);
			$tweet_text = preg_replace('/(^|[\n\s])@([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/$2">@$2</a>', $tweet_text);
			$tweet_text = preg_replace('/(^|[\n\s])#([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/search?q=%23$2">#$2</a>', $tweet_text);
			
			$tweets[] = array(
				'text'      => $tweet_text
			,	'timestamp' => strtotime($tweet->created_at)
			);
		}
		
		set_transient($transient_name, $tweets, 1 * HOUR_IN_SECONDS);
		
	}
	return array_slice($tweets,	0, $tweet_count);

} // LEPI_get_tweets()

