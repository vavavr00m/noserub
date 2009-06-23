<h2><?php __('Add new location'); ?></h2>
<p>
    <?php __('The name will be displayed, when you choose a location as your curent one. <br />
    The address is only used to geocode the location and will not be displayed.<br />
    You can mostly just use <em>Town, Country</em> to specify an address.'); ?>
</p>
<?php
echo $form->create(array('url' => '/settings/locations/add/'));
echo '<input type="hidden" name="security_token" value="' . $noserub->fnSecurityToken() . '" />';
echo $form->input('Location.name', array('label' => __('Name', true), 'size' => 64));
echo $form->input('Location.address', array('label' => __('Address', true), 'size' => 64));
echo $form->end(array('label' => __('Create Location', true))); 
?>
<h2><?php __('Your locations'); ?></h2>
<?php if(!$data) {
    __('You did not create any locations yet.');
} else { ?>
    <table>
        <thead>
            <tr>
                <th><?php __('Name'); ?></th>
                <th><?php __('Address (hidden)'); ?></th>
                <th><?php __('Location'); ?></th>
                <th></th>
            </tr>
        </thead>
        <?php foreach($data as $item) { ?>
            <tr>
                <td><?php echo $item['Location']['name']; ?></td>
                <td><?php echo $item['Location']['address'] == '' ? '<em>' . __('Not entered', true) . '</em>' : $item['Location']['address']; ?></td>
                <td>
                    <?php __('Latitude'); ?>: <?php echo $item['Location']['latitude']; ?><br />
                    <?php __('Longitude'); ?>: <?php echo $item['Location']['longitude']; ?>
                </td>
                <td>
                    <?php echo $html->link(__('Edit', true), '/settings/locations/edit/id:' . $item['Location']['id']); ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>