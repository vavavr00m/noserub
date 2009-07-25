<?php

class ServiceType {
    const NOSERUB = 0;
    const PHOTO = 1;
    const LINK = 2;
    const TEXT = 3;
    const EVENT = 4;
    const MICROPUBLISH = 5;
    const VIDEO = 6;
    const AUDIO = 7;
    const DOCUMENT = 8;
    const LOCATION = 9;
}

$service_types = array(
    ServiceType::NOSERUB => array(
        'token' => 'noserub',
        'name' => __('NoseRub', true),
        'intro' => '@user@ @item@'
    ),
    ServiceType::PHOTO => array(
        'token' => 'photo',
        'name' => __('Photos', true),
        'intro' => __('@user@ took a photo called @item@', true) 
    ),
    ServiceType::LINK => array(
        'token' => 'link',
        'name' => __('Links / Bookmarks', true),
        'intro' => __('@user@ bookmarked @item@', true)
    ),
    ServiceType::TEXT => array(
        'token' => 'text',
        'name' => __('Text / Blog', true),
        'intro' => __('@user@ wrote a text about @item@', true)
    ),
    ServiceType::EVENT => array(
        'token' => 'event',
        'name' => __('Event', true),
        'intro' => __('@user@ plans to attend @item@', true)
    ),
    ServiceType::MICROPUBLISH => array(
        'token' => 'micropublish',
        'name' => __('Micropublishing', true),
        'intro' => __('@user@ says @item@', true)
    ),
    ServiceType::VIDEO => array(
        'token' => 'video',
        'name' => __('Videos', true),
        'intro' => __('@user@ made a video called @item@', true)
    ),
    ServiceType::AUDIO => array(
        'token' => 'audio',
        'name' => __('Audio', true),
        'intro' => __('@user@ listens to @item@', true)
    ),
    ServiceType::DOCUMENT => array(
        'token' => 'document',
        'name' => __('Documents', true),
        'intro' => __('@user@ uploaded a document called @item@', true)
    ),
    ServiceType::LOCATION => array(
        'token' => 'location',
        'name' => __('Locations', true),
        'intro' => __('@user@ is currently at @item@', true)
    )
);

$service_types_list = array();
foreach($service_types as $id => $service_type) {
    $service_types_list[$id] = $service_type['name'];
}

Configure::write('service_types', $service_types);
Configure::write('service_types_list', $service_types_list);
    
