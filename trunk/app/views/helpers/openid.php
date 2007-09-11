<?php

	class OpenidHelper extends AppHelper {
		
		/**
		 * Creates a link which points to an OpenID server.
		 * 
		 * @param string $url OpenID server url.
		 * @param boolean $inline If set to false the link gets added to the head section of
		 * the HTML document. Make sure your layout contains "echo $scripts_for_layout;"
		 * @return the link or nothing, depending on the $inline parameter
		 */
		function serverLink($url, $inline = true) {
			$link = '<link rel="openid.server" href="'.$this->url($url, true).'" />';
			
			if ($inline) {
				return $link;
			} else {
				$view = ClassRegistry::getObject('view');
				$view->addScript($link);
			}
		}
	}
?>