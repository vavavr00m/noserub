<form id="IdentitySettingsForm" enctype="multipart/form-data" method="post" action="<?php echo $this->here; ?>">
        <div id="settings_photo">
        <fieldset>
        <legend>Photo</legend>
          <?php if($this->data['Identity']['photo']) { ?>
            <p>
                <strong>Your current photo:</strong><br />
                <img src="<?php echo FULL_BASE_URL . Router::url('/static/avatars/'.$this->data['Identity']['photo'].'.jpg'); ?>" width="150" height="150" alt="Your current photo" class="mypicture" />
            </p>

        <?php } ?>
        
        <p>
            Size may not exceed 150x150 pixels. If you don't have one with the right size, try <a href="http://www.mypictr.com/">myPictr.com</a>.<br />
            GIF, JPG and PNG allowed.
        </p>
        <label>Photo/Portrait:</label>
        <input type="file" name="data[Identity][photo]" />
        <p><input class="submitbutton" type="submit" value="Save changes"/></p>
    </fieldset>
    </div>
    
    <div id="settings_data">
    
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
        <input type="radio" name="data[Identity][sex]" value="1"<?php echo $this->data['Identity']['sex'] == 1 ? ' checked="checked"' : ''; ?>> <span>female</span>
        <input type="radio" name="data[Identity][sex]" value="2"<?php echo $this->data['Identity']['sex'] == 2 ? ' checked="checked"' : ''; ?>> <span>male</span>
    </fieldset>
    
    <fieldset>
        <legend>Make a statement</legend>
        <p>
			HTML is not allowed; newlines are preserved; URLs with http:// and https:// will turn into links
        </p>
        <?php echo $form->textarea('Identity.about', array('label' => 'About')); ?>
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
        <input class="submitbutton" type="submit" value="Save changes"/>
    </fieldset>
<?php echo $form->end(); ?>

</div>