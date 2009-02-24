<?php

App::import('Model', 'Admin');
$Admin = new Admin();

$Admin->id = 1;
$Admin->saveField('password', md5(Configure::read('NoseRub.admin_hash')));

# this sets the default password for the admin user for network 1 to the 
# value of the hash for the admin routes. this seems good enough, as the
# migrations are only done, when the /system/update/ admin route is
# called with a valid admin_hash.