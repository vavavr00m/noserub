<div class="widget form-network-settings">
    <?php if($this->data) {
        echo $form->create(array('url' => '/admins/ad_management/'));

        echo $form->submit(__('Save', true));
        echo $form->submit(__('Cancel', true), array('name' => 'cancel'));
        echo $form->end(null);
    } else { ?>
        <p>
            <?php __('You need to be logged in to the Admin area to see something here.'); ?>
        </p>
    <?php } ?>
</div>