=== Plugin Name === 
Netflix-X2 
Contributors: Eric VanBergen, Albert Banks 
Donate Link: http://www.ericvb.com 
Tags: netflix, widget, media, images, integration, links, sidebar, embed, widgetized 
Requires at least: 2.0.2 
Tested up to: 3.8
Stable tag: 1.4.1

Display your Netflix queue, movie reviews, and more on your Wordpress blog with this widget ready plugin. 


== Description == 
Use this plugin to display movies from your queue, instant queue, recent rental activity, recommendations, and reviews.  Seamlessly integrate your Netflix movies into your Wordpress blog with this widget-ready plugin!   Netflix-X2 is a re-implementation of Albert Banks' original Netflix plugin for Wordpress.  It expands on the the solid functionality and adds support for additional feeds, new settings, and is widgetized.

Version: 1.4.1
License: GPL 
Author: Eric VanBergen 
Author URI: http://www.ericvb.com 

* Credit Due: Albert Banks wrote the original Netflix plugin for Wordpress, which the vast majority of this reimplementation contains. 
I thank Albert for all the work he has done, and without his original work this plugin would not be possible. 
The original Netflix plugin did not support the newest feeds available from Netflix, so I saw fit to enhance the existing plugin. 
Albert deserves the vast majority of the credit on this project. 
Please check out his site:  www.albertbanks.com 
And feel free to check out his original Netflix plugin 

* Special thanks to David McDonald for contributing the text-only feed logic and functionality.
Please check out his site:  www.watchingred.com

* Special thanks to Ryan Stazel for bringing to light the depreciated MagpieRSS functionality and assisting in implementing SimplePie functions instead.
Please check out his site:  www.staze.org

Going Forward:  Although I started with Albert's original plugin as the base, 
I will be maintaining this as a different plugin, so it has been renamed and re-versioned. 

If you like this plugin - consider rating it and voting for compatibility.  

This plugin can be considered licensed under the GPL. 
Anyone may download, modify, and redistribute this source code as long as it remains open source. 



== Installation == 
1. Download and unzip the plugin (3 files). 
2. Make sure to put the files in a folder called "netflix-x2" in the wp-content/plugins folder of your webserver. 
3. Log into your wordpress administrative panel, click on 'Plugins' and activate the Netflix-X2 plugin. 
4. Configure the plugin settings, add your Netflix ID and also the type of content you wish to display. 
5. Use the new Widget in any widget-ready theme!
6. See readme.txt for more advanced usage options (worth the look!). 

NOTE: Your netflix id comes from your Netflix RSS feed pages.  
To find it, login to your Netflix account and go to your queue. At the bottom of the page, click the "RSS" link. 
Copy the id variable in one of the RSS links and paste it in the configuration panel. (Starts with PXXXXXXXXXXX)



== Usage == 
For a deeper look into plugin options, please visit the plugin website. 
www.ericvb.com/wordpress-netflix-plugin 



== Quick and Easy ==
For quick and easy usage, configure the options by clicking Netflix-X2 section under the "Settings" heading, and then you can simply drag the Netflix widget to your sidebar or call the netflix function from within any of your wordpress files (such as sidebar.php).

Example:

`<? netflix() ?>`



== Advanced Setup ==
For more advanced usage, there are more variables that can be set in the netflix function.

Example:

`<? netflix($number_movies, $feed, $display_type, $image_size, $before, $after, $netflix_id); ?>`


Options breakdown:

$number_movies - Number of feed elements displayed (default is 10)

$feed - Set which Netflix feed to use - Can be set to:
* home- displays movies you have at home currently (default)
* queue- displays movies in your queue in order
* instant- displays movies from your watch instantly queue
* recommendations- displays the movies Netflix has recommended for you
* recent- displays your recent rental activity
* reviews- displays the most recent movie reviews you.ve written

$display_type - What to display - Can be set to:
* title- shows only the movies title (default)
* image- display the movie cover image
* both- shows both the title and cover image
* raw- displays raw text from rss feed
* description- displays the full review text if you use the reviews feed, displays the movie summary info for all other feeds.  This includes the cover image.
* textonly- displays the TEXT ONLY of whatever feed is chosen. 

$image_size - If $type is set to "image" this defines the image size - Can be set to:
* small- small image (64px X 90px) (default)
* large- large image (110px X 154px)

$before - html appearing before each rss item (li, td, br tags for example)

$after - html appearing after each rss item

$netflix_id - your netflix id



== Advanced Options Usage == 
For example, this code would display large images for the next 10 items in your queue in an unordered list: 

`<ul> 
<? netflix(10, "queue", "image", "large", "<li>", "</li>", "P9999999999999999999999999999"); ?> 
</ul>` 


And this code would display the images and titles of the next 20 items in your watch instantly queue:

`<ul>
<? netflix(10, "instant", "both", "large", "<li>", "</li>", "P9999999999999999999999999999"); ?>
</ul>`



== Additional Functions ==
`<? netflix_movies($number_movies, $feed, $netflix_id) ?>` 

get_id() - Displays the Netflix movie ID

get_title() - Get the movie title

get_link() - Get link to movie on the Netflix website

get_description() - Reviews RSS Feed: This function retrieves the review you.ve written.
   All other RSS Feeds: This function retrieves the movie summary.

get_textonly() - This displays only the text of whatever feed is being used.  Embedded cover images are removed.

get_cover_image($size) - Displays the movie cover image (linked from Netflix.s site)

get_cover_image_source($size) - Gets the html link address for the cover image.


This example code of the netflix_movies function will show the last 8 movie reviews you wrote in a nicely formatted list:

`<ul> 
<? $movies = netflix_movies(8, "reviews"); 
foreach ($movies as $movie) { 
echo '<li>'; 
echo '<strong>',$movie->get_title(),'</strong>'; 
echo '<br />'; 
echo $movie->get_description(); 
echo '</li>'; 
} ?> 
</ul>`


One last example of code, this would select the next 25 movies from your queue, looping through them to display each cover image in a formatted html table.

`<? $movies = netflix_movies(25, "queue"); 
echo '<table><tr>'; 
foreach ($movies as $movie) { 
echo '<td>'; 
echo $movie->get_cover_image(); 
echo '</td>'; 
} 
echo '</tr></table>';  ?>`



== Known Issues ==
* Netflix has seemingly stopped providing RSS feeds for streaming customers.  This plugin only works for Netflix DVD subscribers due to this limitation.
* If the Reviews RSS feed is unavailable (Netflix has the RSS feed down), the plugin may return an "invalid option" php error.
* Plan to add error checking code to at least not throw a php error.



== Changelog ==
= Version 1.4.1 =
* Updated RSS urls to match current Netflix feed urls.  (http://dvd.netflix.com vs rss.netflix.com)
* Removed the "Movies at home" feed as Netflix has discontinued it as well.  

= Version 1.4 =
* Removed streaming and recent activity feeds from plugin, as Netflix currenlty does not provide these any more.
* Noted that this plugin can now only work for DVD plan subscirbers.  Streaming subscribers get no RSS feeds.
* Tested with latest version of WordPress 3.8

= Version 1.3.1 =
* No need to include the rss.php any longer since we use SimplePie.  Changed the require statments to call wp-includes/feed.php instead.
* Added a cache timer to counter the SimplePie 12 hour feed cache. Feeds are cached for 30 minutes now vs the 12 hour default.

= Version 1.3 =
* Configured REAL widget functionality instead of the hacky wanna-be widget.  Works much nicer with user being able to pick title.
* Moved the options page to be located under "Settings" vs under the plugins page.  Seems to be the standard now.
* Added a settings link on the plugins page for less clicks to get to configure the plugin.

= Version 1.2.2 =
* Switched fetch_rss functions from MagpieRSS to now use SimplePie function fetch_feed. This change done due to Magpie depreciation and non-active product.
* Removed ^M characters that had become present through Windows/Linux file editing.

= Version 1.2.1 =
* Updated rss include statement in netflix.php - was making call to depreciated file rss-functions.php, now using rss.php.

= Version 1.2 =
* Adding "text only" options for feed display.  This can be set in the plugin options screen, and can also be called directly by using the get_textonly() function.
* Thanks to David McDonald - www.watchingred.com

= Version 1.1 =
* Widgetized the netflix-x2 plugin. No configurable options yet though.
* Added new display option "text" to the netflix function.  This returns the user's written review if they have picked the reviews feed; and returns the movie description for any other feed selection.

= Version 1.0 =
* Started with the original 3.1 version of Albert's plugin.
* Added support for the Instant Queue RSS feed.
* Added support for the Reviews RSS feed.



== Roadmap ==
Future to do lists in upcomign releases.

* With a real widget in place, there is currently no new functionality planned at this time  However, suggestions are always welcome!! 
* Monitor Netflix RSS feeds for streaming to see if it is possible to incorporate them once again



== Contact ==
For any questions, concerns, or suggestions feel free to contact me by email: vanberge@gmail.com

