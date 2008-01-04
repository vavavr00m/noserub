<form id="LocationAddForm" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <legend>
            The name will be displayed, when you choose a location as your curent one.<br />
            The address is only used to geocode the location and will not be displayed.<br />
            You can mostly just use <em>Town, Country</em> to specify an address.
        </legend>
        <?php echo $form->input('Location.name', array('label' => 'Name', 'value' => '', 'size' => 64, 'error' => 'You need to specify a name.')); ?>
        <?php echo $form->input('Location.address', array('label' => 'Address', 'value' => '', 'size' => 64)); ?>
    </fieldset>
    <fieldset>
        <input class="submitbutton" type="submit" value="Create Location"/>
    </fieldset>
</form>