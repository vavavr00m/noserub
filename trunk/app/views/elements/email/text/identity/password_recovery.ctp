<?php echo sprintf(__('You requested a new password for %s', true), 'http://' . $username) . "\n\n"; ?>
<?php echo __('Please click here to set a new password', true) . ":\n\n"; ?>
<?php echo Router::url('/pages/password/recovery/' . $recovery_hash . '/', true); ?>
<?php echo "\n\n"; ?>
<?php __('Thanks!'); ?>