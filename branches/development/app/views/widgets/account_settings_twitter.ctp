<div class="widget widget-account-settings-twitter">
<?php if (Configure::read('NoseRub.allow_twitter_bridge')) { ?>
    <h2><?php __('Posting to Twitter.com'); ?></h2>
    <p>
       <?php __('We will ask you here for your Twitter username and password in order 
       to be able to send your micropublishing messages over to Twitter.com. 
       <strong>This is a bad thing!</strong> If you installed NoseRub on your
       own server, this is not <em>that</em> bad.') ?>
	</p>
	<p>
       <?php __('Otherwise make yourself clear, that the admin of this server can see your
       Twitter login credentials.') ?>
	</p>
	<p>
       <?php __('If you want to know more about why this is bad, please read 
       <a href="http://adactio.com/journal/1513/">this Article by Jeremy Keith</a>
       and come to <a href="http://noserub.com/discuss/">our discussion group</a> to
       discuss this issue!'); ?>
    </p>
	<?php
    echo $form->create(array('url' => $this->here));
	echo $noserub->fnSecurityTokenInput();
    echo $form->checkbox('TwitterAccount.bridge_active') . '&nbsp;';  
	__('Post my <em>what are you doing</em> entries to Twitter.com');
    echo $form->input('TwitterAccount.username', array('label' => 'Twitter Username'));
    echo $form->input('TwitterAccount.password', array('label' => 'Twitter Password', 'type' => 'password'));
    echo $form->end(array('label' => __('Save changes', true)));
	?>
<?php } ?>
</div>
