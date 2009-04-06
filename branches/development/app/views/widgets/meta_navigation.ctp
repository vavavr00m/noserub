<div class="navi">
<ul>
    <?php if(Context::read('logged_in_identity')) { ?>
        <li>
            <?php echo $html->link(__('Settings', true), '/' . Context::read('logged_in_identity.local_username') . '/settings/'); ?>
        </li>
        <li>
            <?php echo $html->link(__('Logout', true), '/pages/logout/' . $noserub->fnSecurityToken()); ?>
        </li>
    <?php } ?>
    <li>
        <?php echo $this->element('languages'); ?>
    </li>
</ul>
</div>