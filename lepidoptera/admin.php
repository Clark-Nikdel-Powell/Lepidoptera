<?php

/** ===============================================================================
 *
 * Social Media Settings
 *
**/

add_action('admin_head', 'LEPI_styles');

function LEPI_styles() {
	echo '<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">';
	echo '<link href="'. plugins_url('/css/style.css' , __FILE__) .'" rel="stylesheet">';
}

add_action( 'admin_enqueue_scripts', 'LEPI_scripts' );

function LEPI_scripts() {
	wp_enqueue_script('Lepidoptera Scripts', plugins_url('/scripts/lepidoptera.min.js' , __FILE__) );
}

add_action('admin_menu', 'LEPI_social_menu');

function LEPI_social_menu() {
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	$slug = 'lepidoptera';
	add_menu_page( 'Lepidoptera', 'Lepidoptera', 'activate_plugins', $slug );
	add_submenu_page( $slug, 'Lepidoptera', 'Lepidoptera', 'activate_plugins', $slug, 'LEPI_social_settings' );
}

function LEPI_social_settings() {
	$slug = 'lepidoptera';

	// General Social Networks Settings
	add_option('facebook_url');
	add_option('gplus_url');
	add_option('linkedin_url');

	// Video Social Networks
	add_option('youtube_url');
	add_option('vimeo_url');

	// Photo Social Networks
	add_option('flickr_url');
	add_option('pinterest_url');

	// RSS
	add_option('rss_url');
	add_option('podcast_url');

	// Twitter Settings
	add_option('twitter_handle');
	add_option('tw_consumer_key');
	add_option('tw_consumer_secret');
	add_option('tw_access_token');
	add_option('tw_access_token_secret');

	// Yelp Settings
	add_option('yelp_business_slug');
	add_option('yelp_consumer_key');
	add_option('yelp_consumer_secret');
	add_option('yelp_access_token');
	add_option('yelp_token_secret');

	// Foursquare Settings
	add_option('foursquare_url');

	// Update/Delete Functions
	if ( isset($_POST['submit']) ) {

		// Update General Settings
		update_option('facebook_url',  $_POST['facebook_url']);
		update_option('gplus_url',     $_POST['gplus_url']);
		update_option('linkedin_url',  $_POST['linkedin_url']);
		update_option('youtube_url',   $_POST['youtube_url']);
		update_option('vimeo_url',     $_POST['vimeo_url']);
		update_option('flickr_url',    $_POST['flickr_url']);
		update_option('pinterest_url', $_POST['pinterest_url']);
		update_option('rss_url',       $_POST['rss_url']);
		update_option('podcast_url',   $_POST['podcast_url']);

		//Update Twitter Settings
		update_option('twitter_handle',         $_POST['twitter_handle']);
		update_option('tw_consumer_key',        $_POST['tw_consumer_key']);
		update_option('tw_consumer_secret',     $_POST['tw_consumer_secret']);
		update_option('tw_access_token',        $_POST['tw_access_token']);
		update_option('tw_access_token_secret', $_POST['tw_access_token_secret']);

		//Update Yelp Settings
		update_option('yelp_business_slug',     $_POST['yelp_business_slug']);
		update_option('yelp_consumer_key',      $_POST['yelp_consumer_key']);
		update_option('yelp_consumer_secret',   $_POST['yelp_consumer_secret']);
		update_option('yelp_access_token',      $_POST['yelp_access_token']);
		update_option('yelp_token_secret',      $_POST['yelp_token_secret']);

		//Update Foursquare Settings
		if (count($_POST['foursquare_url']) > 1) {
			foreach ($_POST['foursquare_url'] as $foursquare_location) {
				if ($foursquare_location != '') {$foursquare_locations[] = $foursquare_location;}
			}
		} else {
			$foursquare_locations = $_POST['foursquare_url'];
		}

		$message = '<div id="message" class="updated"><p>Social media settings updated.</p></div>';
	}

	if ( isset($_POST['delete_twitter_cache']) ) {
		// Problem: if they're using more than one Twitter handle, how do we make sure that all transients are deleted, not just the one that's set?
		// Solution: make the Twitter handle a select box (if there's more than one handle) with the ability to add more. Authentication information switches out based on which handle you have selected.
		delete_transient(get_option('twitter_handle').'_twitter_search_results');
		$message = '<div id="message" class="updated"><p>Twitter cache for '. get_option('twitter_handle') .' deleted.</p></div>';
	}

	if ( isset($_POST['delete_yelp_cache']) ) {
		delete_transient(get_option('yelp_business_slug').'_yelp_results');
		$message = '<div id="message" class="updated"><p>Yelp cache for '. get_option('yelp_business_slug') .' deleted.</p></div>';
	}

?>
<div class="wrap">
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page='.$slug; ?>">
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
</div>

<div class="twitter_settings settingsbox active">
<table class="form-table">
	<tr valign="top">
		<th scope="row">Twitter Handle</th>
		<td><input type="text" id="twitter_handle" name="twitter_handle" value="<?php echo get_option('twitter_handle'); ?>" style="width:70%" /><p class="description">Just the handle, not the URL</p></td>
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
	<tr>
		<td colspan="2"><? submit_button('Delete Twitter Cache', 'delete_twitter', 'delete_twitter_cache', false); ?></td>
	</tr>
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

	foreach ($foursquare_locations as $pointer => $foursquare_location) { ?>
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

</div><!-- api_settings -->

<p class="submit"><?php submit_button('Save Changes', 'primary', 'submit', false); ?></p>
</form>
</div><!-- wrap -->
<?php
} // social_settings()