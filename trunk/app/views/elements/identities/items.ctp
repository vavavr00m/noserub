<div id="network">
 <?php if(empty($data)) { ?>
    <p>
        There are no updates from your social network yet.<br />
        Why don't you add some friends?
    </p>
<?php } else if($filter == 'photo') { ?>
    <?php foreach($data as $date => $cluster) { ?>
        <?php foreach($cluster as $item) { ?>
                <span class="photothumb">
                <?php echo $item['content']; ?><br />
                From <?php echo $html->link($item['username'], $item['url']); ?>
                </span>
        <?php } ?>
    <?php } ?>
<? } else { ?>
    <?php
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        foreach($data as $date => $cluster) { ?>
            
                <?php if($date == $today) { 
                    echo '<h2>Today</h2>'; 
                } else if($date == $yesterday) {
                    echo '<h2>Yesterday</h2>';
                } else {
                    echo '<h3>' . $date . '</h3>';
                } ?>

            <ul class="networklist">
                <?php foreach($cluster as $item) { ?>
                    <li class="<?php echo $item['type']; ?>">
                        <span class="date">
                            <?php echo $item['datetime']; ?>
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
</div>