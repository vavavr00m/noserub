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

<?php if($label) { ?>
    <span class="more">
        <a href="<?php echo Router::Url('/' . $session->read('Identity.local_username') . '/contacts/'); ?>">
            <?php echo $label; ?>
        </a>
    </span>
    <h2><?php __('My Contacts'); ?></h2>
<?php } ?>

<?php if($data) { ?>
    <?php echo $this->element('contacts/box'); ?>
<?php } ?>