<?php
    $uri = $_SERVER['REQUEST_URI'];
    $noserub_url = '/noserub/' . $session->read('Identity.username');
    if(strpos($uri, '/accounts/') > 0 ||
       strpos($uri, '/contacts/') > 0 ||
       strpos($uri, '/settings/') > 0 ) {
           # no sub navigation
    } else if(strpos($uri, '/network/') > 0) { ?>
        <div id="ctxtnav" class="nav">
            <h2>My Network Navigation</h2>
            <ul>
                <li class="first">All</li>
                <li><a href="<?php echo $noserub_url; ?>/network/images/">Images</a></li>  
                <li><a href="<?php echo $noserub_url; ?>/network/links/">Links</a></li>
                <li><a href="<?php echo $noserub_url; ?>/network/blogs/">Blogs</a></li>     
            </ul>
        </div>
    <?php } else if(strpos($uri, '/noserub/') === 0) { ?>
        <div id="ctxtnav" class="nav">
            <h2>My NoseRub Navigation</h2>
            <ul>
                <li class="first">All</li>
                <li><a href="<?php echo $noserub_url; ?>/images/">Images</a></li>  
                <li><a href="<?php echo $noserub_url; ?>/links/">Links</a></li>
                <li><a href="<?php echo $noserub_url; ?>/blogs/">Blogs</a></li>     
            </ul>
        </div>
    <?php } ?>
    