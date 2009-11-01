<?php if(!empty($data)) { ?>
    <div class="widget widget-last-events">
    	<h2><?php __('Last Events'); ?></h2>
        <?php echo $this->element('events/box'); ?>
    </div>
<?php } ?>