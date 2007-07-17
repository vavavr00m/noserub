<div id="metanav" class="nav">
    <ul>
        <?php if($session->check('Identity.id')) { ?>
            <li class="first">logged in as <?php echo $session->read('Identity.username'); ?></li>
            <li><a href="/logout/">Logout</a></li>
            <li><a href="/settings/">Settings</a></li>
        <?php } else { ?>
            <li><a href="/login/">Login</a></li>
        <?php } ?>    
        <li class="last"><a href="/about/">About NoseRub</a></li>
    </ul>
</div>