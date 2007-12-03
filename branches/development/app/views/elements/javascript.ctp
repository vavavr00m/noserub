<script type="text/javascript">
  var nr_data = {'debug':<?php echo (defined('JS_DEBUG') && JS_DEBUG) ? 'true' : 'false'; ?>,'controller':'<?php echo $this->name; ?>', 'action':'<?php echo $this->action; ?>'};
</script>
<?php echo $javascript->link('jquery-1.2.1.min.js'); ?>
<?php echo $javascript->link('jQuery.jTagging.js'); ?>
<?php echo $javascript->link('noserub.js'); ?>