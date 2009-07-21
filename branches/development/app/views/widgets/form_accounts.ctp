<?php 
    $services = Configure::read('services.list');
    $service_data = Configure::read('services.data');
    $service_types = Configure::read('service_types_list'); 
    unset($service_types[0]);
?>
<div class="widget form-accounts">
    <h2><?php __('Add new account'); ?></h2>
    <?php 
    echo $form->create(array('url' => '/settings/accounts/add/'));
    echo $noserub->fnSecurityTokenInput();
    echo $form->input('Account.service', array('label' => __('Service', true), 'type' => 'select', 'options' => $services));
    echo $form->input('Account.username', array('label' => __('Username', true)));
    echo $form->input('Account.label', array('label' => __('Label', true) . ' (' . __('optional', true) . ')'));
    echo $form->end(array('label' => __('Add', true))); 
    ?>
    <h2><?php __('Auto discover Profile URL'); ?></h2>
    <?php
    echo $form->create(array('url' => '/settings/accounts/add/'));
    echo $noserub->fnSecurityTokenInput();
    echo $form->input(
        'Account.url', 
        array(
            'label' => __('URL', true)
        ));
    echo $form->end(array('label' => __('Auto discover', true)));
    ?>

    <h2><?php __('Your Web Accounts'); ?></h2>

    <table class="listing">
        <thead>
            <tr>
                <th><?php __('Service'); ?></th>
                <th><?php __('Username'); ?></th>
                <th><?php __('Type'); ?></th>
                <th><?php __('Feed'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $item) { ?>
                <tr>
                    <td>
                        <img class="whoisicon" alt="<?php echo $service_data[$item['Account']['service']]['name']; ?>" src="<?php echo Router::url('/images/icons/services/' . $service_data[$item['Account']['service']]['icon']); ?>"/>
                        <?php echo $service_data[$item['Account']['service']]['name']; ?>
                    </td>
                    <td><?php if($item['Account']['account_url']) {
                        if($item['Account']['service'] == 'RSS-Feed') {
                            $username = $item['Account']['account_url'];
                        } else {
                            $username = $item['Account']['username'];
                        }
                        echo $html->link($username, $item['Account']['account_url']);
                    } else {
                        echo $item['Account']['username'];
                    } ?></td>
                    <td><?php if($service_data[$item['Account']['service']]['is_contact']) {
                        __('Communication');
                    } else {
                        echo $service_types[$item['Account']['service_type']]; 
                    } ?></td>
                    <td><?php if($item['Account']['feed_url']) {
                        echo $html->link(__('RSS', true), $item['Account']['feed_url']);
                    } ?></td>
                    <td><?php echo $html->link(__('Edit', true), '/settings/accounts/edit/id:' . $item['Account']['id']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>