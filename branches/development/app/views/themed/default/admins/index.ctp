<div id="bd-main-hd">
    <h2><?php __('Admin'); ?></h2>
</div>
<div id="bd-main-bd"
    <?php echo $noserub->formAdminSettings(); ?>
</div>

<div id="rechts">
    <?php echo $noserub->widgetAdminLogin(); ?>
    <?php echo $noserub->widgetAdminNavigation(); ?>
</div>