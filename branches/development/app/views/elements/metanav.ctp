<div id="metanav" class="nav wrapper">
    <ul>
        <?php if(isset($menu) && $menu['logged_in']) { ?>
            <li class="first">
                <?php echo sprintf(__('You are logged in as <strong>%s</strong>', true), $session->read('Identity.local_username')); ?>
            </li>
            <li><?php echo $html->link(__('Logout', true), '/pages/logout/' . $security_token . '/');?></li>
        <?php } else { ?>
            <li><?php echo $html->link(__('Login', true), '/pages/login/');?></li>
        <?php } ?>
    </ul>
</div>