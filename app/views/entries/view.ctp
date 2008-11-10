<?php
    $is_owner = isset($session_identity) &&
                $session_identity['id'] == $data['Identity']['id'];
?>
<div class="vcard">
    <?php echo $this->renderElement('identities/mini_profile'); ?>
</div>
<?php if($is_owner) {
    echo $html->link(__('Delete Entry', true), array('action' => 'delete', $data['Entry']['id'], $security_token));
}?>
<hr class="clear" />
<div id="network">
    <ul class="networklist">
        <?php echo $this->renderElement('entries/row_view', array('item' => $data, 'permalink' => false)); ?>
    </ul>
</div>
<div>
</div>