<div id="network">
 <?php if(empty($data)) { ?>
    <p>
        <?php if($menu['main'] == 'network') { ?>
	        There are no updates from your social network yet.<br />
	        Why don't you add some friends?
        <?php } else { ?>
	        There are no updates from your own activity yet.<br />
	        Why don't you add some more of your own accounts?
        <?php } ?>
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
            if($date == $today) { 
                echo '<h2>Today</h2>'; 
            } else if($date == $yesterday) {
                echo '<h2>Yesterday</h2>';
            } else {
                echo '<h3>' . date('F jS, Y', strtotime($date)) . '</h3>';
            }
            if($filter == 'photo') {
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
            <?php } else { ?>
                <ul class="networklist">
                    <?php foreach($cluster as $item) { ?>
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
                                    $intro = str_replace('@item@', '»<a class="external" href="'.$item['url'].'">'.$item['title'].'</a>«', $intro);
                                    echo $intro; 
                                ?>
                            </span>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } ?>
<?php } ?>
</div>