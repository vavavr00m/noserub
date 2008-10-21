<div class="vcard">
<?php echo $this->renderElement('identities/mini_profile'); ?>
</div>
<hr class="clear" />
<div id="network">
    <ul class="networklist">
        <?php echo $this->renderElement('entries/row_view', array('item' => $data, 'permalink' => false)); ?>
    </ul>
</div>