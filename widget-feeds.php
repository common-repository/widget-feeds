<?php
/*
Plugin Name: Feeds Widget
Plugin URI: http://ketsugi.com/software/wordpress/feeds-widget-for-wordpress/
Description: A widget to display links to various context-sensitive RSS feeds.
Version: 1.3
Author: Joel Pan
Author URI: http://ketsugi.com/

  Copyright 2006-2007  Joel Pan <spamtastic@ketsugi.com>

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// This gets called at the plugins_loaded action
function widget_feeds_init () {

	// Check for the require API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;
	
	// This saves options and prints the widget's config form
	function widget_feeds_control () {
		$options = $newoptions = get_option('widget_feeds');
		if ( $_POST['feeds-submit'] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['feeds-title']));
			$newoptions['protocol'] = strip_tags(stripslashes($_POST['feeds-protocol']));
			$newoptions['icon'] = strip_tags(stripslashes($_POST['feeds-icon']));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_feeds', $options);
		}
?>
				<p style="text-align: center">
					<label for="feeds-title" style="line-height:35px;display:block;"><?php _e('Widget title:', 'widgets'); ?> <input type="text" id="feeds-title" name="feeds-title" value="<?php echo wp_specialchars($options['title'], true); ?>" /></label>
				</p>
				<p style="text-align: center">
					Protocol to use: <label><input style="vertical-align: middle" type="radio" name="feeds-protocol" id="feeds-protocol-http" value="http"<?php if ("http" == $options['protocol']) echo ' checked="checked"' ?> /> http://</label> <label><input style="vertical-align: middle" type="radio" name="feeds-protocol" id="feeds-protocol-feed" value="feed"<?php if ("feed" == $options['protocol']) echo ' checked="checked"' ?> /> feed://</label>
				</p>
				<p style="text-align: center">
					<label for="feeds-icon" style="line-height:35px;display:block;"><?php _e('Feed icon:', 'widgets')?> <input type="text" id="feeds-icon" name="feeds-icon" value="<?php echo wp_specialchars($options['icon'], true) ?>"/></label>
					<input type="hidden" name="feeds-submit" id="feeds-submit" value="1" />
				</p>				
<?php
	}
	// This prints the widget
	function widget_feeds ($args) {
		extract($args);
		$defaults = array('title'=>'&nbsp;', 'protocol'=>'feed', 'icon'=>'');
		$options = (array) get_option('widget_feeds');

		foreach ( $defaults as $key => $value )
			if ( !isset($options[$key]) )
				$options[$key] = $defaults[$key];
		
		echo $before_widget;
		echo $before_title . $options['title'] . $after_title;
		
		$rss2_url = set_protocol(get_bloginfo('rss2_url'), $options['protocol']);
		$comments_rss2_url = set_protocol(get_bloginfo('comments_rss2_url'), $options['protocol']);
		$icon_link = $options['icon'] == '' ? '' : "<img src='{$options['icon']}' alt='RSS feed icon' class='widget_feeds_icon' />";
?>
	<ul>
		<li class="feed"><?php echo $icon_link ?><a href="<?php echo $rss2_url ?>">All Entries</a></li>
		<li class="feed"><?php echo $icon_link ?><a href="<?php echo $comments_rss2_url ?>"><?php _e('All Comments') ?></a></li>
<?php
		//Context-sensitive RSS links
		if ( is_category() ) {
			$rss_link = get_rss_link( 'category', $options['protocol'] );
?>
		<li class="feed"><?php echo $icon_link ?><a href="<?php echo $rss_link ?>"><?php _e('This Category') ?></a></li>
<?php
		}
		else if ( is_single() ) {
			$rss_link = get_rss_link( 'post', $options['protocol'] );		
?>
		<li class="feed"><?php echo $icon_link ?><a href="<?php echo $rss_link ?>"><?php _e("This Post's Comments") ?></a></li>
<?php
		}
		else if ( is_search() ) {
			$rss_link = get_rss_link( 'search', $options['protocol'] );
?>
		<li class="feed"><?php echo $icon_link ?><a href="<?php echo $rss_link ?>"><?php _e('This Search') ?></a></li>
<?php
		}
		else if ( function_exists( 'is_tag' ) && is_tag() ) {
			$rss_link = get_rss_link( 'tag', $options['protocol'] );
?>
		<li class="feed"><?php echo $icon_link ?><a href="<?php echo $rss_link ?>"><?php _e('This Tag') ?></a></li>
<?php
		}
?>
	</ul>
<?php
		echo $after_widget;
	}
	
	// Tell Widgets about our new widget and its control
	register_sidebar_widget('Feeds', 'widget_feeds');
	register_widget_control('Feeds', 'widget_feeds_control');
}

/* Apart from the category link, getting the RSS links for posts and searches involves a lot of hacking at the request URI.
 * This should be considered TEMPORARY until proper function hooks are provided to do this.
 */
function get_rss_link ( $type, $protocol ) {
	$current_uri = parse_url( $_SERVER['REQUEST_URI'] );
	$current_uri['scheme'] = $protocol;
	$current_uri['host'] = $_SERVER['HTTP_HOST'];
	switch ( $type ) {
		case "category":
      //Get the category RSS
      global $wp_query;
      $cat_ID = $wp_query->get_queried_object_id();
      $rss_link = set_protocol(get_category_rss_link(0, $cat_ID, ''), $protocol);
			break;
		case "archive":
			break;
		case "post":
			if ( '' != get_settings( 'permalink_structure' ) ) {
			  $rss_link = trailingslashit( glue_url( $current_uri ) ) . 'feed/';
			}
			else {
			  $current_uri .= isset( $current_uri['query'] ) ? '&' : '';
			  $current_uri .= 'feed=rss2';
				$rss_link .= glue_url( $current_uri );
			}
			break;
		case "search":
			if ( '' != get_settings( 'permalink_structure' ) ) {
				parse_str( $current_uri['query'] );
				$rss_link = parse_url( trailingslashit( get_bloginfo( 'home' ) ) . 'search/' . urlencode($s) . '/feed/' );
				$rss_link['schema'] = $protocol;
				$rss_link = glue_url( $rss_link );
			}
			else {
				$current_uri['query'] .= '&feed=rss2';
				$rss_link = glue_url( $current_uri );
			}
			break;
		case "tag":
			if ( '' != get_settings( 'permalink_structure' ) ) {
				$rss_link = trailingslashit( glue_url( $current_uri ) ) . 'feed/';
			}
			else {
				$current_uri['query'] .= '&feed=rss2';
				$rss_link = glue_url( $current_uri );
			}
	}
	return $rss_link;
}

function set_protocol ( $url, $protocol ) {
	$url = parse_url($url);
	$url['scheme'] = $protocol;
	return glue_url($url);
}


function glue_url ( $parsed ) {
	if (! is_array($parsed)) return false;
	$uri = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '':'//'): '';
	$uri .= isset($parsed['user']) ? $parsed['user'].($parsed['pass']? ':'.$parsed['pass']:'').'@':'';
	$uri .= isset($parsed['host']) ? $parsed['host'] : '';
	$uri .= isset($parsed['port']) ? ':'.$parsed['port'] : '';
	$uri .= isset($parsed['path']) ? $parsed['path'] : '';
	$uri .= isset($parsed['query']) ? '?'.$parsed['query'] : '';
	$uri .= isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';
	return $uri;
}

// This function is hooked into the wp_head() action to add the feed link to the HTML header
function output_feed_link () {
	if ( is_single() ) {
		?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo( 'name' ) ?> This Post's Comments RSS 2.0 Feed" href="<?php echo get_rss_link( 'post', 'http' ) ?>" />
		<?php		
	}
	else if ( is_search() ) {
		?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo( 'name' ) ?> Search RSS 2.0 Feed" href="<?php echo get_rss_link( 'search', 'http' ) ?>" />
		<?php
	}
	else if ( function_exists( 'is_tag' ) && is_tag() ) {
		?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo( 'name' ) ?> Tag RSS 2.0 Feed" href="<?php echo get_rss_link( 'tag', 'http' ) ?>" />
		<?php
	}
}
?>
<?php
// Delay plugin execution to ensure Widgets has a chance to load first
add_action('widgets_init', 'widget_feeds_init');
add_action('wp_head', 'output_feed_link');
?>