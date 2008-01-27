<?php
$sex = array('img' => array(0 => Router::url('/images/profile/avatar/noinfo.gif'),
                             1 => Router::url('/images/profile/avatar/female.gif'),
                             2 => Router::url('/images/profile/avatar/male.gif')),
              'img-small' => array(0 => Router::url('/images/profile/avatar/noinfo-small.gif'),
                                   1 => Router::url('/images/profile/avatar/female-small.gif'),
                                   2 => Router::url('/images/profile/avatar/male-small.gif')),
              'he' => array(0 => 'he/she',
                            1 => 'she',
                            2 => 'he'),
              'him' => array(0 => 'him/her',
                             1 => 'her',
                             2 => 'him'));
                                    
if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
    $static_base_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/avatars/';
} else {
    $static_base_url = FULL_BASE_URL . Router::url('/static/avatars/');
}
?>
<?php $flashmessage->render(); ?>
<?php if($contact['WithIdentity']['photo']) {
    if(strpos($contact['WithIdentity']['photo'], 'http://') === 0 ||
       strpos($contact['WithIdentity']['photo'], 'https://') === 0) {
        # contains a complete path, eg. from not local identities
        $contact_photo = $contact['WithIdentity']['photo'];
    } else {
        $contact_photo = $static_base_url . $contact['WithIdentity']['photo'].'.jpg';
    }	                
} else {
    $contact_photo = $sex['img'][$contact['WithIdentity']['sex']];
} ?>         
<dl id="hcard-<?php echo $contact['WithIdentity']['local_username']; ?>" class="vcards contacts <?php echo $contact['WithIdentity']['local']==1 ? '' : 'externalcontact'; ?>">
    <dt>
        <a href="<?php echo 'http://' . $contact['WithIdentity']['username']; ?>">
    	    <img class="photo" src="<?php echo $contact_photo; ?>" width="80" height="80" alt="<?php echo $contact['WithIdentity']['single_username']; ?>'s Picture" />
    	</a>
    </dt>                     
    <dt>
        <a class="url nickname" href="<?php echo 'http://' . $contact['WithIdentity']['username']; ?>"><?php echo $contact['WithIdentity']['single_username']; ?></a>
    </dt>
	<dd class="fn"><?php echo $contact['WithIdentity']['name']; ?></dd>
		
	<!-- send e-Mail -->
	<?php if($contact['WithIdentity']['local'] == 1 && $contact['WithIdentity']['allow_emails'] != 0) { ?>
	    <dd class="sendmail">
		    <img src="<?php echo Router::url('/images/icons/services/email.gif'); ?>" height="16" width="16" alt="e-Mail" class="sendmail_icon" /> <a href="http://<?php echo $contact['WithIdentity']['username']; ?>/messages/new/">Send e-Mail</a>
		</dd>
	<?php } ?>
</dl>	
	
<br class="clear" />

<h2>Define your relationship</h2>
<p>
    Click on a term to choose it and/or add a term to specify the relationship and group your contacts.
</p>

<br class="clear" />

<form id="DefineContactTypesForm" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        Your relationship: <span id="taglist"></span>
    </fieldset>
    <fieldset>
	    <?php foreach ($noserub_contact_types as $contact_type) { ?>
    		<?php echo $form->checkbox('NoserubContactType.' . $contact_type['NoserubContactType']['id'], 
    		                           array('checked' => in_array($contact_type['NoserubContactType']['id'], $selected_noserub_contact_types))); 
    		?>&nbsp;
    		<span class="contact_type"><?php echo $contact_type['NoserubContactType']['name']; ?></span>
    	<?php } ?>
    	<?php foreach ($contact_types as $contact_type) { ?>
    	    <?php echo $form->checkbox('ContactType.' . $contact_type['ContactType']['id'], 
    	                               array('checked' => in_array($contact_type['ContactType']['id'], $selected_contact_types))); ?>
    		<span class="contact_type"><?php echo $contact_type['ContactType']['name']; ?></span>
    	<?php } ?>
    </fieldset>
    <fieldset>
	    <?php echo $form->input('Tags.own', array('label' => 'Add your own (separated by space):')); ?>
	</fieldset>
	<fieldset>
	    <input class="submitbutton" type="submit" name="submit" value="Save"/>
	</fieldset>
<?php echo $form->end(); ?>