<?php
/*
Plugin Name: NoseRub for WordPress
Plugin URI: http://noserub.com/
Description: Gets the data from your NoseRub account and lets you use it on your weblog. Supernifty.<br />We advise you to install the <a href="http://wordpress.org/extend/plugins/simplepie-core">SimplePie Core</a> plugin, too. If you don't, NoseRub will still work, though.
Version: 0.0.2
Author: Dominik Schwind
Author URI: http://identoo.com/dominik
*/
require_once(dirname(__FILE__)."/nr_db_functions.php");
require_once(dirname(__FILE__)."/nr_optionsmenu.php");

function the_NoseRub_lifestream(){
	if(!class_exists('SimplePie')){
		require_once(dirname(__FILE__)."/simplepie/simplepie.inc");
	}
	
	require_once(dirname(__FILE__)."/nr_cache.php");
	
	$nr_url = get_option("nr_url");
	$nr_feed_url = get_option("nr_feed");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	if(substr($nr_feed_url,0,7) != "http://"){
		$nr_feed = "http://".$nr_domain.$nr_feed_url;
	} else {
		$nr_feed = $nr_feed_url;
	}
	$feed = new SimplePie();
	$feed->set_cache_class("NoseRub_cache");
	$feed->set_feed_url($nr_feed);
	$feed->init();
	$feed->handle_content_type();
	foreach($feed->get_items() as $item){ ?>
		<h3 class="title"><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h3>

		<?php echo $item->get_content(); ?>

		<p class="footnote"><?php echo $item->get_date(); ?></p>
		
		<?php
	}
}

function widget_NoseRub_lifestream($args){
	extract($args);
	echo $before_widget;
	echo $before_title . 'NoseRub Lifestream'. $after_title;
	if(!class_exists('SimplePie')){
		require_once(dirname(__FILE__)."/simplepie/simplepie.inc");
	}
	
	require_once(dirname(__FILE__)."/nr_cache.php");
	
	$nr_url = get_option("nr_url");
	$nr_feed_url = get_option("nr_feed");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	if(substr($nr_feed_url,0,7) != "http://"){
		$nr_feed = "http://".$nr_domain.$nr_feed_url;
	} else {
		$nr_feed = $nr_feed_url;
	}
	$feed = new SimplePie();
	$feed->set_cache_class("NoseRub_cache");
	$feed->set_feed_url($nr_feed);
	$feed->init();
	$feed->handle_content_type();
	echo "<ul>";
	foreach($feed->get_items() as $item){
		echo "<li><a href='".$item->get_permalink()."'>".$item->get_title()."</a></li>\n";
	}
	echo "</ul>";
	echo $after_widget;
}

function widget_NoseRub_location($args){
	extract($args);
	nr_update_locations();
	echo $before_widget;
	echo $before_title . 'NoseRub Location'. $after_title;
	$nr_locations = get_option("nr_locations_data");
	$nr_locs = unserialize($nr_locations);
	$nr_loc_array = array();
	foreach($nr_locs["data"]["Locations"] as $nr_loc){
		$nr_loc_array[$nr_loc["Location"]["id"]] = $nr_loc["Location"]["name"];
	}
	$nr_loc_now = $nr_locs["data"]["Identity"]["last_location_id"];
	echo "I am at ".$nr_loc_array[$nr_loc_now];
	echo $after_widget;
}

function nr_update_locations($cached = true){
	$nr_apikey = get_option("nr_apikey");
	$nr_url = get_option("nr_url");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	$nr_user = $urlex["3"];
	$locations = "http://".$nr_domain."/api/".$nr_user."/".$nr_apikey."/sphp/locations";
	nr_apicall($locations,"locations",$cached);
}

function nr_update_vcard($cached = true){
	$nr_apikey = get_option("nr_apikey");
	$nr_url = get_option("nr_url");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	$nr_user = $urlex["3"];
	$vcard = "http://".$nr_domain."/api/".$nr_user."/".$nr_apikey."/sphp/vcard";
	nr_apicall($vcard,"vcard",$cached);
}

function nr_update_contacts($cached = true){
	$nr_apikey = get_option("nr_apikey");
	$nr_url = get_option("nr_url");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	$nr_user = $urlex["3"];
	$contacts = "http://".$nr_domain."/api/".$nr_user."/".$nr_apikey."/sphp/contacts";
	nr_apicall($contacts,"contacts",$cached);
}

function nr_set_location($id){
	$nr_apikey = get_option("nr_apikey");
	$nr_url = get_option("nr_url");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	$nr_user = $urlex["3"];
	$locations = "http://".$nr_domain."/api/".$nr_user."/".$nr_apikey."/sphp/locations/set/$id";
	nr_apicall($locations,"setlocation",false);
}

function nr_update_feeds(){
	$nr_apikey = get_option("nr_apikey");
	$nr_url = get_option("nr_url");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	$nr_user = $urlex["3"];
	$locations = "http://".$nr_domain."/api/".$nr_user."/".$nr_apikey."/sphp/feeds";
	nr_apicall($locations,"feeds",false);
}

/**
 * Utility functions for the API call
 */
function nr_apicall($nrapi_url,$nrapi_name = false,$cached = true){
	if(!$nrapi_name){
		$nrapi_name = md5($nrapi_url);
	}
	$nrapi_lastcall = get_option("nr_".$nrapi_name."_lastcall");
	if(($cached == false)||((time()-$nrapi_lastcall) > 3600)){
		if(nr_url_exists($nrapi_url)){
			$data = file_get_contents($nrapi_url);
		} else {
			echo '<div id="message" class="error fade">';
			echo '<p>There was an error: Either your NoseRub is down or your API-Key is wrong.</p>';
			echo '</div>';
		}
		if($data){
			update_option("nr_".$nrapi_name."_data",$data);
			update_option("nr_".$nrapi_name."_lastcall",time());
		}
	}
	$data = unserialize(get_option("nr_".$nrapi_name."_data"));
	if($data["code"] > 0){
		echo '<div id="message" class="error fade">';
		echo '<p>There was an error:'.$data["msg"].'</p>';
		echo '</div>';
	}
	return $data;
}

function nr_url_exists($url) {
    // Version 4.x supported
    $handle   = curl_init($url);
    if (false === $handle)
    {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);   
    return $connectable;
}

function nr_init(){
	register_sidebar_widget('NoseRub Lifestream','widget_NoseRub_lifestream');
	register_sidebar_widget('NoseRub Location','widget_NoseRub_location');
}

add_action('admin_menu','nr_Noserub_menu');
add_action('widgets_init','nr_init');

register_activation_hook(__FILE__,"nr_set_NoseRub_options");
register_deactivation_hook(__FILE__,"nr_unset_NoseRub_options");
?>