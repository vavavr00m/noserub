<?php
/**
 * Utility functions for setting and unsetting the database options.
 */
function nr_set_NoseRub_options () {
	add_option("nr_apikey");
	add_option("nr_url");
	add_option("nr_feed");
//	update_option('nr_url', "http://");
}
?>