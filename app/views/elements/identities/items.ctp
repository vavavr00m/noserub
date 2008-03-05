<?php
$max_num_items_per_day = 10;
?>
<div id="network">
 <?php if(empty($data)) { ?>
    <p>
    	There are no updates from your social network or own activity yet.<br />
        Why don't you add some friends or some more of your own accounts?
    </p>
<?php } else { ?>
    <?php
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        $days = 0;
        foreach($data as $date => $cluster) {
            $days++;
            if($days === 6) {
                break;
            }
            
            if(count($filter) > 0 || (!in_array('photo', $filter))) {
                $num_of_activities = count($cluster);
                echo '<span class="more">';
                if($num_of_activities > $max_num_items_per_day) {
                    echo '<a href="#">' . $num_of_activities . ' activities</a>';
                } else {
                    echo $num_of_activities . ' activities';
                }
                echo '</span>';
            }
        
            if($date == $today) { 
                echo '<h2>Today</h2>'; 
            } else if($date == $yesterday) {
                echo '<h2>Yesterday</h2>';
            } else {
                echo '<h3>' . date('F jS, Y', strtotime($date)) . '</h3>';
            }
            if(count($filter) == 1 && in_array('photo', $filter)) {
                foreach($cluster as $item) { ?>
                    <span class="photothumb">
                    <?php echo $item['content']; ?><br />
                    <?php
                        $splitted = split('/', $item['username']);
                        $splitted2 = split('@', $splitted[count($splitted)-1]);
                        $username = $splitted2[0];
                        $label = wordwrap($username, 12, '<br />', true);
                        echo 'From <a href="http://' . $item['username'] . '">' . $label . '</a>';
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
                        ?>
                        <li class="<?php echo $item['type'] == 'photo' ? 'photos' : $item['type']; ?> icon">
                            <span class="date">
                                <?php if($date == $today) {
                                    echo $nicetime->show($item['datetime']); 
                                } else {
                                    echo date('H:s', strtotime($item['datetime'])); 
                                } ?>
                            </span>
                            <span>
                                <?php
                                    $splitted = split('/', $item['username']);
                                    $splitted2 = split('@', $splitted[count($splitted)-1]);
                                    $username = $splitted2[0];
                                    $intro = str_replace('@user@', '<a href="http://'.$item['username'].'">'.$username.'</a>', $item['intro']);
                                    if($item['url']) {
                                        $intro = str_replace('@item@', '»<a class="external" href="'.$item['url'].'">'.$item['title'].'</a>«', $intro);
                                    } else {
                                        $intro = str_replace('@item@', '»'.$item['title'].'«', $intro);
                                    }
                                    echo $intro; 
                                ?>
                            </span>
                        </li>
                        <?php $num_displayed++; ?>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } ?>
<?php } ?>
</div>
