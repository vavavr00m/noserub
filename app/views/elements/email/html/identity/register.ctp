<?php
$verify_link = Router::url('/pages/verify/' . $hash . '/', true);
?>
<h1>
	<?php echo sprintf(__('Welcome to %s!', true), Configure::read('NoseRub.app_name')); ?>
</h1>
<p>
	<a href="<?php echo Router::url('/pages/verify/' . $hash . '/', true); ?>">
		<?php __('Please click here to verify your email address'); ?>
	</a><br />
	<br />
	<?php __('If you cannot click on that link, copy the following URL to your browser')?>:<br /><br />
	<?php echo Router::url('/pages/verify/' . $hash . '/', true); ?>
</p>
<p>
    <?php __('If you do not click on this link, the account will automatically be deleted after 14 days.'); ?>
</p>
<p>
    <?php echo __('Thanks!'); ?>
</p>