<h4>
	Filter
</h4>
<p>
    <form method="post" action="<?php echo $this->here; ?>">
        <div class="input">
            <label>Display contacts:</label>
            <select name="data[TagFilter][id]" size="1">
                <option value="0">
                    All
                </option>
                <?php foreach($tag_filter_list['noserub_contact_type_ids'] as $id => $tag) { ?>
                    <option value="<?php echo $id; ?>">
                        <?php echo $tag; ?>
                    </option>
                <?php } ?>
                <?php foreach($tag_filter_list['contact_type_ids'] as $id => $tag) { ?>
                    <option value="<?php echo $id; ?>">
                        <?php echo $tag; ?>
                    </option>
                <?php } ?>
            </select>
            <input class="submitbutton" type="submit" value="Update"/>
        </div>
    </form>
</p>
<hr />