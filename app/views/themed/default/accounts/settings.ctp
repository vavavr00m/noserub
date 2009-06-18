<div id="bd-main-hd">
    <h2><?php __('Account Settings'); ?></h2>
    <p>
        <?php __('Here you can add all your own social/online activities and import friends in your network.'); ?>
    </p>
</div>
<div id="bd-main-bd">
    <?php echo $noserub->accountSettingsWeb(); ?>
    
    <?php echo $noserub->accountSettingsCommunication(); ?>
    
    <?php echo $noserub->accountSettingsTwitter(); ?> 

<!---
    <div class="left">
        <?php //echo $this->element('accounts/index'); ?>
    </div>

--->
</div>

<div id="bd-main-sidebar">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>