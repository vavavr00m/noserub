<?php if(Context::groupSlug()) { ?>
    <h1><?php __('Group') ?> <span><?php echo $html->link(Context::groupName(), '/groups/view/' . Context::groupSlug()); ?></span></h1>
<?php } ?>