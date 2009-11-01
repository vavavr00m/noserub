<?php
$max_num_items_per_day = 10;

if(isset($filter) && is_array($filter) && count($filter) == 1 && in_array('photo', $filter)) {
    $filter = 'photo';
} else {
    # just for now, as we only have a special view for photo
    $filter = '';
}
?>
<div class="widget widget-lifestream">
    <h2><?php __('Lifestream'); ?></h2>
     <?php if(empty($data)) { ?>
        <p>
        	<?php __("There are no updates from your social network or own activity yet.<br />Why don't you add some friends or some more of your own accounts?"); ?>
        </p>
    <?php } else { 
        echo $this->element('entries');
    } ?>
</div>