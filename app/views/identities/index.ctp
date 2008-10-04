<?php if(!$data) { ?>
    <p>
        Either this account does not exist, or it is only available for the user who created it.
    </p>
<?php } else { ?>
    <?php 
        App::import('Vendor', 'sex');
    	$noserub_url = 'http://' . $data['Identity']['username'];
        
        if(isset($data['Identity']['openid'])) {
        	# We delegate to the OpenID identity instead of the OpenID as the OpenID itself may be 
        	# delegated and because OpenID delegation chaining is not possible our delegation 
        	# wouldn't work.
        	$openid->delegate($data['Identity']['openid_identity'], false);
        	$openid->serverLink($data['Identity']['openid_server_url'], false);
        } else {
        	$openid->xrdsLocation($noserub_url . '/xrds', false);
        	$openid->serverLink('/auth', false);
        }
        if($data['Identity']['generic_feed']) {
            $url = Router::url('/' . $data['Identity']['local_username']);
            if(NOSERUB_USE_CDN) {
                $feed_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/feeds/' . $data['Identity']['local_username'] . '.rss';
            } else {
                $feed_url = NOSERUB_FULL_BASE_URL . $url . '/feeds/rss';
            }
            $this->addScript('<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="' . $feed_url . '" />');
        }
        
        echo $this->element('foaf', array('base_url_for_avatars' => $base_url_for_avatars));
    
        if($data['Identity']['photo']) {
            if(strpos($data['Identity']['photo'], 'http://') === 0 ||
               strpos($data['Identity']['photo'], 'https://') === 0) {
                   # contains a complete path, eg. from not local identities
                   $profile_photo = $data['Identity']['photo'];
               } else {
                   $profile_photo = $base_url_for_avatars . $data['Identity']['photo'] . '.jpg';
               }
        } else {
            $profile_photo = Sex::getImageUrl($data['Identity']['sex']);
        }
    ?>

    <!-- profile -->
	
    <div id="hcard-<?php echo $data['Identity']['local_username']; ?>" class="vcard">

        <?php $flashmessage->render(); ?>
        
        <div id="photo">
        	<img class="photo" src="<?php echo $profile_photo; ?>" width="130" height="130" alt="<?php echo $data['Identity']['local_username']; ?>'s Picture" />
        </div>

        <div id="whois">
        	<h3><a href="<?php echo $noserub_url; ?>" class="fn url"><?php echo $data['Identity']['name']; ?></a></h3>
        	<p id="personalid">
        		<?php echo $data['Identity']['servername']; ?>/<strong class="nickname"><?php echo $data['Identity']['local_username']; ?></strong>
        	</p>
        	<ul class="whoisstats">
        	    <?php if(isset($data['Identity']['age'])) { ?>
        		    <li class="bio icon">
        		        <?php echo Sex::heOrShe($data['Identity']['sex']); ?> is <?php echo $data['Identity']['age']; ?> years old.
        		    </li>
		        <?php } ?>
        		<?php if(isset($distance) || $data['Identity']['address_shown']) {
        		    if($relationship_status == 'self') {
        		        $label = 'you live ';
        		    } else {
        		        $label = Sex::heOrShe($data['Identity']['sex']) . ' lives ';
    		        }
        		    if(isset($distance)) {
        		        $label .= ceil($distance) . ' km away from you';
        		    }
        		    if($data['Identity']['address_shown']) {
        		        $label .= ' in ' . $data['Identity']['address_shown'];
        		    } ?>
        		    <li class="destination icon"> <?php echo $label; ?></li>
        		<?php } ?>
		        
		        <?php 
		            if($relationship_status == 'self') {
		                $label = 'your last Location: ';
		            } else {
		                $label = Sex::hisOrHer($data['Identity']['sex']) . ' last Location: ';
		            }
		            $label .= $data['Location']['name'] == '' ? '<em>Unknown</em>' : $data['Location']['name'];
		        ?>
		        
		        <li class="userlocation icon"> <?php echo $label; ?></li>
		        
        		<?php if($menu['logged_in'] && isset($relationship_status) && $relationship_status != 'self') { ?>
                    <?php
                        if($relationship_status == 'contact') {
                            echo '<li class="removecontact icon">' . Sex::heOrShe($data['Identity']['sex']) . ' is a contact of yours</li>';
                        } else { 
                            echo '<li class="addcontact icon">' . $html->link('Add ' . Sex::himOrHer($data['Identity']['sex']) . ' as your contact', '/' . $data['Identity']['local_username'] . '/add/as/contact/'.$security_token.'/').'</li>';
                        }
                    ?>
                <?php } ?>
                <?php if (!$menu['logged_in']) { ?>
                	<li class="addcontact icon"><?php echo $html->link('Subscribe', '/'.$data['Identity']['local_username'].'/subscribe'); ?></li>
                <?php } ?>
        	</ul>
        </div>
        
        <hr class="clear" />
        
        <?php if($relationship_status == 'self') { ?>
            <?php echo $this->element('identities/what_are_you_doing'); ?>
            <hr class="clear" />
        <?php } ?>
        
        <?php if($data['Identity']['about']) { ?>
            <h4>About <?php echo Sex::himOrHer($data['Identity']['sex']); ?></h4>
            <div id="about">
                <p class="summary">
                    <?php if($data['Identity']['about']) {
                        $pattern = '#(^|[^\"=]{1})(http://|https://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
                        echo preg_replace($pattern,"\\1<a href=\"\\2\\3\"><u>\\2\\3</u></a>\\4", nl2br($data['Identity']['about']));
                    } ?>
                </p>        
            </div>
        <?php } ?>
        
        <?php if($relationship_status == 'contact') { ?>
        
            <hr />
        	
            <div class="textBox">
            	<span class="more"><a href="http://<?php echo $session_identity['username'] . '/contacts/' . $contact['Contact']['id'] , '/edit/'; ?>">edit</a></span>
            	<h4>Relationship</h4>
                <div id="relationshipBox">
                    <p class="summary">
                    	<?php
                    	    foreach($contact['NoserubContactType'] as $contact_type) {
                    	        echo $contact_type['name'] . ' ';
                    	    }
                    	    foreach($contact['ContactType'] as $contact_type) {
                    	        echo $contact_type['name'] . ' ';
                    	    }
                    	?>
                    </p>        
                </div>
            </div>
        
            <div class="textBox lastBox">
                <span class="more"><a href="http://<?php echo $session_identity['username'] . '/contacts/' . $contact['Contact']['id'] , '/edit/'; ?>">edit</a></span>
                <h4>Notes</h4>
                <div id="noteBox">
                    <p class="summary">
                        <?php echo $contact['Contact']['note'] ? $contact['Contact']['note'] : '<em>Add some notes here.</em>'; ?>
                    </p>        
                </div>
    		</div>
            
        <?php } ?>
        
        <hr class="clear" />

        <div>
            <h4>Social activity</h4>
            <?php echo $this->element('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->element('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>

    <!-- // profile -->
    </div>

    <div id="sidebar">
        
         <?php if($relationship_status == 'self') { ?>
    	    <span class="more"><a href="<?php echo $noserub_url . '/settings/locations/'; ?>">manage</a></span>
    	
    	<h4>Location</h4>
    	
            <form class="locator" method="POST" action="<?php echo $this->here; ?>">
                <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
                <div class="input">
                <label>I'm currently at</label>
                <select name="data[Locator][id]" size="1">
                    <?php $selected_location = $data['Identity']['last_location_id']; ?>
                    <?php foreach($locations as $id => $name) { ?>
                        <option <?php if($id == $selected_location) { echo 'selected="selected" '; } ?>value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                    <option value="0">[somewhere else]</option>
                </select>
                <label id="locator_name" for="data[Locator][name]">Where are you then?</label>
                <input type="text" name="data[Locator][name]" value="">
                <input class="submitbutton" type="submit" value="Update"/>
                </div>
            </form>
    
        <hr />
        <?php } ?>
	
	    <?php if($relationship_status == 'self') { ?>
    	    <span class="more"><a href="<?php echo $noserub_url . '/contacts/'; ?>">manage</a></span>
    	<?php } ?>
    	<?php echo $this->element('contacts/box', array('box_head' => 'Contacts', 'data' => $contacts, 'static_base_url' => $base_url_for_avatars)); ?>
    	<p class="morefriends">
    		<strong><?php echo $num_noserub_contacts; ?></strong> NoseRub contacts<br />
    		<strong><?php echo $num_private_contacts; ?></strong> private contacts
    		<?php if(($relationship_status == 'self' && ($num_noserub_contacts+$num_private_contacts > 0)) || 
    		         ($num_noserub_contacts > 0)) { ?>
    		    <a href="<?php echo $noserub_url . '/network/'; ?>">Contact's Social Stream</a>
    		<?php } ?>
        </p>
    
        <hr />
	
	    <?php if(isset($mutual_contacts)) {
	        echo $this->element('contacts/box', array('box_head' => 'Mutual Contacts', 'data' => $mutual_contacts, 'static_base_url' => $base_url_for_avatars));
	        echo '<hr />';
	    } ?>
	    
	    <?php if($relationship_status == 'self') { ?>
    	    <span class="more"><a href="<?php echo $noserub_url . '/settings/accounts/'; ?>">manage</a></span>
    	<?php } ?>
	    <h4>On the web</h4>
	    <ul class="whoissidebar">
	        <?php foreach($accounts as $item) { ?>
	            <li>
	                <img src="<?php echo Router::url('/images/icons/services/') . $item['Service']['icon']; ?>" height="16" width="16" alt="<?php echo $item['Service']['name']; ?>" class="whoisicon" />
	                <a rel="me" class="taggedlink" href="<?php echo $item['account_url']; ?>"><?php echo isset($item['title']) ? $item['title'] : $item['Service']['name']; ?></a>
	            </li>
	        <?php } ?>
	    </ul>
	    <?php if(isset($session_identity) && ($relationship_status == 'self' || $session_identity['local_username'] == $about_identity['namespace'])) { ?>
            <p>
                <?php echo $html->link('Add new service', '/' . ($relationship_status == 'self' ? $session_identity['local_username'] : $about_identity['local_username']) . '/settings/accounts/add/', array('class' => 'addmore')); ?>
            </p>
	    <?php } ?>
	    <hr />

    <?php if($relationship_status == 'self') { ?>
        <span class="more"><a href="<?php echo $noserub_url . '/settings/accounts/'; ?>">manage</a></span>
    <?php } ?>
	<h4>Contact</h4>
	<ul class="whoissidebar">
	    <?php if($menu['logged_in'] &&
	             $about_identity['namespace'] == '' && 
	             $relationship_status != 'self' && 
	             ($about_identity['allow_emails'] != 0 || ($about_identity['allow_emails'] == 1 && $relationship_status == 'contact'))) { ?>
		    <li><img src="<?php echo Router::url('/images/icons/services/email.gif'); ?>" height="16" width="16" alt="e-Mail" class="whoisicon" /> <a href="http://<?php echo $about_identity['username']; ?>/messages/new/">e-Mail</a></li>
		<?php } ?>
		<?php foreach($communications as $item) { ?>
	        <li>
	            <img src="<?php echo Router::url('/images/icons/services/') . $item['Service']['icon']; ?>" height="16" width="16" alt="<?php echo $item['Service']['name']; ?>" class="whoisicon" />
	            <a class="url" href="<?php echo $item['account_url']; ?>"><?php echo $item['Service']['name']; ?></a>
	        </li>
	    <?php } ?>
	</ul>
	<?php if(isset($session_identity) && ($relationship_status == 'self' || $session_identity['local_username'] == $about_identity['namespace'])) { ?>
        <p>
            <?php echo $html->link('Add new service', '/' . ($relationship_status == 'self' ? $session_identity['local_username'] : $about_identity['local_username']) . '/settings/accounts/', array('class' => 'addmore')); ?>
        </p>
    <?php } ?>
    <hr />

    </div>

<?php } ?>
