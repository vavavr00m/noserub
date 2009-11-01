<?php
    if(Context::isSelf()) {
        if($data) {
            $label = __('manage', true);
        } else {
            $label = __('add new', true);
        }
    } else {
        $label = false;
    }
?>

<div class="widget widget-contacts-box">
    <?php if($label) { ?>
        <h2>
			<?php __('My Contacts'); ?>
            <a class="more" href="<?php echo Router::Url('/' . $session->read('Identity.local_username') . '/contacts/'); ?>">
                (<?php echo $label; ?>)
            </a>
		</h2>
    <?php } ?>

    <?php if($data) { ?>
        <?php echo $this->element('contacts/box'); ?>
    <?php } ?>
</div>
