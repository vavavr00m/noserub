<?php echo $this->renderElement('identities/mini_profile'); ?>

<br class="clear" />

<?php $flashmessage->render(); ?>

<form id="Identity/messages/new/Form" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php echo $form->input('Message.subject', array('label' => 'Subject:', 'error' => 'You need to give the message a subject.')); ?>
        
        <div class="input"><label for="MessageText">Message:</label>
        <?php echo $form->textarea('Message.text', array('columns' => 80, 'rows' => 40, 'error' => 'The message is empty.')); ?>
		</div>
        <input class="submitbutton" type="submit" value="Send"/>
    </fieldset>
</form>