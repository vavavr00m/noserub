<div id="bd-main" class="with-sidebar">
    <div id="bd-main-hd">
		<h2><?php __('Search'); ?></h2>
    </div>

    <div id="bd-main-bd">
        <?php echo $noserub->widgetSearchResults(); ?>
    </div>
    
    <div id="bd-main-sidebar">
        <?php echo $noserub->widgetAd('sidebar'); ?>
    </div>
</div>