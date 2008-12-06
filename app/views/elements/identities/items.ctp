<?php
$max_num_items_per_day = 10;

if(isset($filter) && is_array($filter) && count($filter) == 1 && in_array('photo', $filter)) {
    $filter = 'photo';
} else {
    # just for now, as we only have a special view for photo
    $filter = '';
}
?>
<div id="network">
 <?php if(empty($data)) { ?>
    <p>
    	<?php __("There are no updates from your social network or own activity yet.<br />Why don't you add some friends or some more of your own accounts?"); ?>
    </p>
<?php } else { ?>
    <?php
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        $days = 0;
        foreach($data as $date => $cluster) {
            $days++;
            if($days === 14) {
                break;
            }
            
            if($filter != 'photo') {
                $num_of_activities = count($cluster);
                echo '<span class="more">';
                $label = sprintf(__('%d activities', true), $num_of_activities);
                if($num_of_activities > $max_num_items_per_day) {
                    echo '<a href="#">' . $label . '</a>';
                } else {
                    echo $label;
                }
                echo '</span>';
            }
        
            if($date == $today) { 
                echo '<h2>' . __('Today', true) . '</h2>'; 
            } else if($date == $yesterday) {
                echo '<h2>' . __('Yesterday', true) . '</h2>';
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
                        echo __('From', true) . ' <a href="http://' . $item['Identity']['username'] . '">' . $label . '</a>';
                    ?>
                    </span>
                <?php } ?>
                <br class="clear" />
            <?php } else { 
                $num_displayed = 0;
                ?>
                
                <ul class="networklist">
                    <?php foreach($cluster as $item) { ?>
                        <?php
                            if($num_displayed == $max_num_items_per_day) {
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
</div>
