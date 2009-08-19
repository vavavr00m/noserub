<?php
$max_num_items_per_day = 10;

if(isset($filter) && is_array($filter) && count($filter) == 1 && in_array('photo', $filter)) {
    $filter = 'photo';
} else {
    # just for now, as we only have a special view for photo
    $filter = '';
}
?>
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
            
            if($filter != 'photo') {
                $num_of_activities = count($cluster);
                echo '<p class="more">';
                $label = sprintf(__('%d activities', true), $num_of_activities);
                if($num_of_activities > $max_num_items_per_day &&
                   $display_more_link) {
                    # if only one day is displayed, we don't
                    # need the "more" link
                    echo '<a href="#">' . $label . '</a>';
                } else {
                    echo $label;
                }
                echo '</p>';
            }
        
            if($date == $today) { 
                echo '<h3>' . __('Today', true) . '</h3>'; 
            } else if($date == $yesterday) {
                echo '<h3>' . __('Yesterday', true) . '</h3>';
            } else {
                echo '<h3>' . date('F jS, Y', strtotime($date)) . '</h3>';
            }
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
                $num_displayed = 0;
                ?>
                
                <ul class="lifestream">
                    <?php foreach($cluster as $item) { ?>
                        <?php
                            if($num_displayed == $max_num_items_per_day &&
                               $display_more_link) {
                               # if only one day is displayed, show all the
                               # entries, not only $max_num_items_per_day
                                echo '</ul>';
                                echo '<ul class="networklist extended">';
                            }
                        
                            echo $this->renderElement('entries/row_view', array('item' => $item, 'with_date' => ($date != $today)));
                        ?>
                        
                        <?php $num_displayed++; ?>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } ?>
<?php } ?>
