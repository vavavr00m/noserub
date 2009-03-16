<?php if(!$networks) { ?>
    <p>
        <?php __('There are no networks available for subscription'); ?>
    </p>
<?php } else { 
    echo $form->create(array('url' => '/networks/subscription/'));

    foreach($networks as $network) {
        $value = $network['NetworkSubscriber'] ? -1 : 1;
        echo $form->checkbox('Subscribe.network_id', array( 'name' => 'data[SubscribeNetwork][' . $network['Network']['id'] . ']', 'value' => $value));
        if($value == -1) {
            echo sprintf(__('Unsubscribe from %s', true), $network['Network']['name']);
        } else {
            echo sprintf(__('Subscribe to %s', true), $network['Network']['name']);
        }
        echo '<br />';
    } 

    echo $form->end(array('label' => __('Save', true)));
} ?>