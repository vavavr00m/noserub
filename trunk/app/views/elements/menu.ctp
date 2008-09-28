<ul>
	<?php foreach ($menuItems as $menuItem): ?>
		<?php if ($menuItem->isActive()): ?>
			<li class="active">
		<?php else: ?>
			<li>
		<?php endif; ?>
		<?php echo $html->link($menuItem->getLabel(), $menuItem->getLink()); ?>
		</li>
	<?php endforeach; ?>
</ul>