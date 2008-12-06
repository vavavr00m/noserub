<?php if(isset($data['FavoritedBy']) && count($data['FavoritedBy']) > 0 ) {
    echo '<br />';
    echo sprintf(__('Favorited by %d users: ', true), count($data['FavoritedBy']));
    $users = array();
    foreach($data['FavoritedBy'] as $item) {
        $users[] = '<a href="http://' . $item['Identity']['username'] .'">' . $item['Identity']['local_username'] . '</a>';
    }
    echo join(', ', $users);
} ?>