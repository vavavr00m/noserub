<div class="widget widget-main-navigation">
    <?php if(Context::read('logged_in_identity')) { ?>
        <?php
            $base_url = '/' . Context::read('logged_in_identity.local_username') . '/';
        ?>
        <?php echo $noserub->widgetLoggedInUser(); ?>
	
	    <?php echo $noserub->widgetUnreadMessages(); ?>
		
        <ul>
            <li class="home">
				<span class="icon"></span>
                <?php echo $html->link(__('Home', true), '/activities/', array('class' => 'head')); ?>
            </li>
            <li class="contacts">
				<span class="icon"></span>
    			<a class="toggle" href="#">(-)</a>
    			<?php echo $html->link(__('My Contacts', true), '/contacts/', array('class' => 'head')); ?>
    			<ul>
    				<li><?php echo $html->link(__('Add new contact', true), $base_url . 'contacts/add/'); ?></li>
    			</ul>
    		</li>
            <li class="profile">
				<span class="icon"></span>
                <a class="toggle" href="#">(-)</a>
                <?php echo $html->link(__('My Profile', true), $base_url, array('class' => 'head')); ?>
                <ul>
                    <li><?php echo $html->link(__('My Accounts', true), '/settings/accounts/'); ?></li>
                    <li><?php echo $html->link(__('My Comments', true), $base_url . 'comments/'); ?></li>
                    <li><?php echo $html->link(__('My Favorites', true), $base_url . 'favorites/'); ?></li>
                    <li><?php echo $html->link(__('My Messages', true), '/messages/inbox/'); ?></li>
                </ul>
            </li>
            <li class="groups">
				<span class="icon"></span>
                <a class="toggle" href="#">(-)</a>
                <?php echo $html->link(__('Groups', true), '/groups/', array('class' => 'head')); ?>
                <ul>
                    <li><?php echo $html->link(__('My Groups', true), $base_url . '/groups/'); ?></li>
                    <?php foreach($groups as $group) { ?>
                        <li><?php echo $html->link($group['name'], '/groups/view/' . $group['slug']); ?></li>
                    <?php } ?>
                </ul>
            </li>
<?php 
/*
            <li class="locations">
				<span class="icon"></span>
                <a class="toggle" href="#">(-)</a>
                <?php echo $html->link(__('Locations', true), $base_url . '/locations/', array('class' => 'head')); ?>
                <?php if($locations) { ?>
                    <ul>
                        <?php foreach($locations as $location) { ?>
                            <li><?php echo $html->link($location['Location']['name'], '/locations/view/' . $location['Location']['id'] . '/' . $location['Location']['slug']); ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
            <li class="events">
				<span class="icon"></span>
                <a class="toggle" href="#">(-)</a>
                <?php echo $html->link(__('Events', true), $base_url . '/events/', array('class' => 'head')); ?>
                <?php if($events) { ?>
                    <ul>
                        <?php foreach($events as $event) { ?>
                            <li><?php echo $html->link($event['Event']['name'], '/events/view/' . $event['Event']['id'] . '/' . $event['Event']['slug']); ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>    
            <li class="networks">
				<span class="icon"></span>
                <a class="toggle" href="#">(-)</a>
                <?php echo $html->link(__('Networks', true), $base_url . '/networks/', array('class' => 'head')); ?>
                <?php if($networks) { ?>
                    <ul>
                        <?php foreach($networks as $network) { ?>
                            <li>
                                <?php if($network['id'] == Context::read('Network.id')) {
                                    echo $network['name'];
                                } else {
                                    echo $html->link($network['name'], $network['url']); 
                                } ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
*/
?>
        </ul>
    <?php } else { ?>
        <?php echo $this->element('login'); ?>
    <?php } ?>
</div>
