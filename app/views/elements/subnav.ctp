<?php if ($subMenu): ?>
	<div id="subnavigation" class="subnav<?php echo isset($no_wrapper) ? '' : ' wrapper'; ?>">
		<?php echo $this->element('menu', array('menuItems' => $subMenu->getMenuItems())); ?>	
	</div>
<?php endif; ?>