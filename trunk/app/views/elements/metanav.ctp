<div id="metanav" class="nav">
    <ul>
        <?php if($session->check('Identity.id')) { ?>
            <li class="first">logged in as <?php echo $session->read('Identity.username'); ?></li>
            <li><a href="<?php echo NOSERUB_URL_PREFIX; ?>/logout/">Logout</a></li>
        <?php } else { ?>
            <li><a href="<?php echo NOSERUB_URL_PREFIX; ?>/login/">Login</a></li>
        <?php } ?>
        <li><a href="http://wiki.noserub.com/">Wiki</a></li>
        <li class="last"><a href="<?php echo NOSERUB_URL_PREFIX; ?>/admin">Admin</a></li>
        <li class="last"><a href="<?php echo NOSERUB_URL_PREFIX; ?>/contact/">Contact</a></li>
    </ul>
</div>