<div id="bd-main-hd">
    <h2><?php __('Groups'); ?></h2>
</div>
<div id="bd-main-bd"
    <?php echo $noserub->widgetGroupsOverview(); ?>
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetPopularGroups(); ?>
    <?php echo $noserub->widgetNewGroups(); ?>
    <?php echo $noserub->widgetGroups(); ?>
</div>