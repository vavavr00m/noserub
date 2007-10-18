<form id="SyndicationAddForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>Select a name. This is just visible for you and should help you to organize your feeds.</legend>
        <?php echo $form->input('Syndication.name', array('label' => '', 'value' => 'Some name', 'size' => 64)); ?>
    </fieldset>
    <fieldset>
        <legend>Choose, which of your activities should be included</legend>
        <ul>
        <li>
        <?php foreach($accounts as $item) { ?>
            <li>
            <input type="checkbox" name="data[Syndication][Account][]" value="<?php echo $item['Account']['id']; ?>" />
            <img src="<?php echo Router::url('/images/icons/services/'.$item['Service']['icon']); ?>" alt="<?php echo $item['Service']['name']; ?>" />
            <?php echo $item['Account']['account_url']; ?>
            </li>
            
        <?php } ?>
        </ul>
    </fieldset>

    <fieldset>
        <legend>Which of your networks (contacts/friends) activities should be included?</legend>
        <?php foreach($contacts as $contact) { ?>
            <?php if(empty($contact['WithIdentity']['Account'])) { continue; } ?>
            
            <p class="left">
            <img src="/images/profile/avatar/female-small.gif" width="35" height="35" alt="poolbabe's Picture" />
            </p>
            
            <p class="left">
            <?php echo $contact['WithIdentity']['firstname']; ?> <?php echo $contact['WithIdentity']['lastname']; ?><br />
            <strong><?php echo $contact['WithIdentity']['username']; ?></strong>
            </p>
            
            <br class="clear" />

            <ul>
            <li>
            <input type="checkbox" name="data[Syndication][Account][]" value="XXXXXXXXXXXXXXXXXXX" />
            All feeds of <?php echo $contact['WithIdentity']['username']; ?>
            </li>
            <li><a href="#">Specify the feeds</a> +</li>
			</ul>

			<ul id="SyndicationServices">
            <?php foreach($contact['WithIdentity']['Account'] as $item) { ?>
                <li>
                <input type="checkbox" name="data[Syndication][Contact][<?php echo $contact['Contact']['with_identity_id']; ?>][Account][]" value="<?php echo $item['id']; ?>"/>
                <img src="<?php echo Router::url('/images/icons/services/'.$item['Service']['icon']); ?>" alt="<?php echo $item['Service']['name']; ?>" /> <strong><?php echo $item['Service']['name']; ?>:</strong> <?php echo $item['account_url']; ?>
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