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
			return $this->__createTag('<link rel="openid.server" href="?" />', $url, $inline);
		}
		
		/**
		 * Creates a metatag which points to the XRDS file.
		 * 
		 * @param string $url Url of XRDS file.
		 * @param boolean $inline If set to false the metatag gets added to the head section of
		 * the HTML document. Make sure your layout contains "echo $scripts_for_layout;"
		 * @return the metatag or nothing, depending on the $inline parameter
		 */
		function xrdsLocation($url, $inline = true) {
			return $this->__createTag('<meta http-equiv="X-XRDS-Location" content="?" />', $url, $inline);
		}
		
		function __createTag($tag, $url, $inline) {
			$newTag = str_replace('?', $this->url($url, true), $tag);
			
			if ($inline) {
				return $newTag;
			} else {
				$view = ClassRegistry::getObject('view');
				$view->addScript($newTag);
			}
		}
	}
?>