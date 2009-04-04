<div id="inhalt">
	<div id="left">
		<?php if ($isTwitterFeatureEnabled): ?>
        	<form method="POST" action="<?php echo $this->here; ?>">
			<?php echo $form->submit('Start'); ?>
			<?php echo $form->end(); ?>
		<?php else: ?>
			Twitter feature is not enabled in your network.
		<?php endif; ?>
	</div>
</div>
<div id="rechts">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>
