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
<?php echo $html->link('All tests', '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/all'); ?><br />
<?php echo $html->link('Component tests', '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/components'); ?><br />
<?php echo $html->link('Controller tests', '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/controllers'); ?><br />
<?php echo $html->link('Helper tests', '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/helpers'); ?><br />
<?php echo $html->link('Model tests', '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/models'); ?><br />
<?php echo $html->link('Plugin tests', '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/plugins'); ?><br /><br />
<?php if (!empty($groupTestNames)): ?>
	Group Tests:<br />
	<?php foreach ($groupTestNames as $groupTestName): ?>
		<?php echo $html->link($groupTestName, '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/group/'.$groupTestName); ?><br />
	<?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($pluginsWithTests)): ?>
	Plugins with Tests:<br />
	<?php foreach ($pluginsWithTests as $plugin): ?>
		<?php echo $html->link($plugin, '/jobs/'.NOSERUB_ADMIN_HASH.'/tests/plugin/'.$plugin); ?><br />
	<?php endforeach; ?>
<?php endif; ?>