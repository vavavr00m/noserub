<?php echo sprintf(__('%s commented an entry of yours', true), $username) . ":\n\n"; ?>
<?php echo $entry_title . "\n\n"; ?>
<?php echo Router::url('/entry/' . $entry_id . '/') . "\n\n"; ?>
<?php echo $comment; ?>