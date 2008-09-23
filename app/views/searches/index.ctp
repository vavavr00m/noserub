<?php 
    $q = isset($q) ? $q : '';
?>
<form action="<?php echo $this->here; ?>" method="get">
    <fieldset>
        <label>with all the following words:</label>
        <div class="input text">
            <input type="text" size="64" name="q" value="<?php echo $q; ?>">
            <input class="submitbutton" type="submit" value="Go"/>
        </div>
    </fieldset>
</form>

<?php if($items) { ?>
    <div class="vcard">
        <div>
            <h4>Search results for <strong><?php echo $q; ?></strong></h4>
            <?php echo $this->element('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->element('identities/items', array('data' => $items, 'filter' => $filter)); ?>
        </div>
    </div>
    
<?php } ?>