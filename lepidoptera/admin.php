<?php

/** ===============================================================================
 *
 * Social Media Settings
 *
**/

add_action( 'admin_head', 'LEPI_styles' );
add_action( 'admin_init', 'LEPI_register' );
add_action( 'admin_menu', 'LEPI_social_menu' );
add_action( 'admin_enqueue_scripts', 'LEPI_scripts' );


$LEPI_settings = array(
	'facebook_url' 				=> array()
,	'gplus_url' 				=> array()
,	'linkedin_url' 				=> array()
,	'youtube_url' 				=> array()
,	'vimeo_url'	 				=> array()
,	'flickr_url' 				=> array()
,	'pinterest_url' 			=> array()
,	'rss_url' 					=> array()
,	'podcast_url'	 			=> array()
,	'twitter_handles' 			=> array( 'auto' => FALSE )
,	'tw_consumer_key' 			=> array()
,	'tw_consumer_secret' 		=> array()
,	'tw_access_token' 			=> array()
,	'tw_access_token_secret' 	=> array()
,	'yelp_business_slug' 		=> array()
,	'yelp_consumer_key' 		=> array()
,	'yelp_consumer_secret'		=> array()
,	'yelp_access_token' 		=> array()
,	'yelp_token_secret' 		=> array()
,	'foursquare_url' 			=> array( 'auto' => FALSE )
,	'avvo_username' 			=> array()
,	'avvo_api_key'				=> array()
,	'avvo_lawyerid'				=> array()
);
$LEPI_slug = 'lepidoptera';
$LEPI_transient = 'twitter_search_results';

function LEPI_styles() {
	echo '<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">';
	echo '<link href="'. plugins_url('/css/style.css' , __FILE__) .'" rel="stylesheet">';
}

function LEPI_register() {
	foreach ( $LEPI_settings as $setting => $options ) {
		register_setting( 'LEPI', $setting ); 
	}
}

function LEPI_get_handles( $key, $single = FALSE ) { 
	$handles = get_option($key);
	if ( $handles_decoded = json_decode($handles) ) { $handles = $handles_decoded;}
	if ( is_array($handles) && count($handles) > 0 ) {
		if ( $single === TRUE ) { $return = $handles[0]; }
		else { $return = $handles; }
	}
	elseif ( is_string($handles) ) { 
		if ($single === TRUE ) { $return = $handles; }
		else { $return = array( $handles ); }
	}
	else { $return = FALSE;}
	return $return;
}


function LEPI_scripts() {
	wp_enqueue_script('Lepidoptera Scripts', plugins_url('/scripts/lepidoptera.min.js' , __FILE__) );
}


function LEPI_social_menu() {
	global $LEPI_slug;
	add_options_page( 'Lepidoptera', 'Lepidoptera', 'activate_plugins', $LEPI_slug, 'LEPI_social_settings');
}

function LEPI_get_trans() {
	global $LEPI_transient;
	global $wpdb;
	$return = array();
	$caches = $wpdb->get_results("SELECT * FROM " . $wpdb->options . " WHERE option_name LIKE '%\_transient\_timeout\_%" . $LEPI_transient . "'", ARRAY_A);
	if ( count($caches) > 0 ) {
		foreach ( $caches as $cache ) {

			$name_pre = $cache['option_name'];
			$key = preg_replace('/_transient_timeout_/i', '', $name_pre);
			$name = preg_replace('/(_|)' . $LEPI_transient . '/i', '', $key);

			if ( strlen($name) == 0 ) {
				$name = 'All Handles';
			}

			$return[] = array(
				'key' => $key
			,	'name' => $name
			,	'expires' => date('m/d/Y g:i a', $cache['option_value'])
			);
		}
	}
	else { 
		$return = FALSE;
	}
	return $return;
}

function LEPI_update_options() {

	/* set status message */
	$message = '';

	/* Update/Delete Functions */
	if ( isset($_POST['submit']) ) {

		foreach ( $LEPI_settings as $setting => $options ) {
			if ( !isset($options['auto']) || $options['auto'] !== FALSE ) {
				update_option( $setting,  $_POST[$setting] );
			}
		}

		/* Update Foursquare Settings Manually */
		$foursquare_key = 'foursquare_url';
		if ( count($_POST[$foursquare_key]) > 1 ) {
			foreach ( $_POST[$foursquare_key] as $foursquare_location ) {
				if ( $foursquare_location != '' ) { $foursquare_locations[] = $foursquare_location; }
			}
		}
		else {
			$foursquare_locations = $_POST[$foursquare_key];
		}

		/* update twitter handles manually */
		$handles_key = 'twitter_handles';
		if ( isset($_POST[$handles_key]) && count($_POST[$handles_key]) > 0 ) {
			$handlesupd = array();
			foreach ( $_POST[$handles_key] as $handle ) {
				if ( is_string($handle) && strlen($handle) > 0 && !in_array( trim($handle), $handlesupd ) ) { 
					$handlesupd[] = trim($handle);
				}
			}
			if ( count($handlesupd) > 0 ) {
				$handlesupdJSON = json_encode($handlesupd);
				update_option( $handles_key, $handlesupdJSON );
			}
		}

		$message .= '<div id="message" class="updated"><p>Social media settings updated.</p></div>';
	}

	if ( isset($_POST['delete_twitter_cache']) ) {
		$transients = LEPI_get_trans();
		if ($transients) {
			foreach ($transients as $transient) {
				delete_transient($transient['key']);
			}
		}
		
		$message .= '<div id="message" class="updated"><p>Twitter cache deleted.</p></div>';
	}

	if ( isset($_POST['delete_yelp_cache']) ) {
		delete_transient(get_option('yelp_business_slug').'_yelp_results');
		$message .= '<div id="message" class="updated"><p>Yelp cache for '. get_option('yelp_business_slug') .' deleted.</p></div>';
	}

	if ( isset($_POST['delete_avvo_cache']) ) {
		delete_transient(get_option('avvo_lawyerid').'_avvo_results');
		$message .= '<div id="message" class="updated"><p>Avvo cache for '. get_option('avvo_lawyerid') .' deleted.</p></div>';
	}

	return $message;

}

function LEPI_social_settings() {

	global $LEPI_slug;

	/* call update options function */
	$message = LEPI_update_options();

?>
<div class="wrap">
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page='.$LEPI_slug; ?>">
<h2>Lepidoptera</h2>
<?php (isset($message) ? $message : ''); ?>

<h3>Social Network URLs</h3>
<table class="form-table urls">
	<tr valign="top">
		<th scope="row">
			<label for="facebook_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-facebook icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="facebook_url" name="facebook_url" value="<?php echo get_option('facebook_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="gplus_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-google-plus icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="gplus_url" name="gplus_url" value="<?php echo get_option('gplus_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="linkedin_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-linkedin icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="linkedin_url" name="linkedin_url" value="<?php echo get_option('linkedin_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="youtube_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-youtube icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="youtube_url" name="youtube_url" value="<?php echo get_option('youtube_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="vimeo_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-vimeo icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="vimeo_url" name="vimeo_url" value="<?php echo get_option('vimeo_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="pinterest_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-pinterest icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="pinterest_url" name="pinterest_url" value="<?php echo get_option('pinterest_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="flickr_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-flickr icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="flickr_url" name="flickr_url" value="<?php echo get_option('flickr_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="podcast_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-music icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="podcast_url" name="podcast_url" value="<?php echo get_option('podcast_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="rss_url">
			<span class="icon-stack">
			  <i class="icon-sign-blank icon-stack-base"></i>
			  <i class="icon-rss icon-light"></i>
			</span>
			</label>
		</th>
		<td>
			<input type="text" id="rss_url" name="rss_url" value="<?php echo get_option('rss_url'); ?>" style="width:70%" />
			<button class="clear"><i class="icon-eraser"></i></button>
			<p class="description">Default &rarr; <?php echo get_bloginfo('rss2_url'); ?></p>
		</td>
	</tr>
</table>

<div class="api_settings">
<h2>API Settings</h2>

<div class="switchbox">
	<a href="#" class="twitter_settings active">Twitter</a>
	<a href="#" class="yelp_settings">Yelp!</a>
	<a href="#" class="foursquare_settings">Foursquare</a>
	<a href="#" class="avvo_settings" style="display:none;">Avvo</a>
</div>

<div class="twitter_settings settingsbox active">
<div class="handle_template"><?php echo LEPI_load_twitter_template() ?></div>
<table class="form-table">
	<tr valign="top">
		<th scope="row">Twitter Handle</th>
		<td>
		
		<div class="handle_add handle_cell">
			<i class="dashicons dashicons-plus"></i>
		</div>
		<div class="handle_container handle_cell"><?php echo LEPI_load_handles(); ?></div>
		<p class="description">Just the handle, not the URL</p>

		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Consumer Key</th>
		<td><input type="text" id="tw_consumer_key" name="tw_consumer_key" value="<?php echo get_option('tw_consumer_key'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Consumer Secret</th>
		<td><input type="text" id="tw_consumer_secret" name="tw_consumer_secret" value="<?php echo get_option('tw_consumer_secret'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Access Token</th>
		<td><input type="text" id="tw_access_token" name="tw_access_token" value="<?php echo get_option('tw_access_token'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Access Token Secret</th>
		<td><input type="text" id="tw_access_token_secret" name="tw_access_token_secret" value="<?php echo get_option('tw_access_token_secret'); ?>" style="width:70%" /></td>
	</tr>
	<?php

	$transients = LEPI_get_trans();
	if ($transients) { ?>

	<tr>
		<td colspan="2"><? submit_button('Delete Twitter Cache', 'delete_twitter', 'delete_twitter_cache', false);  ?></td>
	</tr>
	<tr>
		<td colspan="2">
		<table class="trans-table">
		<tr>
			<td class="trans-header">Name</td>
			<td class="trans-header">Expires</td>
		</tr>

		<?php foreach ($transients as $transient) { ?>

		<tr>
			<td class="trans-data"><?php echo $transient['name'] ?></td>
			<td class="trans-data"><?php echo $transient['expires'] ?></td>
		</tr>

		<?php } ?>

		</table>
		</td>
	</tr>
	<?php } ?>
</table>
</div><!-- twitter_settings -->

<div class="yelp_settings settingsbox">
<table class="form-table">
	<tr valign="top">
		<th scope="row">Business Slug</th>
		<td><input type="text" id="yelp_business_slug" name="yelp_business_slug" value="<?php echo get_option('yelp_business_slug'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Consumer Key</th>
		<td><input type="text" id="yelp_consumer_key" name="yelp_consumer_key" value="<?php echo get_option('yelp_consumer_key'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Consumer Secret</th>
		<td><input type="text" id="yelp_consumer_secret" name="yelp_consumer_secret" value="<?php echo get_option('yelp_consumer_secret'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Access Token</th>
		<td><input type="text" id="yelp_access_token" name="yelp_access_token" value="<?php echo get_option('yelp_access_token'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Access Token Secret</th>
		<td><input type="text" id="yelp_token_secret" name="yelp_token_secret" value="<?php echo get_option('yelp_token_secret'); ?>" style="width:70%" /></td>
	</tr>
	<tr>
		<td colspan="2"><? submit_button('Delete Yelp Cache', 'delete_yelp', 'delete_yelp_cache', false); ?></td>
	</tr>
</table>
</div><!-- yelp_settigns -->

<div class="foursquare_settings settingsbox">
<table class="form-table">
	<?php
	$foursquare_locations = get_option('foursquare_url');
	if ( $foursquare_locations === '' ) {$foursquare_locations = array(0 => '');}
	$numLocations = count($foursquare_locations);
	$i = 0;

	foreach ( $foursquare_locations as $pointer => $foursquare_location ) { ?>
	<tr valign="top">
		<th scope="row">
			<?php if ( $pointer == 0 ) { ?>Foursquare URL<?php } ?>
			<?php if ( ++$i === $numLocations ) { ?><a href="#" class="add_foursquare">Add Location +</a><? } ?>
		</th>
		<td><input type="text" id="foursquare_url" name="foursquare_url[]" value="<?php echo $foursquare_location; ?>" style="width:70%" /></td>
	</tr>
	<?php } ?>
</table>
</div><!-- foursquare_settings -->

<div class="avvo_settings settingsbox" style="display:none;">
<table class="form-table">
	<tr valign="top">
		<th scope="row">Avvo Username</th>
		<td><input type="text" id="avvo_username" name="avvo_username" value="<?php echo get_option('avvo_username'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">API Key</th>
		<td><input type="text" id="avvo_api_key" name="avvo_api_key" value="<?php echo get_option('avvo_api_key'); ?>" style="width:70%" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Lawyer ID</th>
		<td><input type="text" id="avvo_lawyerid" name="avvo_lawyerid" value="<?php echo get_option('avvo_lawyerid'); ?>" style="width:70%" /></td>
	</tr>
	<tr>
		<td colspan="2"><? submit_button('Delete Avvo Cache', 'delete_avvo', 'delete_avvo_cache', false); ?></td>
	</tr>
</table>
</div><!-- yelp_settigns -->

</div><!-- api_settings -->

<p class="submit"><?php submit_button('Save Changes', 'primary', 'submit', false); ?></p>
</form>
</div><!-- wrap -->
<?php
} // social_settings()

function LEPI_load_twitter_template($val='',$id='') {

	ob_start();
	?>
	<div class="handle_wrapper handle_row">
		<input class="handle_cell handle_input" type="text" id="twitter_handles_<?php echo $id ?>" name="twitter_handles[]" value="<?php echo $val ?>" autocomplete="off" />
		<div class="dashicons dashicons-minus handle_remove handle_cell"></div>
	</div>
	<?php
	return ob_get_clean();
}

function LEPI_load_handles() {
	$handles = LEPI_get_handles('twitter_handles');
	$h_html = '';
	if ( is_array($handles) ) {
		if ( count($handles) > 0 ) {
			foreach ( $handles as $id => $handle ) {
				if ( strlen($handle) && $handle != '' ) {
					$h_html .= LEPI_load_twitter_template($handle,$id);
				}
			}
		}
		else {
			$h_html .= LEPI_load_twitter_template(null,0);
		}
	}
	elseif ($handles) {
		$h_html = LEPI_load_twitter_template($handles,0);
	}
	else {
		$h_html .= LEPI_load_twitter_template(null,0);
	}

	return $h_html;
}