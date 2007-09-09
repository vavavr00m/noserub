<?php
/**
 * Dispatcher used by the testsuite.
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

	class TestDispatcher extends Dispatcher {
		var $controller = null;
	    
	    function _invoke (&$controller, $params, $missingAction = false) {
	        $return = parent::_invoke($controller, $params, $missingAction);
	        $this->controller =& $controller;
	        return $return;
	    }
	}
?>