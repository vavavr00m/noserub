<?php

App::import('Model', 'Network');
$Network = new Network();

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

$Network->id = 1;
$Network->save($data);
$this->log(print_r($data, 1), LOG_DEBUG);