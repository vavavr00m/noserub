<?php if($items) { ?>
    <div class="vcard">
        <div>
            <h4><?php echo sprintf(__('Entries that %s commented on.', true), $identity['Identity']['local_username']); ?></h4>
            <?php echo $this->element('subnav', array('no_wrapper' => true)); ?>
            <?php echo $this->element('identities/items', array('data' => $items)); ?>
        </div>
    </div>    
<?php } ?>