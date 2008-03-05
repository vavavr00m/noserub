<form id="AccountAddFormStep4" method="post" action="<?php echo $this->here ?>">
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <?php 
        $i = 0;
        foreach($data as $username => $name) { ?>
            <div style="float:left;margin:10px;border:1px solid;">
                Name: <strong><?php echo $name; ?></strong><br />
                <input type="hidden" name="data[<?php echo $i; ?>][username]" value="<?php echo $username; ?>">
                <input type="radio" name="data[<?php echo $i; ?>][action]" value="0" checked="checked">ignore<br />
                <input type="radio" name="data[<?php echo $i; ?>][action]" value="1">create as
                <input type="text" name="data[<?php echo $i; ?>][contactname]" value="<?php echo $username; ?>"><br />
                <input type="radio" name="data[<?php echo $i; ?>][action]" value="2">assign to
                <select name="data[<?php echo $i; ?>][contact]">
                    <?php foreach($contacts as $id => $username) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $username; ?></option>
                    <?php } ?>
                </select>
            </div>
    <?php
        $i++;
    } ?>
    <div style="clear:both;" />
    <input class="submitbutton" name="submit" type="submit" value="Add contacts"/>
    <input class="submitbutton" name="cancel" type="submit" value="Skip adding friends"/>
<?php echo $form->end(); ?>