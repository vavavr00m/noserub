<?php echo $this->element('identities/mini_profile', array('base_url_for_avatars' => $base_url_for_avatars)); ?>

<br class="clear" />

<form id="Identity/messages/new/Form" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php echo $form->input('Message.subject', array('label' => __('Subject', true) . ':', 'size' => 64)); ?>
        
        <div class="input"><label for="MessageText"><?php __('Message'); ?>:</label>
        <?php echo $form->textarea('Message.text', array('columns' => 80, 'rows' => 40)); ?>
		</div>
        <input class="submitbutton" type="submit" value="<?php __('Send'); ?>"/>
    </fieldset>
</form>