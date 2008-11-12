<p id="message" class="info">
    <?php echo sprintf(__('When you proceed, a NoseRub export file from the server <strong>%s</strong> will be imported to the currently logged in user.', true), $data['server']['base_url']);?>
    <br />
    <br />
    <?php echo sprintf(__('The export file was for the user <strong>%s</strong> and contains %d contacts, %d accounts and %d locations.', true), $data['vcard']['username'], count($data['contacts']), count($data['accounts']), count($data['locations'])); ?>
    <br /><br />
    <em>
        <?php __('All the contacts and accounts you already have here, will not be overwritten.'); ?>
    </em>
</p>
<h2><?php __('Are you sure to proceed?'); ?></h2>
<p>
    <?php echo $html->link(__('No, take me back', true), '/'.$session->read('Identity.local_username').'/settings/account/'); ?>
</p>
<p>
    <?php echo $html->link(__('Yes, import the data', true), '/'.$session->read('Identity.local_username').'/settings/account/import_data/' . $security_token . '/'); ?>
</p>