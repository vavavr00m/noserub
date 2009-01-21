<h1>
    <?php echo sprintf(__('%s marked an entry of yours as favorite', true), $username) . ":\n\n"; ?>
</h1>
<p>
    <a href="<?php echo Router::url('/entry/' . $entry_id . '/', true); ?>">
		<?php echo $entry_title; ?>
	</a>
</p>