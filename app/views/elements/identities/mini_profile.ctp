<?php
if(isset($data['Identity'])) {
    $data = $data['Identity'];
}
$noserub_url = 'http://' . $data['username'];

if($data['photo']) {
    if(strpos($data['photo'], 'http://') === 0 ||
       strpos($data['photo'], 'https://') === 0) {
           # contains a complete path, eg. from not local identities
           $profile_photo = $data['photo'];
       } else {
           $profile_photo = $base_url_for_avatars . $data['photo'] . '.jpg';
       }
} else {
	App::import('Vendor', 'sex');
    $profile_photo = Sex::getSmallImageUrl($data['sex']);
}
?>
<!-- mini profile // start -->
<div id="hcard-<?php echo $data['local_username']; ?>" class="vcard mini">
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
