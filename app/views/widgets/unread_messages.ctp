<?php 

$unreadMessages = Context::unreadMessages();
if($unreadMessages) { ?>
    <div class="widget widget-unread-messages">
        <p class="notifications">
    	    <?php if($unreadMessages == 1) {
    	        echo sprintf(__('%d new message', true), $unreadMessages); 
    	    } else {
    	        echo sprintf(__('%d new messages', true), $unreadMessages);
    	    } ?><br />
    	    <?php echo $html->link(__('Go to your Inbox', true), '/messages/inbox/'); ?>
        </p>
    </div>
<?php } ?>