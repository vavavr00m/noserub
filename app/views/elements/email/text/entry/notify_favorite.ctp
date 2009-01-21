<?php echo sprintf(__('%s marked an entry of yours as favorite', true), $username) . ":\n\n"; ?>
<?php __('Please click here to verify your email address') . ":\n\n"; ?>
<?php echo $entry_title . "\n\n"; ?>
<?php echo Router::url('/entry/' . $entry_id . '/') . "\n\n"; ?>