<div id="bd-main" class="with-sidebar">
	<div id="bd-main-bd">
		<?php if ($hasTwitterAccount): ?>
			Twitter-Account defined.
			<ul>
				<li class="delete icon"><?php echo $html->link('Remove Account', $this->here . '/delete/' . $noserub->fnSecurityToken() . '/'); ?></li>
			</ul>
		<?php else: ?>
        	<form method="POST" action="<?php echo $this->here; ?>">
			<?php echo $form->submit('Start'); ?>
			<?php echo $form->end(); ?>
		<?php endif; ?>
	</div>
	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetSettingsNavigation(); ?>
	</div>
</div>
