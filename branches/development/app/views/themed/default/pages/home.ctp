<div id="bd-main-hd">
    <h2>Startseite des Netzwerkes</h2>
</div>
<div id="bd-main-bd">
    <?php echo $noserub->widgetFlashMessage(); ?>
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetNewUsers(); ?>
    <?php echo $noserub->widgetPopularUsers(); ?>
    <?php echo $noserub->widgetLastActiveUsers(); ?>
    <?php echo $noserub->widgetLastLoggedInUsers(); ?>
</div>
