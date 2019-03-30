<?php
/*
Plugin Name: Netflix-X2
Plugin URI: http://www.ericvb.com/wordpress-netflix-plugin/
Description: A reimplementation of the original Albert Banks Netflix plugin, Netflix-X2 displays RSS content from your Netflix account.
Version: 1.4.1
License: GPL
Author: Eric VanBergen
Author URI: http://www.ericvb.com
*/

//Credits:  Albert Banks - www.albertbanks.com - Netflix-X2 originates from his plugin.
//	    David McDonald - www.watcihngred.com - Contributed textonly functionality and logic.
//	    Ryan Stazel - www.staze.org - Bringing to light the depreciation of MagpieRSS and helping with the implementation of SimplePie functions instead.
//	    Wordpress users around the world thank you!!


// display netflix info
function netflix($number_movies = 10, $feed= 'queue', $display_type = 'raw', $image_size = 'small', $before = '<li>', $after = '</li>', $netflix_id='') {

	// use configuration if args not set
	for($i = 0 ; $i < func_num_args(); $i++) {
		$args[] = func_get_arg($i);
	}
	if (!isset($args[0])) $number_movies = get_option('netflix_number_movies'); 
	if (!isset($args[1])) $feed = get_option('netflix_feed');
	if (!isset($args[2])) $display_type = trim(get_option('netflix_display_type'));
	if (!isset($args[3])) $image_size = get_option('netflix_image_size');
	if (!isset($args[4])) $before = stripslashes(get_option('netflix_before'));
	if (!isset($args[5])) $after = stripslashes(get_option('netflix_after'));
	if (!isset($args[6])) $netflix_id = stripslashes(get_option('netflix_netflix_id'));
	
	// make sure we get simplepie rss functions
	require_once(ABSPATH.'wp-includes/feed.php');
	// set how long in seconds the rss feeds will be cached by simplepie (12 hours if we dont set this)
	add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 1800;' ) );

	// which feed
	switch($feed) {
		case 'reviews':
		$url = "http://dvd.netflix.com/ReviewsRSS?id=".$netflix_id;
			break;
		case 'recommendations':
			$url = "http://dvd.netflix.com/RecommendationsRSS?id=".$netflix_id;
			break;
		default: 
			$url = "http://dvd.netflix.com/QueueRSS?id=".$netflix_id;
	}
	
	// url setup properly
	if ($url) {
		$rss = fetch_feed($url);
		foreach ($rss->get_items() as $item) {

			// no limit, exit
			if ($number_movies == 0) break;

			// vars
			$raw_title = $item->get_title();
			$title_start_position = strpos($raw_title, " ") + 1;
			$title = substr($raw_title, $title_start_position);
			$link = $item->get_link();
			$description = $item->get_description();
			if (preg_match("#/(\d+)#", $link, $matches)) $movie_id = $matches[1]; 
			

			// diplay type
			switch($display_type) {
				case 'title':
					if ($feed == 'reviews' || $feed == 'recommendations') $display = $raw_title;
					else $display = $title;
					echo wptexturize($before.'<a href="'.$link.'">'.$display.'</a>'.$after);
					break;
				case 'image':
					if ($feed == 'reviews' || $feed == 'recommendations') $title = $raw_title;
					$display = '<img src="http://cdn.nflximg.com/us/boxshots/'.$image_size.'/'.$movie_id.'.jpg" alt="'.$title.'" title="'.$title.'" />';
					echo wptexturize($before.'<a href="'.$link.'">'.$display.'</a>'.$after);
					break;
				case 'both':
					if ($feed == 'reviews' || $feed == 'recommendations') $title = $raw_title;
					$display = '<img src="http://cdn.nflximg.com/us/boxshots/'.$image_size.'/'.$movie_id.'.jpg" alt="'.$title.'" title="'.$title.'" /><br />'.$title.'';
					echo wptexturize($before.'<a href="'.$link.'">'.$display.'</a>'.$after);
					break;
				case 'description':
					$display = $description;
					echo wptexturize($before.'<a href="'.$link.'">'.$display.'</a>'.$after);
					break;
				case 'textonly':
					if ($feed == 'reviews' || $feed == 'recommendations') $title = $raw_title;
					$display = preg_replace("/<.*?>/", "", $description);
					//for textonly we ONLY display the text.  No hyperlink info as with other cases.
					echo wptexturize($before.$title.'<br />'.$display.$after);
					break;
				case 'raw':
					$display = $raw_title;
					echo wptexturize($before.$display.$after);
					break;
				// raw
				default: 
					$display = $raw_title;
			}
		
			// display link
			$number_movies--;
		}
	}
}

function netflix_movies($number_movies = 10, $feed= 'queue', $netflix_id='') {

	// includes
	require_once('movie.class.php');
	require_once(ABSPATH.'wp-includes/feed.php');

	
	$movies = array();
	
	// use configuration if args not set
	for($i = 0 ; $i < func_num_args(); $i++) {
		$args[] = func_get_arg($i);
	}
	if (!isset($args[0])) $number_movies = get_option('netflix_number_movies'); 
	if (!isset($args[1])) $feed = get_option('netflix_feed');
	if (!isset($args[6])) $netflix_id = stripslashes(get_option('netflix_netflix_id'));
	
	// loop through feed
	$url = generate_feed_url($netflix_id, $feed);
	$rss = fetch_feed($url);
	foreach ($rss->get_items() as $item) {


		// limit reached, exit
		if ($number_movies == 0) break;

		// vars
		$title = generate_title($item->get_title(), $feed);
		$link = $item->get_link();
		if (preg_match("#/(\d+)#", $link, $matches)) $movie_id = $matches[1];
		$description = $item->get_description(); 
		
		// save movie
		$results[] = new Movie($movie_id, $title, $link, $description);
		$number_movies--;
	}
	return $results;
}

// generate movie title
function generate_title($title, $feed) {

	// feed
	if ($feed == 'queue') {
		$title_start_position = strpos($title, ' ');
		return substr($title, $title_start_position);
	}
	return $title;
}

// generate feed url
function generate_feed_url($netflix_id, $feed) {

	// feed
	switch($feed) {
		case 'reviews':
			return "http://dvd.netflix.com/ReviewsRSS?id=".$netflix_id;
		case 'recommendations':
			return "http://dvd.netflix.com/RecommendationsRSS?id=".$netflix_id;
		default: 
			return "http://dvd.netflix.com/QueueRSS?id=".$netflix_id;
	}
}


// make the plugin widget ready
// aka widgetized
function netflix_widget($args){

	extract($args);
	$options = get_option("netflix_widget");

	echo $before_widget;
	echo $before_title;
	echo $options['title'];
	echo $after_title;
	echo '<ul>'; //yeah yeah this part is sorta hokey
        netflix();
	echo '</ul>'; //hokey++
	echo $after_widget;
}

function widget_init(){
        register_sidebar_widget(__('Netflix-X2'), 'netflix_widget');
}
add_action("plugins_loaded", "widget_init");

//configuring widget options. 
//just title so far but more to come
function netflix_widget_control()
{
  $options = get_option("netflix_widget");

  if (!is_array( $options ))
  {
  $options = array(
  'title' => 'My Netflix'
  //planning ahead - more options to go here
  );
  }

    if ($_POST['netflix_widget_submit']) {
      $options['title'] = htmlspecialchars($_POST['netflix_widget_title']);
      update_option("netflix_widget", $options);
    }

?>
  <p>
  <label for="netflix_widget_title">Title: </label><br />
  <input class="widefat" type="text" id="netflix_widget_title" name="netflix_widget_title" value="<?php echo $options['title'];?>" />
      <input type="hidden" id="netflix_widget_submit" name="netflix_widget_submit" value="1" />
      
    <?php
}
//register the widget controls
register_widget_control( 'Netflix-X2', 'netflix_widget_control');


// subpanel to set netflix options
function netflix_subpanel() {
	// form submitted
	if (isset($_POST['configure_netflix'])) {
		// gather form data
		$form_netflix_id = $_POST['netflix_id'];
		$form_number_movies = $_POST['number_movies'];
		$form_feed = $_POST['feed'];
		$form_display_type = $_POST['display_type'];
		$form_image_size = $_POST['image_size'];
		$form_before = $_POST['before'];
		$form_after = $_POST['after'];

		// update options
		update_option('netflix_netflix_id', $form_netflix_id);
		update_option('netflix_number_movies', $form_number_movies);
		update_option('netflix_feed', $form_feed);
		update_option('netflix_display_type', $form_display_type);
		update_option('netflix_image_size', $form_image_size);
		update_option('netflix_before', $form_before);
		update_option('netflix_after', $form_after);
?>

<div class="updated">
  <p>Options changes saved.</p>
</div>
<?php
	}
?>
<div class="wrap">
  <h2>Netflix-X2 Configuration</h2>
  <form method="post">
    <fieldset class="options">
    <table>
      <tr>
        <td><p><strong>
            <label for="flickr_nsid">Netflix ID</label>
            :</strong></p></td>
        <td><input name="netflix_id" type="text" id="netflix_id" value="<?php echo get_option('netflix_netflix_id'); ?>" size="40" />
          View your <a href="http://www.netflix.com/RSSFeeds">Personal Feeds</a> to find your id.
          </p></td>
      </tr>
      <tr>
        <td><p><strong>Feed:</strong></p></td>
        <td><select name="number_movies" id="number_movies">
            <option <?php if(get_option('netflix_number_movies') == '1') { echo "selected"; } ?> value="1">1</option>
            <option <?php if(get_option('netflix_number_movies') == '2') { echo "selected"; } ?> value="2">2</option>
            <option <?php if(get_option('netflix_number_movies') == '3') { echo "selected"; } ?> value="3">3</option>
            <option <?php if(get_option('netflix_number_movies') == '4') { echo "selected"; } ?> value="4">4</option>
            <option <?php if(get_option('netflix_number_movies') == '5') { echo "selected"; } ?> value="5">5</option>
            <option <?php if(get_option('netflix_number_movies') == '6') { echo "selected"; } ?> value="6">6</option>
            <option <?php if(get_option('netflix_number_movies') == '7') { echo "selected"; } ?> value="7">7</option>
            <option <?php if(get_option('netflix_number_movies') == '8') { echo "selected"; } ?> value="8">8</option>
            <option <?php if(get_option('netflix_number_movies') == '9') { echo "selected"; } ?> value="9">9</option>
            <option <?php if(get_option('netflix_number_movies') == '10') { echo "selected"; } ?> value="10">10</option>
          </select>
          movies from your
          <select name="feed" id="feed">
            <option <?php if(get_option('netflix_feed') == 'queue') { echo "selected"; } ?> value="queue">Queue</option>
            <option <?php if(get_option('netflix_feed') == 'reviews') { echo "selected"; } ?> value="reviews">Movie Reviews</option>
            <option <?php if(get_option('netflix_feed') == 'recommendations') { echo "selected"; } ?> value="recommendations">Recommendations</option>
          </select>
          RSS feed </td>
      </tr>
      <tr>
        <td><p><strong>Display:</strong> </p></td>
        <td><select name="display_type" id="display_type">
            <option <?php if(get_option('netflix_display_type') == 'title') { echo "selected"; } ?> value="title">Movie Title</option>
            <option <?php if(get_option('netflix_display_type') == 'raw') { echo "selected"; } ?> value="raw">Raw Feed</option>
            <option <?php if(get_option('netflix_display_type') == 'image') { echo "selected"; } ?> value="image">Cover Image</option>
            <option <?php if(get_option('netflix_display_type') == 'both') { echo "selected"; } ?> value="both">Both Image and Title</option>
            <option <?php if(get_option('netflix_display_type') == 'description') { echo "selected"; } ?> value="description">Full Summary/Review with Cover Image</option>
            <option <?php if(get_option('netflix_display_type') == 'textonly') { echo "selected"; } ?> value="textonly">Summary/Review, Text ONLY - no Cover Image</option>
          </select>
          (if image select size:
          <select name="image_size" id="image_size">
            <option <?php if(get_option('netflix_image_size') == 'small') { echo "selected"; } ?> value="small">Small</option>
            <option <?php if(get_option('netflix_image_size') == 'large') { echo "selected"; } ?> value="large">Large</option>
          </select>
          ) 
      </tr>
      <tr>
        <td><p><strong>
            <label for="before">Before</label>
            /
            <label for="after">After</label>
            :</strong></p></td>
        <td><input name="before" type="text" id="before" value="<?php echo htmlspecialchars(stripslashes(get_option('netflix_before'))); ?>" size="10" />
          /
          <input name="after" type="text" id="afte" value="<?php echo htmlspecialchars(stripslashes(get_option('netflix_after'))); ?>" size="10" />
          <em> e.g. &lt;li&gt;&lt;/li&gt;, &lt;p&gt;&lt;/p&gt;</em>
          </p>
        </td>
      </tr>
    </table>
    </fieldset>
    <p>
    <div class="submit">
      <input type="submit" name="configure_netflix" value="<?php _e('Save Settings &raquo;', 'configure_netflix') ?>" />
    </div>
    </p>
  </form>
</div>
<?php 
} 

function netflix_admin_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('options-general.php', 'Netflix-X2 Configuration Page', 'Netflix-X2', 8, basename(__FILE__), 'netflix_subpanel');
	}
}

add_action('admin_menu', 'netflix_admin_menu'); 

// Add settings link on plugin page  
function netflix_settings_link($links) {  
	$settings_link = '<a href="options-general.php?page=netflix.php">Settings</a>';  
	array_unshift($links, $settings_link);  
	return $links;  
}  
   
$plugin = plugin_basename(__FILE__);  
add_filter("plugin_action_links_$plugin", 'netflix_settings_link' );

?>
