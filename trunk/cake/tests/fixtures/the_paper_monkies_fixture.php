<?php
/* SVN FILE: $Id: the_paper_monkies_fixture.php 751 2008-06-27 12:22:53Z dhofstet $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package			cake.tests
 * @subpackage		cake.tests.fixtures
 * @since			CakePHP(tm) v 1.2.0.4667
 * @version			$Revision$
 * @modifiedby		$LastChangedBy: dhofstet $
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * Short description for class.
 *
 * @package		cake.tests
 * @subpackage	cake.tests.fixtures
 */
class ThePaperMonkiesFixture extends CakeTestFixture {
/**
 * name property
 * 
 * @var string 'ThePaperMonkies'
 * @access public
 */
	var $name = 'ThePaperMonkies';
/**
 * fields property
 * 
 * @var array
 * @access public
 */
	var $fields = array(
		'apple_id' => array('type' => 'integer', 'length' => 10, 'null' => true),
		'device_id' => array('type' => 'integer', 'length' => 10, 'null' => true)
	);
/**
 * records property
 * 
 * @var array
 * @access public
 */
	var $records = array();
}

?>