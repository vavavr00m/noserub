<?php if(empty($data)) { ?>
    <p>
        No items there yet.
    </p>
<?php } else if($filter == 'photo') { ?>
    <?php foreach($data as $date => $cluster) { ?>
        <?php foreach($cluster as $item) { ?>
            <div style="float:left;padding:10px;">
                <?php echo $item['content']; ?><br />
                <?php echo $html->link($item['username'], $item['url']); ?>
            </div>
        <?php } ?>
    <?php } ?>
<? } else { ?>
    <?php
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        foreach($data as $date => $cluster) { ?>
            <h2>
                <?php if($date == $today) { 
                    echo 'Today'; 
                } else if($date == $yesterday) {
                    echo 'Yesterday';
                } else {
                    echo $date;
                } ?>
            </h2>
            <ul>
                <?php foreach($cluster as $item) { ?>
                    <li class="<?php echo $item['type']; ?>">
                        <span>
                            <?php
                                $splitted = split('/', $item['username']);
                                $splitted2 = split('@', $splitted[count($splitted)-1]);
                                $username = $splitted2[0];
                                $intro = str_replace('@user@', '<a href="http://'.$item['username'].'">'.$username.'</a>', $item['intro']);
                                $intro = str_replace('@item@', '<a href="'.$item['url'].'">'.$item['title'].'</a>', $intro);
                                echo $intro; 
                            ?>
                        </span>
                        <span>
                            <?php echo $item['datetime']; ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
<?php } ?>