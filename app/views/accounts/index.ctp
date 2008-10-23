<?php $flashmessage->render(); ?>
<p class="infotext">
    Here you can add all your own social/online activities and import friends in your network.
</p>

<hr class="space" />

<div class="left">
    <?php echo $this->element('accounts/index'); ?>
</div>

<div class="right">
    <form method="POST" action="<?php echo $this->here; ?>">
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
        <table class="listing">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contact_accounts as $account) { ?>
                    <tr>
                        <td>
                            <img class="whoisicon" alt="<?php echo $account['Service']['name']; ?>" src="<?php echo Router::url('/images/icons/services/' . $account['Service']['icon']); ?>"/>
                            <?php echo $account['Service']['name']; ?>
                        </td>
                        <td>
                            <?php
                                $value = '';
                                foreach($data as $item) {
                                    if($item['Account']['service_id'] == $account['Service']['id']) {
                                        $value = $item['Account']['username'];
                                    }
                                    if($value) {
                                        break;
                                    }
                                }
                            ?>
                            <input type="text" size="32" value="<?php echo $value; ?>" name="data[Service][<?php echo $account['Service']['id']; ?>][username]">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <fieldset>
            <input class="submitbutton" type="submit" value="Save changes"/>
        </fieldset>
    </form>
</div>

<?php if(!defined('NOSERUB_ALLOW_TWITTER_BRIDGE') || NOSERUB_ALLOW_TWITTER_BRIDGE) { ?>
    <div class="left">
        <hr class="space" />
        <h2>Posting to Twitter.com</h2>
        <p id="message" class="alert">
           We will ask you here for your Twitter username and password in order 
           to be able to send your micropublishing messages over to Twitter.com. 
           <strong>This is a bad thing!</strong> If you installed NoseRub on your
           own server, this is not <em>that</em> bad.<br />
           Otherwise make yourself clear, that the admin of this server can see your
           Twitter login credentials.<br />
           If you want to know more about why this is bad, please read 
           <a href="http://adactio.com/journal/1513/">this Article by Jeremy Keith</a>
           and come to <a href="http://noserub.com/discuss/">our discussion group</a> to
           discuss this issue!
        </p>
        <form method="POST" action="<?php echo $this->here; ?>">
            <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
            <fieldset>
                <?php echo $form->checkbox('TwitterAccount.bridge_active'); ?>Post my <em>what are you doing</em> entries to Twitter.com	
        		<?php echo $form->input('TwitterAccount.username', array('label' => 'Twitter Username')); ?>
        		<?php echo $form->input('TwitterAccount.password', array('label' => 'Twitter Password', 'type' => 'password')); ?>
            </fieldset>
            <fieldset>
                <input class="submitbutton" type="submit" value="Save changes"/>
            </fieldset>
        </form>
    </div>
<?php } ?>