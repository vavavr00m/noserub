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

function nr_show_NoseRub_location(){
	$apikey = get_option("nr_apikey");
	$url = get_option("nr_url");
	print("Noserub: ".$url);
}

function nr_update_locations(){
	$nr_apikey = get_option("nr_apikey");
	$nr_url = get_option("nr_url");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	$nr_user = $urlex["3"];
	$locations = "http://".$nr_domain."/api/".$nr_user."/".$nr_apikey."/sphp/locations";
	nr_apicall($locations,"locations");
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
		$data = file_get_contents($nrapi_url);
		if($data){
			update_option("nr_".$nrapi_name."_data",$data);
			update_option("nr_".$nrapi_name."_lastcall",time());
		}
	}
	$data = unserialize(get_option("nr_".$nrapi_name."_data"));
	return $data;
}

add_action('admin_menu','nr_Noserub_menu');

register_activation_hook(__FILE__,"nr_set_NoseRub_options");
register_deactivation_hook(__FILE__,"nr_unset_NoseRub_options");
?>