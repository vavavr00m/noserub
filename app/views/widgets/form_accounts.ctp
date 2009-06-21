<h2><?php __('Add new account'); ?></h2>
<?php 
echo $form->create(array('url' => '/settings/accounts/add/'));
echo '<input type="hidden" name="security_token" value="' . $noserub->fnSecurityToken() . '" />';
echo $form->input('service_id', array('label' => __('Service', true), 'type' => 'select', 'options' => $services));
echo $form->input('username', array('label' => __('Username', true)));
echo $form->input('label', array('label' => __('Label', true) . ' (' . __('optional', true) . ')'));
echo $form->end(array('label' => __('Add', true))); 
?>
<h2><?php __('Auto discover Profile URL'); ?></h2>
<?php
echo $form->create(array('url' => '/settings/accounts/add/'));
echo '<input type="hidden" name="security_token" value="' . $noserub->fnSecurityToken() . '" />';
echo $form->input(
    'url', 
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
                    <img class="whoisicon" alt="<?php echo $item['Service']['name']; ?>" src="<?php echo Router::url('/images/icons/services/' . $item['Service']['icon']); ?>"/>
                    <?php echo $item['Service']['name']; ?>
                </td>
                <td><?php if($item['Account']['account_url']) {
                    echo $html->link($item['Account']['username'], $item['Account']['account_url']);
                } else {
                    echo $item['Account']['username'];
                } ?></td>
                <td><?php if($item['Service']['is_contact']) {
                    __('Communication');
                } else {
                    echo $service_types[$item['Account']['service_type_id']]; 
                } ?></td>
                <td><?php if($item['Account']['feed_url']) {
                    echo $html->link(__('RSS', true), $item['Account']['feed_url']);
                } ?></td>
                <td><?php echo $html->link(__('Edit', true), '/settings/accounts/edit/id:' . $item['Account']['id']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>