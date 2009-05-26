<ul>
    <?php if(Context::read('logged_in_identity')) { ?>
        <li>
            <?php echo $html->link(__('Settings', true), '/settings/'); ?>
        </li>
        <li>
            <?php echo $html->link(__('Logout', true), '/pages/logout/' . $noserub->fnSecurityToken()); ?>
        </li>
    <?php } ?>
    <li class="lang">
        <?php echo $this->element('languages'); ?>
    </li>
</ul>