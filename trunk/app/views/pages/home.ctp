<h1>NoseRub</h1>
<?php if($session->check('Identity.id')) { ?>
    <ul>
        <li>
            Go to your homepage: <?php echo $html->link(FULL_BASE_URL . '/noserub/' . $session->read('Identity.username'))?>
        </li>
        <li>
            <?php echo $html->link('Logout', '/logout/'); ?>
        </li>
    </ul>
<?php } ?>
<p>
    Right now, you should either <?php echo $html->link('register', '/register/'); ?> a new account or <?php echo $html->link('login', '/login/'); ?> with your already existing username and password.
</p>
<p>
    You could also visit the <?php echo $html->link('Wiki', 'http://wiki.noserub.com/'); ?> or (if you are even more interested) take a look at the projects homepage at <?php echo $html->link('Google Code', 'href="http://code.google.com/p/noserub/'); ?>. You can grab the sources there and start a NoseRub server on your own.
</p>