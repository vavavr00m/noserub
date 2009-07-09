<div class="widget widget-last-logged-in-users">
    <?php if($data) { ?>
        <h2><?php __('Recently Logged in Users'); ?></h2>
        <?php echo $this->element('contacts/box'); ?>
    <?php } ?>
</div>