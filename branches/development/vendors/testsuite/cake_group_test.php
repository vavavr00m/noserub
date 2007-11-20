<?php
/**
 * The base class for group tests.
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

	class CakeGroupTest extends GroupTest {
		
		/**
		 * Adds a test file to the group test. There is no error if the file does not exist.
		 * @param string $testFile The path to a test file. Depending on the setting of $useRelativePath it
		 * has to be a relative path (relative to app/tests) or an absolute path.
		 * @param boolean $useRelativePath Defines whether the $testFile parameter is a relative path.
		 */
		function addTestFile($testFile, $useRelativePath = true) {
			$testFolder = '';
			
			if ($useRelativePath) {
				$testFolder = APP.'tests'.DS;
			}
			
			if (file_exists($testFolder.$testFile)) {
				parent::addTestFile($testFolder.$testFile);
			}
		}
	}
?>