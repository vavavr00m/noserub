<?php
/* SVN FILE: $Id: index.thtml 24 2007-04-02 15:21:59Z dho $ */
/**
 * Test suite view.
 *
 * PHP versions 4 and 5
 *
 * CakePHP Test Suite <http://cakeforge.org/projects/testsuite/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright	 Copyright (c) 2006, Cake Software Foundation, Inc.
 * @package      tests
 * @since        CakePHP Test Suite v 1.0.0.0
 * @version      $Revision: 24 $
 * @modifiedby   $LastChangedBy: dho $
 * @lastmodified $Date: 2007-04-02 17:21:59 +0200 (Mon, 02 Apr 2007) $
 * @license      http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
 ?>
<?php echo $html->link('All tests', '/tests/all'); ?><br />
<?php echo $html->link('Component tests', '/tests/components'); ?><br />
<?php echo $html->link('Controller tests', '/tests/controllers'); ?><br />
<?php echo $html->link('Helper tests', '/tests/helpers'); ?><br />
<?php echo $html->link('Model tests', '/tests/models'); ?><br />
<?php echo $html->link('Plugin tests', '/tests/plugins'); ?><br /><br />
<?php if (!empty($groupTestNames)): ?>
	Group Tests:<br />
	<?php foreach ($groupTestNames as $groupTestName): ?>
		<?php echo $html->link($groupTestName, '/tests/group/'.$groupTestName); ?><br />
	<?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($pluginsWithTests)): ?>
	Plugins with Tests:<br />
	<?php foreach ($pluginsWithTests as $plugin): ?>
		<?php echo $html->link($plugin, '/tests/plugin/'.$plugin); ?><br />
	<?php endforeach; ?>
<?php endif; ?>