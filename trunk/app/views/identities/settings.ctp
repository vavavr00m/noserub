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
        <input type="radio" name="data[Identity][sex]" value="0"<?php echo $this->data['Identity']['sex'] == 0 ? ' checked="checked"' : ''; ?>>rather not say
        <input type="radio" name="data[Identity][sex]" value="1"<?php echo $this->data['Identity']['sex'] == 1 ? 'checked="checked"' : ''; ?>>female
        <input type="radio" name="data[Identity][sex]" value="2"<?php echo $this->data['Identity']['sex'] == 2 ? 'checked="checked"' : ''; ?>>male
    </fieldset>
    
    <fieldset>
        <legend>Geolocation</legend>
        <p>
            The address is used to determine the geolocation. The address will 
            not be displayed to anyone else, just the geolocation, if you enter
            a valid address.
        </p>
        <?php 
            echo $form->input('Identity.address', array('label' => 'Address for geolocation',
                                                        'size'  => 64)); 
        ?>
        <label>Latitude</label>
        <?php echo $this->data['Identity']['latitude']; ?>
        <br />
        <label>Longitude</label>
        <?php echo $this->data['Identity']['longitude']; ?>
    </fieldset>
    
    <fieldset>
        <legend>E-Mail & Password</legend>
        <p>
            When you change your e-mail address, you will be logged out and 
            can only log in again, when you confirm the new e-mail address by
            clicking on the link we send to your new e-mail address.
        </p>
        <?php 
            echo $form->input('Identity.email', array('label' => 'E-Mail address', 
                                                      'size'  => 32,
                                                      'error' => 'Please enter a valid E-Mail address')); 
        ?>
        <?php 
        
            echo $form->input('Identity.passwd', array('type'  => 'password',
                                                       'label' => 'Password', 
                                                       'error' => 'Passwords must be at least 6 characters in length')); 
        ?>
        <?php 
            echo $form->input('Identity.passwd2', array('type' => 'password', 
                                                        'label' => 'Password repeated', 
                                                        'error' => 'Passwords must match')); ?>
    </fieldset>
    
    <fieldset>
        <?php echo $form->submit('Save changes'); ?>
    </fieldset>
<?php echo $form->end(); ?>