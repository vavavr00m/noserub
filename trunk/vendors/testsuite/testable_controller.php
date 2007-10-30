<?php
/**
 * Base controller class which is automatically injected when running tests. 
 *
 * Copyright (c) 2007, Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
*/

	class TestableController extends Controller {
		var $redirectUrl = null;
		var $redirectStatus = null;
		var $flashMessage = null;
		
		function flash($message, $url, $pause = 1) {
			$this->autoRender = false;
			$this->flashMessage = $message;
			$this->redirectUrl = $url;
		}
		
		function redirect($url, $status = null, $exit = false) {
			$this->autoRender = false;
			$this->redirectUrl = Router::url($url, false);
			$this->redirectStatus = $status;
		}
		
		function render($action = null, $layout = null, $file = null) {
			ob_start();
			parent::render($action, $layout, $file);
			ob_end_clean();
		}
	}
?>