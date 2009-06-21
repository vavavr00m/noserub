<?php if(Context::read('logged_in_identity')) { ?>
    <?php
        $base_url = '/' . Context::read('logged_in_identity.local_username') . '/';
    ?>
    <h4>
        <?php echo $html->image($noserub->fnProfilePhotoUrl('small'), array('class' => 'userimage', 'alt' => Context::read('logged_in_identity.name'))); ?>
		<?php echo sprintf(__('Hi %s!', true), Context::read('logged_in_identity.name')); ?>
	</h4>
	
    <p class="notifications">
		3 new notifications<br />
		<a href="#">check them out</a>
	</p>
		
    <ul>
        <li>
            <?php echo $html->link(__('Home', true), '/activities/'); ?>
        </li>
        <li>
			<a class="toggle" href="#">(-)</a>
			<a href="#"><?php __('Contacts'); ?></a>
			<ul>
				<li><?php echo $html->link(__('My Contacts', true), $base_url . 'contacts/'); ?></li>
				<li><?php echo $html->link(__('Add new contact', true), $base_url . 'contacts/add/'); ?></li>
			</ul>
		</li>
        <li>
            <a class="toggle" href="#">(-)</a>
            <?php echo $html->link(__('My Profile', true), $base_url); ?>
            <ul>
                <li><?php echo $html->link(__('My Accounts', true), '/settings/accounts/'); ?></li>
            </ul>
        </li>
        <li>
            <a class="toggle" href="#">(-)</a>
            <?php echo $html->link(__('Groups', true), $base_url . '/groups/'); ?>
            <?php if($groups) { ?>
                <ul>
                    <?php foreach($groups as $group) { ?>
                        <li><?php echo $html->link($group['name'], '/groups/' . $group['slug']); ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
        <li>
            <a class="toggle" href="#">(-)</a>
            <?php echo $html->link(__('Networks', true), $base_url . '/networks/'); ?>
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
    </ul>
<?php } else { ?>
    <?php echo $this->element('login'); ?>
    <?php if(Context::read('network.registration_type') == 1) {
        echo $html->link(__('Register a new account', true), '/pages/register/');
    } ?>
<?php } ?>