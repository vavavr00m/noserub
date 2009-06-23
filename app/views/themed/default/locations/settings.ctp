<div id="bd-main-hd">
    <h2><?php __('Location Settings'); ?></h2>
    <p>
        <?php __('Once you added some locations, like <em>Home</em> and <em>Office</em>, you 
        will be able to set this location on your profile page to tell, where you 
        currently are.'); ?>
    </p>
</div>
<div id="bd-main-bd">
    <?php echo $noserub->widgetFlashMessage(); ?>
    <?php echo $noserub->formLocations(); ?>
</div>
<div id="bd-main-sidebar">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>