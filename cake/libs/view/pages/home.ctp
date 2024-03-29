<?php
/* SVN FILE: $Id$ */

/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
if (Configure::read() == 0):
	$this->cakeError('error404');
endif;
?>
<h2><?php echo sprintf(__('Release Notes for CakePHP %s.', true), Configure::version()); ?></h2>
<?php
echo $this->Html->link(__('Read the changelog', true), 'http://code.cakephp.org/wiki/changelog/1_3_0-alpha');

if (Configure::read() > 0):
	Debugger::checkSessionKey();
endif;
?>
<p>
	<?php
		if (is_writable(TMP)):
			echo '<span class="notice success">';
				__('Your tmp directory is writable.');
			echo '</span>';
		else:
			echo '<span class="notice">';
				__('Your tmp directory is NOT writable.');
			echo '</span>';
		endif;
	?>
</p>
<p>
	<?php
		$settings = Cache::settings();
		if (!empty($settings)):
			echo '<span class="notice success">';
					echo sprintf(__('The %s is being used for caching. To change the config edit APP/config/core.php ', true), '<em>'. $settings['engine'] . 'Engine</em>');
			echo '</span>';
		else:
			echo '<span class="notice">';
					__('Your cache is NOT working. Please check the settings in APP/config/core.php');
			echo '</span>';
		endif;
	?>
</p>
<p>
	<?php
		$filePresent = null;
		if (file_exists(CONFIGS.'database.php')):
			echo '<span class="notice success">';
				__('Your database configuration file is present.');
				$filePresent = true;
			echo '</span>';
		else:
			echo '<span class="notice">';
				__('Your database configuration file is NOT present.');
				echo '<br/>';
				__('Rename config/database.php.default to config/database.php');
			echo '</span>';
		endif;
	?>
</p>
<?php
if (isset($filePresent)):
	if (!class_exists('ConnectionManager')) {
		require LIBS . 'model' . DS . 'connection_manager.php';
	}
	$db = ConnectionManager::getInstance();
	@$connected = $db->getDataSource('default');
?>
<p>
	<?php
		if ($connected->isConnected()):
			echo '<span class="notice success">';
	 			__('Cake is able to connect to the database.');
			echo '</span>';
		else:
			echo '<span class="notice">';
				__('Cake is NOT able to connect to the database.');
			echo '</span>';
		endif;
	?>
</p>
<?php endif;?>
<h3><?php __('Editing this Page'); ?></h3>
<p>
<?php
__('To change the content of this page, create: APP/views/pages/home.ctp.<br />
To change its layout, create: APP/views/layouts/default.ctp.<br />
You can also add some CSS styles for your pages at: APP/webroot/css.');
?>
</p>

<h3><?php __('Getting Started'); ?></h3>
<p>
	<?php
		echo $this->Html->link(
			sprintf('<strong>%s</strong>%s', __('new', true ), __('CakePHP 1.2 Docs', true )),
			'http://book.cakephp.org',
			array('target' => '_blank', 'escape' => false)
		);
	?>
</p>
<p>
	<?php
		echo $this->Html->link(
			__('The 15 min Blog Tutorial', true),
			'http://book.cakephp.org/view/219/the-cakephp-blog-tutorial',
			array('target' => '_blank', 'escape' => false)
		);
	?>
</p>

<h3><?php __('More about Cake'); ?></h3>
<p>
<?php __('CakePHP is a rapid development framework for PHP which uses commonly known design patterns like Active Record, Association Data Mapping, Front Controller and MVC.'); ?>
</p>
<p>
<?php __('Our primary goal is to provide a structured framework that enables PHP users at all levels to rapidly develop robust web applications, without any loss to flexibility.'); ?>
</p>

<ul>
	<li>
		<?php
			echo $this->Html->link(__('Cake Software Foundation', true), 'http://www.cakefoundation.org/');
		?>
		<ul><li><?php __('Promoting development related to CakePHP'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('CakePHP', true), 'http://www.cakephp.org');
		?>
		<ul><li><?php __('The Rapid Development Framework'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('CakePHP Documentation', true), 'http://book.cakephp.org');
		?>
		<ul><li><?php __('Your Rapid Development Cookbook'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('CakePHP API', true), 'http://api.cakephp.org');
		?>
		<ul><li><?php __('Quick Reference'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('The Bakery', true), 'http://bakery.cakephp.org');
		?>
		<ul><li><?php __('Everything CakePHP'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('The Show', true), 'http://live.cakephp.org');
		?>
		<ul><li><?php __('The Show is a live and archived internet radio broadcast CakePHP-related topics and answer questions live via IRC, Skype, and telephone.'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('CakePHP Google Group', true), 'http://groups.google.com/group/cake-php');
		?>
		<ul><li><?php __('Community mailing list'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('irc.freenode.net #cakephp', true), 'irc://irc.freenode.net/cakephp');
		?>
		<ul><li><?php __('Live chat about CakePHP'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('CakePHP Code', true), 'http://code.cakephp.org/');
		?>
		<ul><li><?php __('For the Development of CakePHP (Tickets, Git browser, Roadmap, Changelogs)'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('CakeForge', true), 'http://www.cakeforge.org');
		?>
		<ul><li><?php __('Open Development for CakePHP'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('Book Store', true), 'http://astore.amazon.com/cakesoftwaref-20/');
		?>
		<ul><li><?php __('Recommended Software Books'); ?></li></ul>
	</li>
	<li>
		<?php
			echo $this->Html->link(__('CakePHP gear', true), 'http://www.cafepress.com/cakefoundation');
		?>
		<ul><li><?php __('Get your own CakePHP gear - Doughnate to Cake'); ?></li></ul>
	</li>
</ul>