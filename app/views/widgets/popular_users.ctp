<div class="widget widget-popular-users">
    <?php if($data) { ?>
        <h2><?php __('Popular Users'); ?></h2>
        <?php echo $this->element('contacts/box'); ?>
    <?php } ?>
</div>