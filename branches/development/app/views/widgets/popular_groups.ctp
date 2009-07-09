<div class="widget widget-popular-groups">
    <?php if($groups) { ?>
        <h2><?php __('Popular Groups'); ?></h2>
        <?php echo $this->element('groups/box'); ?>
    <?php } ?>
</div>