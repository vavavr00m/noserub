<form id="IdentitySettingsForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>Personal data</legend>
        <?php 
            echo $form->input('Identity.firstname', array('label' => 'Your firstname',
                                                          'size'  => 32)); 
        ?>
        <?php 
            echo $form->input('Identity.lastname', array('label' => 'Your lastname',
                                                          'size'  => 32)); 
        ?>
        <label>Sex</label>
        <input type="radio" name="data[Identity][sex]" value="0"<?php echo $this->data['Identity']['sex'] == 0 ? ' checked="checked"' : ''; ?>> <span>rather not say</span>
        <input type="radio" name="data[Identity][sex]" value="1"<?php echo $this->data['Identity']['sex'] == 1 ? 'checked="checked"' : ''; ?>> <span>female</span>
        <input type="radio" name="data[Identity][sex]" value="2"<?php echo $this->data['Identity']['sex'] == 2 ? 'checked="checked"' : ''; ?>> <span>male</span>
    </fieldset>
    
    <fieldset>
        <legend>Geolocation</legend>
        <p>
            The address is used to determine the geolocation. The address will <strong>not</strong> be displayed to anyone else, just the geolocation, if you enter a valid address.
        </p>
        <?php 
            echo $form->input('Identity.address', array('label' => 'Address for geolocation',
                                                        'size'  => 64)); 
        ?>

        <p class="geolocation">Latitude<br /><strong><?php echo $this->data['Identity']['latitude']; ?></strong></p>
        <p class="geolocation">Longitude<br /><strong><?php echo $this->data['Identity']['longitude']; ?></strong></p>
        
    </fieldset>
    
    <fieldset>
        <?php echo $form->submit('Save changes'); ?>
    </fieldset>
<?php echo $form->end(); ?>