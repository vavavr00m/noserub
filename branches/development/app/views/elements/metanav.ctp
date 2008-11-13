<?php
    $action = $this->name . '.' . $this->action;
?>
<div id="metanav" class="nav wrapper">
    <ul>
        <?php if(isset($menu) && $menu['logged_in']) { ?>
            <li class="first">
                <?php echo sprintf(__('You are logged in as <strong>%s</strong>', true), $session->read('Identity.local_username')); ?>
            </li>
            <li>
                <?php if(strpos($action, '_settings') > 0 ||
                         $action == 'Accounts.index' ||
                         $action == 'Locations.index' ||
                         $action == 'Syndications.index' ||
                         $action == 'OpenidSites.index' ||
                         $action == 'OauthConsumers.index' ||
                         $action == 'AccountSettings.index') {
                    __('Settings');
                } else { ?>
                    <?php echo $html->link(__('Settings', true), '/' . $session->read('Identity.local_username') . '/settings/'); ?>
                <?php } ?>
            </li>
            <li>
                <?php if($this->name == 'Searches') {
                    __('Search');
                } else { ?>
                    <?php echo $html->link(__('Search', true), '/search/'); ?>
                <?php } ?>
            </li>
            <li><?php echo $html->link(__('Logout', true), '/pages/logout/' . $security_token . '/');?></li>
        <?php } else { ?>
            <li>
                <?php if($this->name == 'Searches') {
                    __('Search');
                } else { ?>
                    <?php echo $html->link(__('Search', true), '/search/'); ?>
                <?php } ?>
            </li>
            <li><?php echo $html->link(__('Login', true), '/pages/login/');?></li>
        <?php } ?>
    </ul>
</div>