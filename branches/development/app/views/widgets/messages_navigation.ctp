<div class="widget widget-messages-navigation">
    <ul>	
        <li><?php echo $html->link(__('Inbox', true), '/messages/inbox/') ?></li>
        <li><?php echo $html->link(__('Sent messages', true), '/messages/sent/') ?></li>
    </ul>
    <hr />
    <?php echo $html->link(__('Create a new message', true), '/messages/add/') ?>
</div>