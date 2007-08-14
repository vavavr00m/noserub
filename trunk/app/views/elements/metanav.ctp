<div id="metanav" class="nav">
    <ul>
        <?php if($session->check('Identity.id')) { ?>
            <li class="first">logged in as <?php echo $session->read('Identity.username'); ?></li>
            <li><?php echo $html->link('Logout', '/logout/');?></li>
        <?php } else { ?>
            <li><?php echo $html->link('Login', '/login/');?></li>
        <?php } ?>
        <li><?php echo $html->link('Wiki', 'http://wiki.noserub.com/');?></li>
        <li class="last"><?php echo $html->link('Admin', '/admin/');?></li>
        <li class="last"><?php echo $html->link('Contact', '/contact/');?></li>
    </ul>
</div>