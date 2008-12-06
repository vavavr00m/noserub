<ul>
	<?php foreach ($menuItems as $menuItem): ?>
        <?php if($menuItem->isSettings()) {
            # hack to be able to show the settings' sub menu
            # without having the settings menu item displayed
            # in the main navigation
            continue;
        } ?>	
		<?php if ($menuItem->isActive()): ?>
			<li class="active">
		<?php else: ?>
			<li>
		<?php endif; ?>
		<?php echo $html->link($menuItem->getLabel(), $menuItem->getLink()); ?>
		</li>
	<?php endforeach; ?>
</ul>