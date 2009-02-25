<?php echo sprintf(__('Welcome to %s!', true), Configure::read('context.network.name')) . "\n\n"; ?>
<?php echo __('Please click here to verify your email address', true) . ":\n\n"; ?>
<?php echo Router::url('/pages/verify/' . $hash . '/', true) . "\n\n"; ?>
<?php echo __('If you do not click on this link, the account will automatically be deleted after 14 days.', true) . "\n\n"; ?>
<?php echo __('Thanks!', true) . "\n"; ?>