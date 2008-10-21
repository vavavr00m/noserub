<?php
    $selected = $this->data['TagFilter']['id'];
?>
<h4>
	Filter
</h4>
<p>
    <form method="post" action="<?php echo $this->here; ?>">
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
        <div class="input">
            <label>Display contacts:</label>
            <select name="data[TagFilter][id]" size="1">
                <option value="0" <?php if('0' == $selected) { echo ' selected="selected"'; }?>>
                    All
                </option>
                <?php foreach($tag_filter_list['noserub_contact_type_ids'] as $id => $tag) { ?>
                    <option value="noserub.<?php echo $id; ?>"<?php if('noserub.'.$id == $selected) { echo ' selected="selected"'; }?>>
                        <?php echo $tag; ?>
                    </option>
                <?php } ?>
                <?php foreach($tag_filter_list['contact_type_ids'] as $id => $tag) { ?>
                    <option value="private.<?php echo $id; ?>"<?php if('private.'.$id == $selected) { echo ' selected="selected"'; }?>>
                        <?php echo $tag; ?>
                    </option>
                <?php } ?>
            </select>
            <input class="submitbutton" type="submit" value="Update"/>
        </div>
    </form>
</p>
<hr />