<div class="widget widget-new-groups">
    <?php if($groups) { ?>
        <h2><?php __('New Groups'); ?></h2>
        <?php echo $this->element('groups/box'); ?>
    <?php } ?>
</div>