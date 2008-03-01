<?php
$sex = array('img-small' => array(0 => '/images/profile/avatar/noinfo-small.gif',
                                  1 => '/images/profile/avatar/female-small.gif',
                                  2 => '/images/profile/avatar/male-small.gif'));

if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
    $static_base_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/avatars/';
} else {
    $static_base_url = FULL_BASE_URL . Router::url('/static/avatars/');
}
?>
<form id="SyndicationAddForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>Select a name. This is just visible for you and should help you to organize your feeds.</legend>
        <?php echo $form->input('Syndication.name', array('label' => '', 'value' => 'Some name', 'size' => 64)); ?>
    </fieldset>
    <fieldset>
        <legend>Choose, which of your activities should be included</legend>
        <ul>
        <?php foreach($accounts as $item) { ?>
            <li>
            <input type="checkbox" name="data[Syndication][Account][]" value="<?php echo $item['Account']['id']; ?>" />
            <img src="<?php echo Router::url('/images/icons/services/'.$item['Service']['icon']); ?>" alt="<?php echo $item['Service']['name']; ?>" />
            <?php echo $item['Account']['account_url']; ?>
            </li>
            
        <?php } ?>
        </ul>
    </fieldset>

<p class="infotext">
	Do you want to include your networks (contacts/friends) activities too?
	
</p>
<p class="infotext">
	<a class="specifynetwork addmore" href="#">Specify network activities</a>
</p>
<br />

    <fieldset class="mynetwork">
        <legend>Which of your networks (contacts/friends) activities should be included?</legend>
        <?php foreach($contacts as $contact) { ?>
            <?php if(empty($contact['WithIdentity']['Account'])) { continue; } ?>
            
            <p class="left">
                <?php
                    if($contact['WithIdentity']['photo']) {
                        if(strpos($contact['WithIdentity']['photo'], 'http://') === 0 ||
                           strpos($contact['WithIdentity']['photo'], 'http://') === 0) {
                            $contact_photo = str_replace('.jpg', '-small.jpg', $contact['WithIdentity']['photo']);
                        } else {
                            $contact_photo = $static_base_url . $contact['WithIdentity']['photo'].'-small.jpg';
                        }
                    } else {
                        $contact_photo = $sex['img-small'][$contact['WithIdentity']['sex']];
                    }
                ?>
                <img src="<?php echo $contact_photo; ?>" width="35" height="35" alt="<?php echo $contact['WithIdentity']['username']; ?>'s Picture" class="avatar" />
            </p>
            <p class="left">
                <?php echo $contact['WithIdentity']['firstname']; ?> <?php echo $contact['WithIdentity']['lastname']; ?><br />
                <strong><?php echo $contact['WithIdentity']['username']; ?></strong>
            </p>    
            <br class="clear" />
            <ul>
                <li>
                    <input class="check_all" type="checkbox" name="dummy" value="-1" />
                    All feeds of <?php echo $contact['WithIdentity']['username']; ?>
                </li>
                <li><a class="specify addmore" href="#">Specify the feeds</a></li>
			</ul>

			<ul class="accounts_of_contact">
                <?php foreach($contact['WithIdentity']['Account'] as $item) { ?>
                    <li>
                        <input type="checkbox" name="data[Syndication][Contact][<?php echo $contact['Contact']['with_identity_id']; ?>][Account][]" value="<?php echo $item['Account']['id']; ?>"/>
                        <img src="<?php echo Router::url('/images/icons/services/'.$item['Service']['icon']); ?>" alt="<?php echo $item['Service']['name']; ?>" /> <strong><?php echo $item['Service']['name']; ?>:</strong> <?php echo $item['Account']['account_url']; ?>
                    </li>
                <?php } ?>
            </ul>
            <hr />
        <?php } ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Create Feed"/>
    </fieldset>
</form>