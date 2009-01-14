<script type="text/javascript">
  var nr_data = {'debug':<?php echo (defined('JS_DEBUG') && JS_DEBUG) ? 'true' : 'false'; ?>,'controller':'<?php echo $this->name; ?>', 'action':'<?php echo $this->action; ?>'};
</script>
<?php echo $javascript->link('http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js'); ?>
<?php echo $javascript->link('noserub.js'); ?>