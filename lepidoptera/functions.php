<?php

/** ===============================================================================
 *
 * Provides Open Graph information in the header for Facebook
 *
 * Most of the params are automatic
 *
 *
**/

function LEPI_open_graph($args=0) {
	global $post;

	$defaults = array(
		'url'        	=> get_bloginfo('url')
	,	'title'        => get_bloginfo('name')
	,	'desc'      	=> get_bloginfo('description')
	,	'default_img' 	=> get_bloginfo('template_url').'/images/apple-touch-icon.png'
	,	'custom_img'	=> false
	);
	$vars = wp_parse_args($args, $defaults);

	$url = is_singular()
		? get_permalink($post->ID)
		: $vars['url'];

	$title = is_singular()
		? $post->post_title.' | '.$vars['title']
		: $vars['title'];

	$desc = is_singular() && has_excerpt($post->ID)
		? apply_filters('get_the_excerpt', $post->post_excerpt)
		: $vars['title'];

	if (is_singular() && has_post_thumbnail($post->ID)) {


		/* Set up an empty array for the links. */
		$links = array();

		/* Get the intermediate image sizes and add the full size to the array. */
		$sizes = get_intermediate_image_sizes();
		$sizes[] = 'full';

		/* Loop through each of the image sizes. */
		foreach ( $sizes as $size ) {

			/* Get the image source, width, height, and whether it's intermediate. */
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $size );

			if ( empty($img) && !empty($image) && $image[1] >= 200 && $image[2] >= 200 ) {
				$img = $image[0];
			}
		}
	} elseif (is_singular() && !has_post_thumbnail($post->ID) && isset($vars['custom_img'])) {
		$img = $vars['custom_img'];
	}

	$type = is_singular('post')
		? 'article'
		: 'website';

	?>
	<meta property="og:type" content="<?php echo $type; ?>">
	<meta name="twitter:card"    content="summary">
	<meta name="twitter:creator" content="@<?php echo get_option('twitter_handle') ?>">
	<meta name="twitter:title" property="og:title" content="<?php echo esc_attr($title); ?>">
	<meta name="twitter:url" property="og:url" content="<?php echo esc_attr($url); ?>">
	<?php if (isset($img)) { ?><meta name="twitter:image" property="og:image" content="<?php echo esc_attr($img); ?>"><?php } ?>
	<?php if (!isset($img)) { ?><meta name="twitter:image" property="og:image" content="<?php echo esc_attr($vars['default_img']); ?>"><?php } ?>
	<meta name="twitter:description" property="og:description" content="<?php echo esc_attr($desc); ?>">
	<link rel="apple-touch-icon" href="<?php echo esc_attr($vars['default_img']); ?>" />
	<?php
} // LEPI_open_graph()

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
function FB_SDK() { include_once( LEPI_path . 'lib/fb-sdk.php' ); }

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
 * Facebook Like Box
 *
 * Arguments:
 *   'href'        : URL to like
 *   'send'        : 'true', 'false' : Send button
 *   'layout'      : 'standard', 'button_count', 'box_count' : Standard is crap, button count is small, box count is big
 *   'width'       : idk, this doesn't seem to do much..
 *   'height'      : most unnecesary.
 *   'show-faces'  : 'true', 'false' : Takes up too much space
 *   'stream'      : show the stream. or not. just don't cross them.
 *   'header' 		 : shows the Facebook header
 *   'show_border' : do you want borders with that?
 *   'colorscheme' : 'light', 'dark'
 *   'force_wall'  : For places, specifies whether the stream contains posts from the Place's wall or just checkins from friends. Default value: false.
 *
**/

// returns box
function LEPI_get_fb_box($args=0) {

	$defaults = array(
		'href'        	=> get_option('facebook_url')
	,	'width'       	=> 292
	,	'height'       => ''
	,	'show-faces'  	=> true
	,	'stream'  		=> true
	,	'header'  		=> false
	,	'show_border'  => true
	,	'colorscheme' 	=> 'light'
	,	'force_wall'   => false
	);
	$vars = wp_parse_args($args, $defaults);

	// build box div
	$box = '<div class="fb-like-box" data-href="'.$vars['href'].'" data-width="'.$vars['width'].'" data-height="'.$vars['height'].'" ';
	$box .= 'data-show-faces="'.$vars['show-faces'].'" data-stream="'.$vars['stream'].'" data-show-border="'.$vars['show_border'].'" data-colorscheme="'.$vars['colorscheme'].'" data-header="'.$vars['header'].'" data-force-wall="'.$vars['force_wall'].'" ';
	$box .= '></div>'.PHP_EOL;

	// put sdk in the footer IF IT HASN'T BEEN ADDED YET, return box
	if (!did_action('FB_SDK')) {
		add_action('wp_footer', 'FB_SDK');
	}
	return $box;



} // LEPI_get_fb_button()

// displays button
function LEPI_fb_box($args=0) {

	echo LEPI_get_fb_box($args);

} // LEPI_fb_box()


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
function GP_SDK() { include_once( LEPI_path . 'lib/gp-sdk.php' ); }

// returns button
function LEPI_get_gp_button($args=0) {

	$defaults = array(
		'href'        => get_option('gplus_url')
	,	'size'        => 'medium'
	);
	$vars = wp_parse_args($args, $defaults);

	// build button div
	$button = '<g:plusone href="'. $vars['href'] .'" size="'. $vars[ 'size' ] .'"></g:plusone>';

	// put sdk in the footer, return button
	add_action('wp_footer', 'GP_SDK');
	return $button;

} // LEPI_get_gp_button()

// displays button
function LEPI_gp_button($args=0) {

	echo LEPI_get_gp_button($args);

} // LEPI_gp_button()

/** ===============================================================================
 *
 * Foursquare Like Button
 *
 * Arguments:
 *   'href'        : URL of Foursquare location
 *   'format'      : Empty for regular, wide for expanded text

 *
**/

// Pull in Foursquare Javascript SDK only once.
function FSQ_SDK() { include_once( LEPI_PATH . 'lib/fsq-sdk.php'); }

// Returns Foursquare Like button
function LEPI_get_fsq_button($args=0) {

	$defaults = array(
		'href'	 => get_option('foursquare_url')
	,	'format' => ''
	);
	$vars = wp_parse_args($args, $defaults);

	// build the button
	// data-fuid="360215"
	$button = '<a href="'. $vars['href'] .'" class="fourSq-widget" data-type="like" data-variant="'. $vars['format'] .'">Like us on foursquare</a>';

	// put sdk in the footer, return button
	add_action('wp_footer', 'FSQ_SDK');
	return $button;

} // LEPI_get_fsq_button()

// displays button
function LEPI_fsq_button($args=0) {

	echo LEPI_get_fsq_button($args);

} // LEPI_fsq_button()

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
 * Get tweets with Twitter's REST API, now with more OAuth!
 *
 * @param int    $max_tweets    Number of tweets to return
 * @param string $twitter_id Twitter handle.. duh
 *
**/

function LEPI_get_tweets($max_tweets, $twitter_id) {

	include_once( LEPI_path . 'lib/twitter/tmhOAuth.php' );

	$access_code = get_option('tw_access_token_secret');

	if ($access_code != '') {

	$transient_name = $twitter_id.'_twitter_search_results';
//   delete_transient($transient_name);
	$tweets_raw = get_transient($transient_name);

	if ($tweets_raw === false) { //if there is no cached file

		$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    => get_option('tw_consumer_key'),
		'consumer_secret' => get_option('tw_consumer_secret'),
		'user_token'      => get_option('tw_access_token'),
		'user_secret'     => get_option('tw_access_token_secret')
		));

		// Build the request. Full param list here: https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
		// Because retweets and replies are filtered out AFTER getting tweets, the default count is $max_tweets plus 20,
		// (for good measure) and is pared down in the foreach() loop below.
		$code    = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), array(
		  'screen_name' => $twitter_id,
		  'count' => $max_tweets+20,
		  'include_rts' => false,
		  'include_entities' => false,
		  'exclude_replies'	=> true
		));

		$tweets_raw = json_decode($tmhOAuth->response['response'], true);
		set_transient($transient_name, $tweets_raw, 1 * HOUR_IN_SECONDS);
		$set_tweets = true;
	}
	if (isset($set_tweets)) { //parse the feed just once as it will be cached from now on
	  $tweets_raw = json_decode($tmhOAuth->response['response'], true);
	}

	$limit = 0;

	if (isset($tweets_raw['error']) && $tweets_raw['error'] == 'Not authorized') {
		 $tweets_raw['error_message'] = 'The '. $twitter_id .' account\'s tweets are protected and are not available for display. Unprotect the account in order to access the tweets.';
		 return $tweets_raw;
	} else {

		foreach ($tweets_raw as $tweet) {

			if (($limit < $max_tweets) && $tweet['text']) {

				$tweet_text = $tweet['text'];

				// Add hyperlink html tags to any urls, twitter ids or hashtags in the tweet.
				$tweet_text = preg_replace('/(\.\.\.+)/', 'É', $tweet_text);
				$tweet_text = preg_replace('/(https?:\/\/[^\s"<>É]+)/','<a href="$1">$1</a>', $tweet_text);
				$tweet_text = preg_replace('/(^|[\n\s])@([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/$2">@$2</a>', $tweet_text);
				$tweet_text = preg_replace('/(^|[\n\s])#([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/search?q=%23$2">#$2</a>', $tweet_text);

				// Time zone offsets
				$timestamp = new DateTime($tweet['created_at']);
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

		if (isset($tweets)) return array_slice($tweets, 0, $max_tweets);

		}
	}

} // LEPI_get_tweets()

/** ===============================================================================
 *
 * Get reviews with Yelp's business API
 *
 * @param int    $yelp_count  Number of yelps to return
 * @param string $yelp_slug
 *
**/

function LEPI_get_yelps($args=0) {

$defaults = array(
	'yelp_business_slug'    	=> get_option('yelp_business_slug')
,	'yelp_consumer_key'    	   => get_option('yelp_consumer_key')
,	'yelp_consumer_secret'	   => get_option('yelp_consumer_secret')
,	'yelp_access_token'			=> get_option('yelp_access_token')
,	'yelp_token_secret'	      => get_option('yelp_token_secret')
);
$vars = wp_parse_args($args, $defaults);

$biz_slug = $vars['yelp_business_slug'];

// Enter the path that the oauth library is in relation to the php file
include_once ( LEPI_path . 'lib/OAuth.php' );

$unsigned_url = 'http://api.yelp.com/v2/business/'.$biz_slug;

// Set your keys here
$consumer_key 		= $vars['yelp_consumer_key'];
$consumer_secret 	= $vars['yelp_consumer_secret'];
$token 				= $vars['yelp_access_token'];
$token_secret 		= $vars['yelp_token_secret'];

// Check the cache first
$transient_name = $biz_slug.'_yelp_results';
$cached_yelps = get_transient($transient_name);

// If there is no cache, go get the Yelps
if ($cached_yelps === false) {

// Configure OAuth configuration
$token = new OAuthToken($token, $token_secret);
$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
$oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
$oauthrequest->sign_request($signature_method, $consumer, $token);
$signed_url = $oauthrequest->to_url();

// Send Yelp API Call
$ch = curl_init($signed_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
$data = curl_exec($ch); // Yelp response
curl_close($ch);

// Handle Yelp response data
$response = json_decode($data);

$requested_yelps = (array) $response;
set_transient($transient_name, $requested_yelps, 1 * HOUR_IN_SECONDS);

// Return the data!
return $requested_yelps;

} else {
return $cached_yelps;
}

}