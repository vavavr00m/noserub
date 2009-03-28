<div id="inhalt">
    <?php if(!$data) { ?>
    	<?php __('There is no such entry available.'); ?>
    <?php } else { ?>
    	<?php
    	    $is_owner = isset($session_identity) &&
    	                $session_identity['id'] == $data['Identity']['id'];
    	?>
    	<?php $flashmessage->render(); ?>
    	<div class="vcard">
    	    <?php echo $this->renderElement('identities/mini_profile'); ?>
    	</div>
    	<?php if($is_owner) {
    	    # see entries_controller::delete()
    	    # echo $html->link(__('Delete Entry', true), array('action' => 'delete', $data['Entry']['id'], '_t' => $security_token));
    	}
    	if($session_identity && $data['Entry']['service_type_id'] != 0) {
    	    $label = isset($already_marked) ? __('Unmark Entry as favorite', true) : __('Mark Entry as favorite', true);
    	    echo $html->link($label, array('action' => 'mark', $data['Entry']['id'], '_t' => $noserub->fnSecurityToken()));
    	} ?>
    	<hr class="clear" />
    	<div id="network">
    		<?php if($data['Entry']['account_id'] > 0) {
    			echo $html->link(__('External permalink', true), $data['Entry']['url']);
    		} ?>
    	    <ul class="networklist">
    	        <?php echo $this->renderElement('entries/row_view', array('item' => $data, 'permalink' => false)); ?>
    	    </ul>
    	</div>
    	<?php if(isset($session_identity) && 
    	         ($data['Entry']['account_id'] > 0 || $data['Entry']['service_type_id'] == 5)) { ?>
    	    <hr class="clear" />
    	    <div>
    	        <form id="MakeCommentForm" method="post" action="<?php echo $this->here ?>">
    	            <input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
    	            <fieldset>
    	        	    <?php echo $form->textarea('Comment.content', array('columns' => 80, 'rows' => 10)); ?>
    	        	</fieldset>
    	        	<fieldset>
    	        	    <input class="submitbutton" type="submit" name="submit" value="<?php __('Add comment'); ?>"/>
    	        	</fieldset>
    	        </form>
    	    </div>
    	<?php } ?>
    <?php } ?>
</div>