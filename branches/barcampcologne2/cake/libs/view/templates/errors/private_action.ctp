<?php
/* SVN FILE: $Id: private_action.ctp 4605 2007-03-09 23:26:37Z phpnut $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.view.templates.errors
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 4605 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-03-10 00:26:37 +0100 (Sat, 10 Mar 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<h1><?php echo sprintf(__('Private Method in %s', true), $controller);?></h1>
<p class="error"><?php echo sprintf(__("You are seeing this error because the private class method <em>%s</em> should not be accessed directly.", true), $action);?></p>
<p><span class="notice"><strong><?php __('Notice'); ?>: </strong>
<?php echo sprintf(__('If you want to customize this error message, create %s', true), APP_DIR.DS."views/errors/private_action.ctp");?></span></p>