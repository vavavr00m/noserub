<?php if(Context::isLoggedIn()) { ?>
    <h4><?php 
        echo $html->image($noserub->fnLoggedInIdentityPhotoUrl('small'), array('class' => 'userimage', 'alt' => Context::read('logged_in_identity.name')));
        echo sprintf(__('Hi %s!', true), Context::read('logged_in_identity.name'));
    ?></h4>
<?php } ?>