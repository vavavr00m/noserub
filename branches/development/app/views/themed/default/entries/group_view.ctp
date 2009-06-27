<div id="bd-main">
    <div id="bd-main-bd">
    	<div id="bd-main-hd">
    		<h2><?php echo __('Group') . ': ' . $group['Group']['name']; ?></h2>
    	</div>
    	<div id="bd-main-bd">
        	<?php echo $noserub->widgetFlashMessage(); ?>
    		<?php echo $noserub->widgetViewEntry(); ?>
    		<?php echo $noserub->formAddComment(); ?>
    	</div>
    </div>
</div>