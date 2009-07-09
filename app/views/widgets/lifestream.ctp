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
    <?php } else { ?>
        <?php
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 days'));
            $days = 0;
            $display_more_link = count($data) > 1;
            foreach($data as $date => $cluster) {
                $days++;
                if($days === 14) {
                    break;
                }
            
                if($date == $today) { 
                    echo '<h3>' . __('Today', true) . '</h3>'; 
                } else if($date == $yesterday) {
                    echo '<h3>' . __('Yesterday', true) . '</h3>';
                } else {
                    echo '<h3>' . date('F jS, Y', strtotime($date)) . '</h3>';
                }
                echo '<ul class="lifestream">';
            
                if($filter == 'photo') {
                    foreach($cluster as $item) { ?>
                        <span class="photothumb">
                        <?php echo $item['Entry']['content']; ?><br />
                        <?php
                            $splitted = split('/', $item['Identity']['username']);
                            $splitted2 = split('@', $splitted[count($splitted)-1]);
                            $username = $splitted2[0];
                            $label = wordwrap($username, 12, '<br />', true);
    						__('From');
    						echo ' ' . $html->link($label, '/entry/' . $item['Entry']['id']);
                        ?>
                        </span>
                    <?php } ?>
                    <br class="clear" />
                <?php } else { 
                    foreach($cluster as $item) {
                        echo $this->renderElement('entries/row_view', array('item' => $item, 'with_date' => ($date != $today)));    
                    }                    
                } ?>
                </ul>
            <?php } ?>
    <?php } ?>
</div>