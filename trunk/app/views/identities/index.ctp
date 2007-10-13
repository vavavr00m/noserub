<?php if(!$data) { ?>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
    <?php 
        $noserub_url = 'http://' . $data['Identity']['username'];
        
        $openid->xrdsLocation($noserub_url . '/xrds', false);
        $openid->serverLink('/auth', false);
        
        echo $this->renderElement('foaf');
    
        $sex = array('img' => array(0 => '/images/profile/avatar/noinfo.gif',
                                    1 => '/images/profile/avatar/female.gif',
                                    2 => '/images/profile/avatar/male.gif'),
                     'img-small' => array(0 => '/images/profile/avatar/noinfo-small.gif',
                                          1 => '/images/profile/avatar/female-small.gif',
                                          2 => '/images/profile/avatar/male-small.gif'));
    
        if($data['Identity']['photo']) {
            $profile_photo = FULL_BASE_URL . Router::url('/static/avatars/'.$data['Identity']['photo'].'.jpg');
        } else {
            $profile_photo = $sex['img'][$data['Identity']['sex']];
        }
    ?>

    <!-- profile -->

    <div id="profile">

        <div id="photo">
        	<img src="<?php echo $profile_photo; ?>" width="130" height="130" alt="<?php echo $data['Identity']['local_username']; ?>'s Picture" />
        </div>

        <div id="whois">
        	<h3><?php echo $data['Identity']['name']; ?></h3>
        	<p id="personalid"><?php echo $data['Identity']['servername']; ?>/<strong><?php echo $data['Identity']['local_username']; ?></strong></p>
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
            <p>
                <?php if($data['Identity']['about']) {
                    $pattern = '#(^|[^\"=]{1})(http://|https://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
                    echo preg_replace($pattern,"\\1<a href=\"\\2\\3\"><u>\\2\\3</u></a>\\4", nl2br($data['Identity']['about']));
                } else { ?>
                    <em>The User yet did not make any statement.</em>
                <?php } ?>
            </p>        
        </div>

        <br class="clear" />
        <div>
            <h4>Social activity</h4>
            <?php echo $this->renderElement('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->renderElement('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>

    <!-- // profile -->
    </div>

    <div id="sidebar">
	
	    <?php if($num_noserub_contacts > 9) { ?>
    	    <span class="more"><a href="<?php echo $noserub_url . '/contacts/'; ?>">see all</a></span>
    	<?php } ?>
    	<h4>Friends</h4>
    	<p class="friendthumbs">
    	    <?php foreach($contacts as $item) { ?>
    	        <a href="http://<?php echo $item['WithIdentity']['username']; ?>">
    	            <?php if($item['WithIdentity']['photo']) {
                        $contact_photo = FULL_BASE_URL . Router::url('/static/avatars/' . $item['WithIdentity']['photo'].'-small.jpg');
                    } else {
                        $contact_photo = $sex['img-small'][$item['WithIdentity']['sex']];
                    } ?>
    	            <img src="<?php echo $contact_photo; ?>" width="35" height="35" alt="<?php echo $item['WithIdentity']['local_username']; ?>'s Picture" />
    	        </a>
    	    <?php } ?>
    	</p>
    	<p>
    		<a href="<?php echo $noserub_url . '/contacts/'; ?>"><strong> <?php echo $num_noserub_contacts; ?></strong> NoseRub contacts</a><br />
    		<strong><?php echo $num_private_contacts; ?></strong> private contacts
        </p>
    
        <hr />

	<h4>On the web</h4>
	<ul class="whoissidebar">
	    <?php foreach($accounts as $item) { ?>
	        <li>
	            <img src="/images/icons/services/<?php echo $item['Service']['icon']; ?>" height="16" width="16" alt="<?php echo $item['Service']['name']; ?>" class="whoisicon" />
	            <a rel="me" href="<?php echo $item['account_url']; ?>"><?php echo $item['Service']['name']; ?></a>
	        </li>
	    <?php } ?>
	</ul>
	
	<hr />

	<h4>Contact</h4>
	<ul class="whoissidebar">
		<li><img src="/images/icons/services/email.gif" height="16" width="16" alt="e-Mail" class="whoisicon" /> <a href="#">e-Mail</a></li>
		<?php foreach($communications as $item) { ?>
	        <li>
	            <img src="/images/icons/services/<?php echo $item['Service']['icon']; ?>" height="16" width="16" alt="<?php echo $item['Service']['name']; ?>" class="whoisicon" />
	            <a href="<?php echo $item['account_url']; ?>"><?php echo $item['Service']['name']; ?></a>
	        </li>
	    <?php } ?>
	</ul>
	
    	<hr />

    </div>

<?php } ?>