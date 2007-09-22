<div id="metanav" class="nav wrapper">
    <ul>
        <?php if($menu['logged_in']) { ?>
            <li class="first">You are logged in as <strong><?php echo $session->read('Identity.local_username'); ?></strong></li>
            <li><?php echo $html->link('Logout', '/pages/logout/');?></li>
        <?php } else { ?>
            <li><?php echo $html->link('Login', '/pages/login/');?></li>
        <?php } ?>
    </ul>
</div>