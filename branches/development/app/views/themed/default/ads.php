/**
 * Definition file for the ad spots of this theme.
 *
 * make sure not to start this file with <?php !!!!
 * The pattern is like this:
 * $theme_ad_spots = array(
 *     <index> => array(
 *         'name' => '<ad spot name>',
 *         'size' => '<width>x<height>',
 *         'info' => '<some text to describe the ad spot>'
 *     )
 * );
 *
 * The ad spot names are then used in the network admin interafce
 * to place ads on them.
 */
 
$theme_ad_spots = array(
    1 => array(
        'name' => 'sidebar',
        'size' => '200x200',
        'info' => 'Used in the right sidebar',
        'default' => '<a href="http://noserub.com/" target="_blank"><img src="' . Router::url('/images/default-ad_200x200.gif') . '" /></a>'
    )
);