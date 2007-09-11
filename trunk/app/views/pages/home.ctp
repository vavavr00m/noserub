<?php if($session->check('Identity.id')) { ?>
    <ul>
        <li>
            Go to your homepage: <?php echo $html->link('http://' . $session->read('Identity.username'))?>
        </li>
        <li>
            <?php echo $html->link('Logout', '/pages/logout/'); ?>
        </li>
    </ul>
<?php } ?>
<p>
    Right now, you should either <?php echo $html->link('register', '/pages/register/'); ?> a new account or <?php echo $html->link('login', '/pages/login/'); ?> with your already existing username and password.
</p>
<p>
    You could also visit the <?php echo $html->link('Wiki', 'http://wiki.noserub.com/'); ?> or (if you are even more interested) take a look at the projects homepage at <?php echo $html->link('Google Code', 'http://code.google.com/p/noserub/'); ?>. You can grab the sources there and start a NoseRub server on your own.
</p>