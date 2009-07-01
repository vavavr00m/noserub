<?php 

if(!$data) { 
    __('There is no such entry available.');
} else { ?>
	<div class="vcard">
	    <?php echo $this->renderElement('identities/mini_profile'); ?>
	</div>
    <?php if(Context::isLoggedIn() && $data['Entry']['service_type_id'] != 0) {
	    $label = isset($already_marked) ? __('Unmark Entry as favorite', true) : __('Mark Entry as favorite', true);
	    echo $html->link($label, array('action' => 'mark', $data['Entry']['id'], '_t' => $noserub->fnSecurityToken()));
	}
	if($data['Entry']['account_id'] > 0) {
		echo $html->link(__('External permalink', true), $data['Entry']['url']);
	}
	echo $this->renderElement('entries/view', array('item' => $data, 'permalink' => false));
}