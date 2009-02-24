<?php if($context['logged_in_identity']) { ?>
    <?php echo sprintf(__('Hi %s!', true), $context['logged_in_identity']['name']); ?>
    <ul id="nav main">
        <li id="home">
            <?php echo $html->link(__('Home', true), '/' . $context['logged_in_identity']['local_username'] . '/network/'); ?>
        </li>
        <li id="contacts">
            <h2><?php __('Contacts'); ?></h2>
            <ul>
                <li><?php echo $html->link(__('My Contacts', true), '/' . $context['logged_in_identity']['local_username'] . '/contacts/'); ?></li>
            </ul>
        </li>
        <li id="profile">
            <?php echo $html->link(__('My Profile', true), '/' . $context['logged_in_identity']['local_username'] . '/'); ?>
        </li>
        <li id="groups">
            <?php echo $html->link(__('Groups', true), '/groups/'); ?>
            <?php if($groups) { ?>
                <ul>
                    <?php foreach($groups as $group) { ?>
                        <li><?php echo $html->link($group['name'], '/groups/' . $group['slug']); ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
        <li id="networks">
            <?php echo $html->link(__('Networks', true), '/networks/'); ?>
            <?php if($networks) { ?>
                <ul>
                    <?php foreach($networks as $network) { ?>
                        <li>
                            <?php if($network['id'] == $context['network_id']) {
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
    <?php echo $noserub->widgetAdminMenu(); ?>
<?php } else { ?>
    <?php echo $this->element('login'); ?>
<?php } ?>
