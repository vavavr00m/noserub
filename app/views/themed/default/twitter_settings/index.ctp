<div id="inhalt">
	<div id="left">
		<?php if ($isTwitterFeatureEnabled): ?>
			<?php if ($hasTwitterAccount): ?>
				Twitter-Account defined.
			<?php else: ?>
	        	<form method="POST" action="<?php echo $this->here; ?>">
				<?php echo $form->submit('Start'); ?>
				<?php echo $form->end(); ?>
			<?php endif; ?>
		<?php else: ?>
			Twitter feature is not enabled in your network.
		<?php endif; ?>
	</div>
</div>
<div id="rechts">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>
