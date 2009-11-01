<?php
    $options = array(
        0 => __('No one', true),
        1 => __('Contacts', true),
        2 => __('Registered users', true)
    );
?>
<fieldset>
    <h2><?php __('Updates'); ?></h2>
    <p>
        <?php __('We want to show not logged in users, what is going on inside. 
        So, if you give us your permisson, we will show your updates
        there for everyone.'); ?>
    </p>
    <input type="radio" name="data[Identity][frontpage_updates]" value="1"<?php echo $this->data['Identity']['frontpage_updates'] == 1 ? ' checked="checked"' : ''; ?>>&nbsp;<?php __('show my updates on frontpage'); ?><br />
    <input type="radio" name="data[Identity][frontpage_updates]" value="0"<?php echo $this->data['Identity']['frontpage_updates'] == 0 ? ' checked="checked"' : ''; ?>>&nbsp;<?php __("don't show them"); ?>
</fieldset>
<fieldset>
    <h2><?php __('Communication'); ?></h2>
    
	<label><?php __('Which persons may send me e-Mails?'); ?></label>
    <?php echo $form->select('Identity.allow_emails', $options, null, array(), false); ?><br />
    
	<label><?php __('Notify me when the following happens'); ?>:</label><br />
    <?php echo $form->checkbox('Identity.notify_contact'); ?>
    <?php __('Someone adds me as contact'); ?>
	<br />
    <?php echo $form->checkbox('Identity.notify_comment'); ?>
    <?php __('Someone makes a comment on one of my entries'); ?>
	<br />
    <?php echo $form->checkbox('Identity.notify_favorite'); ?>
    <?php __('Someone marks one of my entries as favorite'); ?>
</fieldset>
