<div id="bd-main-hd">
    <?php echo $noserub->widgetProfile(); ?>
</div>
 <div id="bd-main-bd">
    <?php echo $noserub->widgetFlashMessage(); ?>
    <?php echo $noserub->widgetContacts(array('layout' => 'list')); ?>
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetContactFilter(); ?>
</div>