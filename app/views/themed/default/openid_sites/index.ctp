<div id="bd-main" class="with-sidebar">
    <div id="bd-main-hd">
	</div>
	<div id="bd-main-bd">
        <p>
            <?php __('Below is a list of sites you have visited. You will not be prompted to approve identifying yourself to 
            sites you have checked.'); ?>
        </p>
        <form method="post" action="<?php echo $this->here; ?>">
        	<table class="listing">
        		<tr>
        			<th><?php __('Site'); ?></th>
        			<th><?php __('Email'); ?></th>
        			<th><?php __('Fullname'); ?></th>
        			<th><?php __('Gender'); ?></th>
        		</tr>
        		<?php foreach ($openidSites as $openidSite): ?>
        			<tr>
        				<td>
        					<?php echo $form->checkbox('OpenidSite.'.$openidSite['OpenidSite']['id'], array('checked' => $openidSite['OpenidSite']['allowed'] ? true : false)); ?>
        					<?php echo $openidSite['OpenidSite']['url']; ?>
        				</td>
        				<td><?php echo ($openidSite['OpenidSite']['email']) ? 'x' : '-'; ?></td>
        				<td><?php echo ($openidSite['OpenidSite']['fullname']) ? 'x' : '-'; ?></td>
        				<td><?php echo ($openidSite['OpenidSite']['gender']) ? 'x' : '-'; ?></td>
        			</tr>
        		<?php endforeach; ?>
        	</table>
        	<p>
        		<input class="submitbutton" type="submit" name="Submit" value="<?php __('Save changes'); ?>" />
        	</p>
        </form>
    </div>
    <div id="bd-main-sidebar">
		<?php echo $noserub->widgetSettingsNavigation(); ?>
	</div>
</div>