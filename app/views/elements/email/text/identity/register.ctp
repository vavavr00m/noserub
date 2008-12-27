<?php echo sprintf(__('Welcome to %s!', true), Configure::read('NoseRub.app_name')) . "\n\n"; ?>
<?php __('Please click here to verify your email address') . ":\n\n"; ?>
<?php echo Router::url('/pages/verify/' . $hash . '/') . "\n\n"; ?>
<?php __('If you do not click on this link, the account will automatically be deleted after 14 days.') . "\n\n"; ?>
<?php echo __('Thanks!') . "\n"; ?>