<div class="widget widget-search-results">
	<?php if($items) { ?>
        <h2><?php echo sprintf(__('Search results for %s', true), '<strong>' . $q . '</strong>'); ?></h2>
    <?php } ?>

    <?php if($items) { ?>
        <?php echo $this->element('identities/items', array('data' => $items, 'filter' => array())); ?>
    <?php } ?>
    <?php if ($q && empty($items)) { ?>
    	<p><?php echo __('No results found.', true); ?></p>
    <?php }?>
</div>
