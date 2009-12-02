<div class="widget widget-admin-navigation">
    <?php if(Context::read('admin_id')) { ?>
        You're logged in as admin_id <?php echo Context::read('admin_id'); ?>.
        <ul>
            <li><?php echo $html->link(__('Network Settings', true), '/admins/network_settings/'); ?></li>
            <li><?php echo $html->link(__('Ad Management', true), '/admins/ads/'); ?></li>
            <li><?php echo $html->link(__('Change password', true), '/admins/password/'); ?></li>
            <li><?php echo $html->link(__('Admin logout', true), '/admins/logout/'); ?></li>
        </ul>
    <?php } ?>
</div>