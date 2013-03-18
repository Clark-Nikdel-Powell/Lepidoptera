<?

/*

Plugin Name: CNP Social Boxes
Plugin URI: http://clarknikdelpowell.com/plugins/social-boxes
Description: Generate data structure for social media boxes, so that you can format markup how you like.
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

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/*
===============================================================================

  Site's Social Media Boxes

===============================================================================
*/

function social_boxes($args = array()) {

	$defaults = array(
		'ID'						=> $post->ID,
		'fb_btn_format' 		=> 'button_count',
		'fb_btn_width' 		=> "300",
		'fb_btn_faces' 		=> false,
		'gp_btn_size' 			=> 'medium',
		'gp_btn_annotation' 	=> 'bubble',
		'gp_btn_width'			=> '300',
		'fb_box_width'			=>	'292',
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
			$tweet_data[$i]['link'] = 'https://www.twitter.com/'.$handle.'/status/'.$tweets[$i]->id_str;
			$tweet_data[$i]['timestamp'] = strtotime($tweets[$i]->created_at);	
			$tweet_data[$i]['text'] = $tweet;
			
			} elseif ($count + 1 < count($tweets)) {$count++;}
		} //endif
	} //endfor
	
	return $tweet_data;

}

?>