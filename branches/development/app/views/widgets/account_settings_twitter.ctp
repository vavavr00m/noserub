<?php if (Configure::read('NoseRub.allow_twitter_bridge')) { ?>
    <h2><?php __('Posting to Twitter.com'); ?></h2>
    <p>
       <?php __('We will ask you here for your Twitter username and password in order 
       to be able to send your micropublishing messages over to Twitter.com. 
       <strong>This is a bad thing!</strong> If you installed NoseRub on your
       own server, this is not <em>that</em> bad.<br />
       Otherwise make yourself clear, that the admin of this server can see your
       Twitter login credentials.<br />
       If you want to know more about why this is bad, please read 
       <a href="http://adactio.com/journal/1513/">this Article by Jeremy Keith</a>
       and come to <a href="http://noserub.com/discuss/">our discussion group</a> to
       discuss this issue!'); ?>
    </p>
    <form method="POST" action="<?php echo $this->here; ?>">
        <?php echo $noserub->fnSecurityTokenInput(); ?>
        <fieldset>
            <?php echo $form->checkbox('TwitterAccount.bridge_active'); ?><?php __('Post my <em>what are you doing</em> entries to Twitter.com'); ?>
    		<?php echo $form->input('TwitterAccount.username', array('label' => 'Twitter Username')); ?>
    		<?php echo $form->input('TwitterAccount.password', array('label' => 'Twitter Password', 'type' => 'password')); ?>
        </fieldset>
        <fieldset>
            <input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
        </fieldset>
    </form>
<?php } ?>