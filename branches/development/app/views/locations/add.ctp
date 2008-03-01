<form id="LocationAddForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>
		Add a new location    
        </legend>
        <p>
        The name will be displayed, when you choose a location as your curent one. <br />
        The address is only used to geocode the location and will not be displayed.<br />
            You can mostly just use <em>Town, Country</em> to specify an address.</p>
        <?php echo $form->input('Location.name', array('label' => 'Name', 'size' => 64, 'error' => 'You need to specify a name.')); ?>
        <?php echo $form->input('Location.address', array('label' => 'Address', 'size' => 64)); ?>
    </fieldset>
    <fieldset>
        <?php if($this->action == 'add') { ?>
            <input class="submitbutton" type="submit" value="Create Location"/>
        <?php } else { ?>
            <input class="submitbutton" type="submit" value="Save"/>
        <?php } ?>
    </fieldset>
</form>