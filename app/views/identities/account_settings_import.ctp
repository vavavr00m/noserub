<p id="message" class="info">
    When you proceed, a NoseRub export file from the server
    <strong><?php echo $data['server']['base_url']; ?></strong>
    will be imported to the currently logged in user.<br />
    <br />
    The export file was for the user 
    <strong><?php echo $data['vcard']['username']; ?></strong>
    and contains <?php echo count($data['contacts']); ?> contacts, 
    <?php echo count($data['accounts']); ?> accounts, 
    <?php echo count($data['locations']); ?> locations and
    <?php echo count($data['feeds']); ?> feeds.
    <br /><br />
    <em>All the contacts and accounts you already have here, will not be overwritten.</em>
</p>
<h2>Are you sure to proceed?</h2>
<p>
    <?php echo $html->link('No, take me back', '/'.$session->read('Identity.local_username').'/settings/account/'); ?>
</p>
<p>
    <?php echo $html->link('Yes, import the data', '/'.$session->read('Identity.local_username').'/settings/account/import_data/' . $security_token . '/'); ?>
</p>