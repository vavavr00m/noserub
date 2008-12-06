<?php
    $noserub_url = 'http://' . $identity['username'];
?>
<span class="more"><a href="<?php echo $noserub_url . '/settings/locations/'; ?>"><?php __('manage'); ?></a></span>
	
<h4><?php __('Location'); ?></h4>
	
<form class="locator" method="POST" action="<?php echo $this->here; ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <div class="input">
    <label><?php __("I'm currently at"); ?></label>
    <select name="data[Locator][id]" size="1">
        <?php $selected_location = $identity['last_location_id']; ?>
        <?php foreach($locations as $id => $name) { ?>
            <option <?php if($id == $selected_location) { echo 'selected="selected" '; } ?>value="<?php echo $id; ?>"><?php echo $name; ?></option>
        <?php } ?>
        <option value="0">[<?php __('somewhere else'); ?>]</option>
    </select>
    <label id="locator_name" for="data[Locator][name]"><?php __('Where are you then?'); ?></label>
    <input type="text" name="data[Locator][name]" value="">
    <input class="submitbutton" type="submit" value="<?php __('Update'); ?>"/>
    </div>
</form>