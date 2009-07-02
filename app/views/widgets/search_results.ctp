<div class="widget widget-search-result">
	<?php if($items) { ?>
        <h2><?php echo sprintf(__('Search results for %s', true), '<strong>' . $q . '</strong>'); ?></h2>
    <?php } ?>

    <?php if($items) { ?>
        <?php echo $this->element('identities/items', array('data' => $items, 'filter' => array())); ?>
    <?php } ?>
    <?php if ($q && empty($items)) { ?>
    	<div>
    		<h4><?php echo __('No results found.', true); ?></h4>
    	</div>
    <?php }?>
</div>