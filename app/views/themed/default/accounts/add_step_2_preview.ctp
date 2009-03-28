<div id="inhalt">
    <form id="AccountAddFormStep3" method="post" action="<?php echo $this->here ?>">
    <dl>
        <?php if(isset($data['title'])) { ?>
            <dt><?php __('Title'); ?></dt>
            <dd>
    		  <?php
    		  if ($data['service_id'] === 8):
              	echo $form->input('Account.title', array('value' => $data['title'], 'label' => false));
    		  else:
    		  	echo htmlentities($data['title']);
    		  endif;
    		  ?>
    		</dd>
        <?php } ?>
        <?php if (isset($service_types)): ?>
        	<dt><?php __('Servicetype'); ?></dt>
        	<dd>
        		<?php echo $form->select('Account.service_type_id', $service_types, array('3'), array(), false); ?>
        	</dd>
        <?php endif; ?>
    
        <dt><?php __('URL'); ?></dt>
        <dd><?php echo htmlentities($data['account_url']); ?></dd>
    
        <?php if($data['feed_url']) { ?>
            <dt><?php __('Feed'); ?></dt>
            <dd><?php echo htmlentities($data['feed_url']); ?></dd>
    
            <dt><?php __('Items'); ?></dt>
            <dd>
                <ul>
                    <?php foreach($data['items'] as $item) { ?>
                        <li><a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a></li>
                    <?php } ?>
                </ul>
            </dd>
        <?php } ?>
    </dl>
        <input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">
        <input class="submitbutton" type="submit" name="submit" value="<?php __('OK. Save it!'); ?>"/>
        <input class="submitbutton" type="submit" name="cancel" value="<?php __('Forget it'); ?>"/>
    </form>
</div>

<div id="rechts">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>