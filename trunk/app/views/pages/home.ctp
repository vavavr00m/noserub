<h1>NoseRub</h1>
<?php if($session->check('Identity.id')) { ?>
    <ul>
        <li>
            Go to your homepage: <a href="/noserub/<?php echo $session->read('Identity.username'); ?>/"><?php echo FULL_BASE_URL . '/noserub/' . $session->read('Identity.username'); ?>/</a>
        </li>
        <li>
            <a href="/logout/">Logout</a>
        </li>
    </ul>
<?php } ?>
<p>
    Right now, you should either <a href="/register/">register</a> a new account or <a href="/login/">login</a> with your already existing username and password.
</p>
<p>
    You could also visit the <a href="http://wiki.noserub.com/">Wiki</a> or (if you are even more interested) take a look at the projects homepage at <a href="http://code.google.com/p/noserub/">Google Code</a>. You can grab the sources there and start a NoseRub server on your own.
</p>