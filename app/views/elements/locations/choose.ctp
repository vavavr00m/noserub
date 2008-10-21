<?php
    $noserub_url = 'http://' . $identity['username'];
?>
<span class="more"><a href="<?php echo $noserub_url . '/settings/locations/'; ?>">manage</a></span>
	
<h4>Location</h4>
	
<form class="locator" method="POST" action="<?php echo $this->here; ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <div class="input">
    <label>I'm currently at</label>
    <select name="data[Locator][id]" size="1">
        <?php $selected_location = $identity['last_location_id']; ?>
        <?php foreach($locations as $id => $name) { ?>
            <option <?php if($id == $selected_location) { echo 'selected="selected" '; } ?>value="<?php echo $id; ?>"><?php echo $name; ?></option>
        <?php } ?>
        <option value="0">[somewhere else]</option>
    </select>
    <label id="locator_name" for="data[Locator][name]">Where are you then?</label>
    <input type="text" name="data[Locator][name]" value="">
    <input class="submitbutton" type="submit" value="Update"/>
    </div>
</form>