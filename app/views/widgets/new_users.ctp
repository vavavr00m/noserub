<div class="widget widget-new-users">
    <?php if($data) { ?>
        <h2><?php __('Newbies'); ?></h2>
        <?php echo $this->element('contacts/box'); ?>
    <?php } ?>
</div>