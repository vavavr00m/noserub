<p>
    <?php echo sprintf(__('%s commented an entry of yours', true), $username) . ":\n\n"; ?>
</p>
<p>
    <a href="<?php echo Router::url('/entry/' . $entry_id . '/', true); ?>">
		<?php echo $entry_title; ?>
	</a>
</p>
<p>
    <em>
        <?php echo $comment; ?>
    </em>
</p>