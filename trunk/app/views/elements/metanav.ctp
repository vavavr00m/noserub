<div id="metanav" class="nav">
    <ul>
        <?php if($session->check('Identity.id')) { ?>
            <li class="first">logged in as <?php echo $session->read('Identity.username'); ?></li>
            <li><a href="/logout/">Logout</a></li>
        <?php } else { ?>
            <li><a href="/login/">Login</a></li>
        <?php } ?>
        <li><a href="http://wiki.noserub.com/">Wiki</a></li>
        <li class="last"><a href="/admin">Admin</a></li>
        <li class="last"><a href="/contact/">Contact</a></li>
    </ul>
</div>