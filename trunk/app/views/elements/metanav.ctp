<div id="metanav" class="nav wrapper">
    <ul>
        <?php if($session->check('Identity.id')) { ?>
            <li class="first">You are logged in as <strong><?php echo $session->read('Identity.username'); ?></strong></li>
        <?php } else { ?>
            <li><?php echo $html->link('Login', '/pages/login/');?></li>
        <?php } ?>
        <li class="last"><?php echo $html->link('Admin', '/pages/admin/');?></li>
        <li class="last"><?php echo $html->link('Contact', '/pages/contact/');?></li>
        <li><?php echo $html->link('Logout', '/pages/logout/');?></li>
    </ul>
</div>