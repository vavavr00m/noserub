<form id="AccountAddFormStep2" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <?php echo $form->input('Account.feed_url', array('label' => 'RSS-Feed:', 
                                                          'error' => 'The RSS-Feed could not be read.')); ?>
        <label>
        <?php echo $form->select('Account.service_type_id', $service_types, array('3'), null, null, false); ?>           
        <?php echo $form->submit('Peview >'); ?>
    </fieldset>
<?php echo $form->end(); ?>