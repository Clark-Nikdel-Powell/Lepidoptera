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

	// General Social Media Settings
	add_settings_section( 'GENERAL_social', 'Social Media Info', 'GENERAL_social_callback', 'general' );

	add_settings_field( 'facebook_url', 'Facebook URL', 'facebook_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'gplus_url', 'Google+ URL', 'gplus_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'youtube_url', 'YouTube URL', 'youtube_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'vimeo_url', 'Vimeo URL', 'vimeo_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'flickr_url', 'Flickr URL', 'flickr_callback', 'general', 'GENERAL_social' );
	add_settings_field( 'rss_url', 'RSS URL', 'rss_callback', 'general', 'GENERAL_social' );

	register_setting( 'general', 'facebook_url' );
	register_setting( 'general', 'gplus_url' );
	register_setting( 'general', 'youtube_url' );
	register_setting( 'general', 'vimeo_url' );
	register_setting( 'general', 'flickr_url' );
	register_setting( 'general', 'rss_url' );

	// Twitter Settings
	add_settings_section( 'GENERAL_twitter', 'Twitter Settings', 'GENERAL_twitter_callback', 'general' );

	add_settings_field( 'twitter_handle',  		   'Twitter Handle', 		'twitter_handle_callback', 			'general', 'GENERAL_twitter' );
	add_settings_field( 'tw_consumer_key', 			'Consumer Key', 			'tw_consumer_key_callback', 			'general', 'GENERAL_twitter' );
	add_settings_field( 'tw_consumer_secret', 		'Consumer Secret', 		'tw_consumer_secret_callback', 		'general', 'GENERAL_twitter' );
	add_settings_field( 'tw_access_token', 			'Access Token', 			'tw_access_token_callback', 			'general', 'GENERAL_twitter' );
	add_settings_field( 'tw_access_token_secret', 	'Access Token Secret', 	'tw_access_token_secret_callback', 	'general', 'GENERAL_twitter' );

	register_setting( 'general', 'twitter_handle' );
	register_setting( 'general', 'tw_consumer_key' );
	register_setting( 'general', 'tw_consumer_secret' );
	register_setting( 'general', 'tw_access_token' );
	register_setting( 'general', 'tw_access_token_secret' );

} // social_info()

function GENERAL_social_callback() { echo ''; }

function facebook_callback($args) {
	echo '<input type="text" id="facebook_url" name="facebook_url" value="'.get_option('facebook_url').'" style="width:70%" />';
}
function gplus_callback($args) {
	echo '<input type="text" id="gplus_url" name="gplus_url" value="'.get_option('gplus_url').'" style="width:70%" />';
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

// Twitter Settings
function GENERAL_twitter_callback() { echo ''; }

function twitter_handle_callback($args) {
	echo '@<input type="text" id="twitter_handle" name="twitter_handle" value="'.get_option('twitter_handle').'" style="width:70%" />';
}
function tw_consumer_key_callback($args) {
	echo '<input type="text" id="tw_consumer_key" name="tw_consumer_key" value="'.get_option('tw_consumer_key').'" style="width:70%" />';
}
function tw_consumer_secret_callback($args) {
	echo '<input type="text" id="tw_consumer_secret" name="tw_consumer_secret" value="'.get_option('tw_consumer_secret').'" style="width:70%" />';
}
function tw_access_token_callback($args) {
	echo '<input type="text" id="tw_access_token" name="tw_access_token" value="'.get_option('tw_access_token').'" style="width:70%" />';
}
function tw_access_token_secret_callback($args) {
	echo '<input type="text" id="tw_access_token_secret" name="tw_access_token_secret" value="'.get_option('tw_access_token_secret').'" style="width:70%" />';
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
 * Google +1 Button
 *
 * Arguments:
 *   'href'        : URL to like
 *   'size'        : 'small', 'medium', 'standard', 'tall'
 *
**/

// pull in google javascript sdk only once
function GP_SDK() { include_once('gp-sdk.php'); }

// returns button
function LEPI_get_gp_button($args=0) {

	$defaults = array(
		'href'        => get_option('gplus_url')
	,	'size'        => 'medium'
	);
	$vars = wp_parse_args($args, $defaults);

	// build button div
	$button = '<g:plusone size="'. $vars[ 'size' ] .'"></g:plusone>';

	// put sdk in the footer, return button
	add_action('wp_footer', 'GP_SDK');
	return $button;

} // LEPI_get_fb_button()

// displays button
function LEPI_gp_button($args=0) {

	echo LEPI_get_gp_button($args);

} // LEPI_gp_button()


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
	if ($vars['via']) { $button .= ' data-via="'.$vars['via'].'" '; }
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
 * Get tweets with Twitter's REST API, now with more OAuth!
 *
 * @param int    $max_tweets    Number of tweets to return
 * @param string $twitter_id Twitter handle.. duh
 *
**/

function LEPI_get_tweets($max_tweets, $twitter_id) {

	require_once 'lib/twitter/tmhOAuth.php';

	$access_code = get_option('tw_access_token_secret');

	if ($access_code != '') {

	$tmhOAuth = new tmhOAuth(array(
	'consumer_key'    => get_option('tw_consumer_key'),
	'consumer_secret' => get_option('tw_consumer_secret'),
	'user_token'      => get_option('tw_access_token'),
	'user_secret'     => get_option('tw_access_token_secret')
	));

	// Build the request. Full param list here: https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
	// Because retweets and replies are filtered out AFTER getting tweets, the default count is $max_tweets plus 20,
	// (for good measure) and is pared down in the foreach() loop below.
	$code    = $tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/user_timeline'), array(
	  'screen_name' => $twitter_id,
	  'count' => $max_tweets+20,
	  'include_rts' => false,
	  'include_entities' => false,
	  'exclude_replies'	=> true
	));

	$transient_name = $twitter_id.'_twitter_search_results';
	$tweets_raw = get_transient($transient_name);

	if ($tweets_raw === false) { //if there is no cached file
	  $tweets_raw = json_decode($tmhOAuth->response['response'], true);
	  set_transient($transient_name, $tweets_raw, 1 * HOUR_IN_SECONDS);
	  $set_tweets = true;
	}
	if (isset($set_tweets)) { //parse the feed just once as it will be cached from now on
	  $tweets_raw = json_decode($tmhOAuth->response['response'], true);
	}

	$limit = 0;

	foreach ($tweets_raw as $tweet) {

		if ($limit < $max_tweets) {

			$tweet_text = $tweet['text'];

			// Add hyperlink html tags to any urls, twitter ids or hashtags in the tweet.
			$tweet_text = preg_replace('/(\.\.\.+)/', '…', $tweet_text);
			$tweet_text = preg_replace('/(https?:\/\/[^\s"<>…]+)/','<a href="$1">$1</a>', $tweet_text);
			$tweet_text = preg_replace('/(^|[\n\s])@([^\s"<>…\t\n\r<:]*)/is', '$1<a href="http://twitter.com/$2">@$2</a>', $tweet_text);
			$tweet_text = preg_replace('/(^|[\n\s])#([^\s"<>…\t\n\r<:]*)/is', '$1<a href="http://twitter.com/search?q=%23$2">#$2</a>', $tweet_text);

			// Time zone offsets
			$timestamp   = new DateTime($tweet['created_at']);
			$offset = get_option('gmt_offset');
			$processed_time = $timestamp->format('U') + ($offset*3600);
			$datetime = new DateTime();
			$datetime->setTimestamp($processed_time);

			$tweets[] = array(
				'text'      => $tweet_text
			,	'timestamp' => $processed_time
			);
		}

		$limit++;

	}

	return array_slice($tweets, 0, $tweet_count);

	}

} // LEPI_get_tweets()

?>