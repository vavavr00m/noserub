<?php
/*
Plugin Name: NoseRub for WordPress
Plugin URI: http://noserub.com/
Description: Gets the data from your NoseRub account and lets you use it on your weblog. Supernifty.
Version: 0.0.1
Author: Dominik Schwind
Author URI: http://identoo.com/dominik
*/

require_once (ABSPATH . WPINC . '/rss.php');

function the_NoseRub_lifestream(){
	$nr_url = get_option("nr_url");
	$nr_feed_url = get_option("nr_feed");
	$urlex = explode("/",$nr_url);
	$nr_domain = $urlex["2"];
	$nr_feed = "http://".$nr_domain.$nr_feed_url;
	$rss = fetch_rss($nr_feed);
//	print_r($rss);
	foreach($rss->items as $item){
		$i = "<div class='nr_item'>
			<a href='".$item["link"]."'>".$item["title"]."</a><br />
			".strftime("%c",strtotime($item["pubdate"]))."
		</div>";
		print($i);
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

/**
 * Utility functions to show the menu
 */
function nr_Noserub_options () {
	echo '<div class="wrap"><h2>NoseRub</h2>';
	if ($_REQUEST['submit']) {
		nr_update_NoseRub_options();
	}
	nr_print_NoseRub_options_form();
	echo '</div>';
}
function nr_Noserub_menu () {
	add_options_page(
		'Noserub',	//Title
		'Noserub',	//Sub-menu title
		'manage_options',	//Security
		__FILE__,	//File to open
		'nr_NoseRub_options'	//Function to call
	);  
}
function nr_update_NoseRub_options() {
	$updated = false;
	if ($_REQUEST['nr_apikey']) {
		update_option('nr_apikey', $_REQUEST['nr_apikey']);
		$updated = true;
	}
	if ($_REQUEST['nr_feed']) {
		update_option('nr_feed', $_REQUEST['nr_feed']);
		$updated = true;
	}
	if ($_REQUEST['nr_url']) {
		$nrurl = trim($_REQUEST['nr_url']);
		if((strpos($nrurl,"http://") === FALSE)||(strpos($nrurl,"http://") > 0)){
			$_REQUEST['nr_url'] = "http://".$_REQUEST['nr_url'];
		}
		update_option('nr_url', $_REQUEST['nr_url']);
		$updated = true;
	}
	if($_REQUEST['nr_location']){
		if(is_numeric($_REQUEST['nr_location'])){
			
		}
	}
	if ($updated) {
		echo '<div id="message" class="updated fade">';
		echo '<p>Options Updated</p>';
		echo '</div>';
	} else {
		echo '<div id="message" class="error fade">';
		echo '<p>Unable to update options</p>';
		echo '</div>';
	}
}
function nr_print_NoseRub_options_form(){
	$nr_apikey = get_option("nr_apikey");
	$nr_url = get_option("nr_url");
	$nr_feed = get_option("nr_feed");
	$f = "<form method='post'>
		<table class='optiontable'>
		<tr>
			<th scope='row'>Noserub-URL:</th>
			<td><input type='text' id='nr_url' name='nr_url' value='".$nr_url."'/></td>
		</tr>
		<tr>
			<th scope='row'>Noserub API-key:</th>
			<td><input type='text' id='nr_apikey' name='nr_apikey' value='".$nr_apikey."'/></td>
		</tr>
		</table>
		<p class='submit'>
			<input type='submit' value='Update Options &raquo;' name='submit' />
		</p>";
	if(($nr_apikey != "")&&($nr_url != "")){
		nr_update_locations();
		$nr_locations = get_option("nr_locations_data");
		$nr_locs = unserialize($nr_locations);
		$l = "<!-- <table class='optiontable'>
			<tr>
				<th scope='row'>Locations:</th>
				<td>
				<select name='nr_location' size='1'>
					<option value=''></option>";
		foreach($nr_locs["data"] as $ns_loc){
			$l.="<option value='".$ns_loc["Location"]["id"]."'>".$ns_loc["Location"]["name"]."</option>";
		}
		$l.="</select>
				</td>
			</tr>
		</table> -->";
		$f .= $l;
		nr_update_feeds();
		$nr_feeds = get_option("nr_feeds_data");
		$nr_feeds = unserialize($nr_feeds);
		$l = "<table class='optiontable'>
			<tr>
				<th scope='row'>Feed:</th>
				<td>
				<select name='nr_feed' size='1'>
					<option value=''></option>";
		foreach($nr_feeds["data"] as $nr_feeddata){
			$l.="<option value='".$nr_feeddata["Syndication"]["url"]["rss"]."'";
			if($nr_feeddata["Syndication"]["url"]["rss"] == $nr_feed){
				$l .= " selected='selected' ";
			}
			$l.=">".$nr_feeddata["Syndication"]["name"]."</option>";
		}
		$l.="</select>
				</td>
			</tr>
		</table>";
		$f .= $l;
	}
	$f .= "</form>";
	print $f;
}
/**
 * Utility functions for setting and unsetting the database options.
 */
function nr_set_NoseRub_options () {
	add_option("nr_apikey");
	add_option("nr_url");
	add_option("nr_feed");
	update_option('nr_url', "http://");
}
function nr_unset_NoseRub_options () {
	delete_option("nr_apikey");
	delete_option("nr_url");
	delete_option("nr_feed");
}

add_action('admin_menu','nr_Noserub_menu');

register_activation_hook(__FILE__,"nr_set_NoseRub_options");
register_deactivation_hook(__FILE__,"nr_unset_NoseRub_options");
?>