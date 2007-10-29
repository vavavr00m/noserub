<?php
$noserub_url = 'http://' . $data['Identity']['username'];
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

if($data['Identity']['photo']) {
    if(strpos($data['Identity']['photo'], 'http://') === 0 ||
       strpos($data['Identity']['photo'], 'https://') === 0) {
           # contains a complete path, eg. from not local identities
           $profile_photo = $data['Identity']['photo'];
       } else {
           $profile_photo = $static_base_url . $data['Identity']['photo'] . '.jpg';
       }
} else {
    $profile_photo = $sex['img'][$data['Identity']['sex']];
}
?>
<div id="hcard-<?php echo $data['Identity']['local_username']; ?>" class="vcard">
<div id="photo">
	<img src="<?php echo $profile_photo; ?>" width="130" height="130" alt="<?php echo $data['Identity']['local_username']; ?>'s Picture" />
</div>

<div id="whois">
	<h3><a href="<?php echo $noserub_url; ?>" class="fn url"><?php echo $data['Identity']['name']; ?></a></h3>
	<p id="personalid">
		<?php echo $data['Identity']['servername']; ?>/<strong class="nickname"><?php echo $data['Identity']['local_username']; ?></strong>
	</p>
	<ul class="whoisstats">
	    <?php if(isset($data['Identity']['age'])) { ?>
		    <li class="bio icon">
		        <?php echo $sex['he'][$data['Identity']['sex']]; ?> is <?php echo $data['Identity']['age']; ?> years old.
		    </li>
        <?php } ?>
		<?php if(isset($distance) || $data['Identity']['address_shown']) {
		    $label = $sex['he'][$data['Identity']['sex']] . ' lives ';
		    if(isset($distance)) {
		        $label .= ceil($distance) . ' km away from you';
		    }
		    if($data['Identity']['address_shown']) {
		        $label .= ' in ' . $data['Identity']['address_shown'];
		    } ?>
		    <li class="destination icon"> <?php echo $label; ?></li>
		<?php } ?>
	</ul>
</div>
</div>
<br class="clear" />

<?php $flashmessage->render(); ?>

<form id="Identity/messages/new/Form" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php echo $form->input('Message.subject', array('label' => 'Subject:', 'error' => 'You need to give the message a subject.')); ?>
        Message:<br />
        <?php echo $form->textarea('Message.text', array('columns' => 80, 'rows' => 40, 'error' => 'The message is empty.')); ?>
        <br />
        <input class="submitbutton" type="submit" value="Send"/>
    </fieldset>
</form>