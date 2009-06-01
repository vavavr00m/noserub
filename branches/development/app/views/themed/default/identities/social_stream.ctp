<div id="bd-main-hd">
    <h1><?php __("Social Stream of your contact's activities")?></h1>
</div>
<div id="bd-main-bd">
    <?php echo $noserub->widgetNetworkLifestream(); ?>
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetContacts(); ?>
    <?php echo $noserub->widgetContactFilter(); ?>
    <?php echo $noserub->widgetNewUsers(); ?>
</div>