<?php

/**
 * we cannot import a model like "Network" to do a proper 
 * "Network->save()", because it is associated with other
 * models that will be created in a later migration.
 *
 * We therefore need to write the update query by ourselfs.
 */
$name     = Configure::read('NoseRub.app_name') ? Configure::read('NoseRub.app_name') : 'NoseRub';
$feeds    = Configure::read('NoseRub.manual_feeds_update') ? 1 : 0;
$register = Configure::read('NoseRub.registration_type') == 'all' ? 1 : 2;
$ssl      = Configure::read('NoseRub.use_ssl') ? 1 : 0;
$maps     = Configure::read('NoseRub.google_maps_key') ? Configure::read('NoseRub.google_maps_key') : '';
$info     = Configure::read('NoseRub.api_info_active') ? 1 : 0;

$now = date('Y-m-d H:i:s');

$data = array(
    'name' => $name,
    'url'  => Configure::read('NoseRub.full_base_url'),
    'default_language' => Configure::read('NoseRub.default_language'),
    'manual_feeds_update' => $feeds,
    'registration_type' => $register,
    'use_ssl' => $ssl,
    'google_maps_key' => $maps,
    'api_info_active' => $info,
    'modified' => $now,
    'created'  => $now
);

$updates = array();
foreach($data as $field => $value) {
    $updates[] = $field . '="' . $value . '"';
}
$sql = 'UPDATE networks SET ' . join(',', $updates) . ' WHERE id=1';

$this->query($sql);