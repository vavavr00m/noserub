<form id="SyndicationAddForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>Select a name. This is just visible for you and should help you to organize your feeds.</legend>
        <?php echo $form->input('Syndication.name', array('label' => '', 'value' => 'Some name', 'size' => 64)); ?>
    </fieldset>
    <fieldset>
        <legend>Choose, which of your activities should be included</legend>
        <?php foreach($accounts as $item) { ?>
            <input type="checkbox" name="data[Syndication][Account][]" value="<?php echo $item['Account']['id']; ?>"/>
            <img src="<?php echo Router::url('/images/icons/services/'.$item['Service']['icon']); ?>" />
            <?php echo $item['Account']['account_url']; ?>
            <br />
        <?php } ?>
    </fieldset>
    <fieldset>
        <legend>Which of your networks activities should be included?</legend>
        <?php foreach($contacts as $contact) { ?>
            <?php if(empty($contact['WithIdentity']['Account'])) { continue; } ?>
            <?php echo $contact['WithIdentity']['username']; ?>
            <br />
            <?php foreach($contact['WithIdentity']['Account'] as $item) { ?>
                <input type="checkbox" name="data[Syndication][Contact][<?php echo $contact['Contact']['with_identity_id']; ?>][Account][]" value="<?php echo $item['id']; ?>"/>
                <img src="<?php echo Router::url('/images/icons/services/'.$item['Service']['icon']); ?>" />
                <?php echo $item['account_url']; ?>
                <br />
            <?php } ?>
        <?php } ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Create Feed"/>
    </fieldset>
</form>