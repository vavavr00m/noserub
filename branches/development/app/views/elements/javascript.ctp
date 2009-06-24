<?php echo $javascript->link('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'); ?>
<?php echo $javascript->object(Context::forJs(), array('prefix' => 'var noserub_context=', 'block' => true)); ?>
<?php echo $javascript->link('theme.js'); ?>
<?php echo $javascript->link('noserub.js'); ?>