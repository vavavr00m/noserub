<?php
    $is_owner = isset($session_identity) &&
                $session_identity['id'] == $data['Identity']['id'];
?>
<?php $flashmessage->render(); ?>
<div class="vcard">
    <?php echo $this->renderElement('identities/mini_profile'); ?>
</div>
<?php if($is_owner) {
    echo $html->link(__('Delete Entry', true), array('action' => 'delete', $data['Entry']['id'], '_t' => $security_token));
} else if($session_identity && $data['Entry']['service_type_id'] != 0) {
    $label = isset($already_marked) ? __('Unmark Entry as favorite', true) : __('Mark Entry as favorite', true);
    echo $html->link($label, array('action' => 'mark', $data['Entry']['id'], '_t' => $security_token));
} ?>
<hr class="clear" />
<div id="network">
    <ul class="networklist">
        <?php echo $this->renderElement('entries/row_view', array('item' => $data, 'permalink' => false)); ?>
    </ul>
</div>
<hr class="clear" />
<div>
<?php if(count($data['FavoritedBy']) > 0 ) {
    echo sprintf(__('Favorited by %d users: ', true), count($data['FavoritedBy']));
    $users = array();
    foreach($data['FavoritedBy'] as $item) {
        $users[] = '<a href="http://' . $item['Identity']['username'] .'">' . $item['Identity']['local_username'] . '</a>';
    }
    echo join(', ', $users);
} ?>
</div>