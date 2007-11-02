<?php
if(isset($data['Identity'])) {
    $data = $data['Identity'];
}
$noserub_url = 'http://' . $data['username'];
$sex = array('img' => array(0 => Router::url('/images/profile/avatar/noinfo.gif'),
                            1 => Router::url('/images/profile/avatar/female.gif'),
                            2 => Router::url('/images/profile/avatar/male.gif')),
             'img-small' => array(0 => Router::url('/images/profile/avatar/noinfo-small.gif'),
                                  1 => Router::url('/images/profile/avatar/female-small.gif'),
                                  2 => Router::url('/images/profile/avatar/male-small.gif')),
             'he' => array(0 => 'he/she',
                           1 => 'she',
                           2 => 'he'),
             'him' => array(0 => 'him/her',
                            1 => 'her',
                            2 => 'him'));

if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
    $static_base_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/avatars/';
} else {
    $static_base_url = FULL_BASE_URL . Router::url('/static/avatars/');
}

if($data['photo']) {
    if(strpos($data['photo'], 'http://') === 0 ||
       strpos($data['photo'], 'https://') === 0) {
           # contains a complete path, eg. from not local identities
           $profile_photo = $data['photo'];
       } else {
           $profile_photo = $static_base_url . $data['photo'] . '.jpg';
       }
} else {
    $profile_photo = $sex['img-small'][$data['sex']];
}
?>
<!-- mini profile // start -->
<div id="hcard-<?php echo $data['local_username']; ?>" class="vcard">
<div id="photo">
	<a href="<?php echo $noserub_url; ?>"><img src="<?php echo $profile_photo; ?>" width="35" height="35" alt="<?php echo $data['local_username']; ?>'s Picture" /></a>
</div>

<div id="whois">
	<h3><a href="<?php echo $noserub_url; ?>" class="fn url"><?php echo $data['name']; ?></a></h3>
	<p id="personalid">
		<a href="<?php echo $noserub_url; ?>"><?php echo $data['servername']; ?>/<strong class="nickname"><?php echo $data['local_username']; ?></strong></a>
	</p>
</div>
</div>
<!-- mini profile // end -->