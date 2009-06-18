<h2><?php __('Your Communication Accounts'); ?></h2>
<?php

echo $form->create(array('url' => '/settings/accounts/communication/'));
?><input type="hidden" name="security_token" value="<?php echo $noserub->fnSecurityToken(); ?>">

<table class="listing">
    <thead>
        <tr>
            <th><?php __('Service'); ?></th>
            <th><?php __('Username'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $item) { ?>
            <tr>
                <td>
                    <img class="whoisicon" alt="<?php echo $item['Service']['name']; ?>" src="<?php echo Router::url('/images/icons/services/' . $item['Service']['icon']); ?>"/>
                    <?php echo $item['Service']['name']; ?>
                </td>
                <td>
                    <?php if(isset($item['Account'])) {
                        $value = $item['Account']['username'];
                    } else {
                        $value = '';
                    }?>
                    <input type="text" size="32" value="<?php echo $value; ?>" name="data[Service][<?php echo $item['Service']['id']; ?>][username]">
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php echo $form->end(array('label' => __('Save', true))); ?>