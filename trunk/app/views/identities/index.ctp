<?php if(!$data) { ?>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
    <?php 
        $noserub_url = 'http://' . $data['Identity']['username'];
    ?>
	<?php $openid->xrdsLocation($noserub_url . '/xrds', false); ?>
	<?php $openid->serverLink('/auth', false); ?>
    
    <?php echo $this->renderElement('foaf'); ?>

<!-- profile -->

<div id="profile">

<div id="photo">
	<img src="/images/profile/avatar/male.gif" width="130" height="130" alt="<?php echo $data['Identity']['local_username']; ?>'s Picture" />
</div>

<div id="whois">
	<h3>Full Name</h3>
	<p id="personalid">currentDomainTag.tld/<strong><?php echo $data['Identity']['local_username']; ?></strong></p>

	<ul class="whoisstats">
		<li class="bio icon"><?php echo $data['Identity']['local_username']; ?> is a XXXX and XXXX years old.</li>
		
		<?php if(isset($distance)) { ?>
		<li class="destination icon">XXXX lives <?php echo ceil($distance); ?> km away from you.</li>
		<?php } ?>
		
		<?php if($menu['logged_in'] && isset($relationship_status) && $relationship_status != 'self') { ?>
            <?php
                if($relationship_status == 'contact') {
                    echo '<li class="removecontact icon">' . $data['Identity']['local_username'] . ' is a contact of yours. <a href="#">Remove XXXX</a></li>';
                } else { 
                    echo '<li class="addcontact icon">' . $html->link('Add ' . $data['Identity']['local_username'] . ' as your contact.', '/' . $data['Identity']['local_username'] . '/add/as/contact/').'</li>';
                }
            ?>
    <?php } ?>
    		
	</ul>
</div>

<br class="clear" />

<h4>About me</h4>
<div id="about">
<?php switch($data['Identity']['sex']) {
                case  1: echo 'her'; break;
                case  2: echo 'his'; break;
                default: echo 'her/his'; break;
            } ?>
Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.
</div>

    <br class="clear" />
    <div>
        <h4>Social activity</h4>
        <?php echo $this->renderElement('subnav'); ?>
        <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
    </div>
<?php } ?>

<!-- // profile -->
</div>

<div id="sidebar">
	
	<span class="more"><a href="<?php echo $noserub_url . '/contacts/'; ?>">see all</a></span>
	<h4>Friends</h4>
	<p>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
		<a href="#"><img src="/images/profile/avatar/male-small.gif" width="35" height="35" alt="XXXX's Picture" /></a>
	</p>
	<p>
		<a href="<?php echo $noserub_url . '/contacts/'; ?>"><strong> <?php echo $num_noserub_contacts; ?></strong> NoseRub contacts</a><br />
		<strong><?php echo $num_private_contacts; ?></strong> private contacts
    </p>
    
    <hr />

	<h4>On the web</h4>
	<ul class="whoissidebar">
		<li><img src="/images/icons/services/flickr.gif" height="16" width="16" alt="flickr" class="serviceicon" /> <a href="#">DEMO</a></li>
		<li><img src="/images/icons/services/twitter.gif" height="16" width="16" alt="twitter" class="serviceicon" /> <a href="#">DEMO</a></li>
	</ul>
	
	<hr />

	<h4>Contact</h4>
	<ul class="whoissidebar">
		<li class="contact_email icon"><a href="#">e-Mail</a></li>
		<li class="contact_jabber icon"><a href="xmpp:USERNAMEHERE">Jabber</a></li>
		<li class="contact_icq icon"><a href="http://www.icq.com/USERNAMEHERE">ICQ</a></li>
		<li class="contact_yim icon"><a href="http://edit.yahoo.com/config/send_webmesg?.target=USERNAMEHERE&amp;.src=pg">YIM!</a></li>
		<li class="contact_aim icon"><a href="aim:USERNAMEHERE">AIM</a></li>
		<li class="contact_skype icon"><a href="skype:USERNAMEHERE">Skype</a></li>
		<li class="contact_msn icon"><a href="msnim:USERNAMEHERE">MSN</a></li>
	</ul>
	
	<hr />

</div>