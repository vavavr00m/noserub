<?php

$sql = 'UPDATE admins SET password="' . md5(Configure::read('NoseRub.admin_hash')) . '" WHERE id=1';
$this->query($sql);

# this sets the default password for the admin user for network 1 to the 
# value of the hash for the admin routes. this seems good enough, as the
# migrations are only done, when the /system/update/ admin route is
# called with a valid admin_hash.