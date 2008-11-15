<?php
    $url = Router::url('/' . $session_identity['local_username']);
    if(Configure::read('Noserub.use_cdn')) {
        $feed_url = 'http://s3.amazonaws.com/' . Configure::read('Noserub.cdn_s3_bucket') . '/feeds/';
    } else {
        $feed_url = Configure::read('Noserub.full_base_url') . $url . '/feeds/';
    } 
?>
<?php $flashmessage->render(); ?>
<p class="infotext">
    <?php __('You can create Feeds from your own social activities or those of friends in your network. This feeds then can be used by your RSS-Reader or you can integrate it on your website to show everyone, what you did in the last couple of hours and days.'); ?>
</p>

<hr class="space" />

<h2><?php __('Your feeds'); ?></h2>
<p class="infotext">
<a href="<?php echo $url . '/settings/feeds/add/'; ?>" class="addmore"><?php __('Create a new feed'); ?></a>
</p>
<?php if(!$data) { ?>
    <p class="infotext">
        <?php __('You did not create any feeds yet.'); ?>
    </p>
<?php } else { ?>
    <table class="listing">
        <thead>
        <tr>
            <th><?php __('Name'); ?></th>
            <th><?php __('Links'); ?></th>
            <th></th>
        </tr>
        </thead>
        <?php foreach($data as $item) { ?>
            <tr>
                <td><?php echo $item['Syndication']['name']; ?></td>
                <td>
                    <a href="<?php echo $feed_url . $item['Syndication']['hash']; ?>.rss">RSS</a> - 
                    <a href="<?php echo $feed_url . $item['Syndication']['hash']; ?>.js">JSON</a> -
                    <a href="<?php echo $feed_url . $item['Syndication']['hash']; ?>.sphp">SPHP</a>
                </td>
                <td>
                	<ul>
                   		<li class="delete icon"><?php echo $html->link(__('Delete', true), '/' . $session_identity['local_username'] . '/settings/feeds/'.  $item['Syndication']['id'] . '/delete/' . $security_token . '/'); ?></li>
                   	</ul>
                </td>
            </tr>
        <?php } ?>
    </table>
    <p class="infotext">
        <a href="<?php echo $url . '/settings/feeds/add/'; ?>" class="addmore"><?php __('Create a new feed'); ?></a>
    </p>
<?php } ?>

<hr class="space" />

<h2><?php __('Generic feed'); ?></h2>
<form id="IdentityGenericFeedsForm" method="post" action="<?php echo $this->here; ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <fieldset>
        <legend>
            <?php sprintf(__('If you want a feed with all your updates being available at<br />%s<br />please enable the following option.', true), $feed_url . $session_identity['local_username'] . '.(rss|js|sphp)'); ?>
        </legend>
        <?php echo $form->checkbox('Identity.generic_feed'); ?><?php __('Enable generic feed'); ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="<?php __('Save changes'); ?>"/>
    </fieldset>
</form>