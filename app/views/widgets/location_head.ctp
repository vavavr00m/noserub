<?php if(Context::locationId()) { ?>
    <h1><?php __('Location') ?> <span><?php echo $html->link(Context::locationName(), '/locations/view/' . Context::locationId() . '/' . Context::locationSlug()); ?></span></h1>
<?php } ?>