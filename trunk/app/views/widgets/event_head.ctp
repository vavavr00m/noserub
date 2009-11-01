<?php if(Context::eventId()) { ?>
    <h1><?php __('Event') ?> <span><?php echo $html->link(Context::eventName(), '/events/view/' . Context::eventId() . '/' . Context::eventSlug()); ?></span></h1>
<?php } ?>