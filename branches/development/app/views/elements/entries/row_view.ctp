<?php 
    if(!isset($with_date)) {
        $with_date = true;
    }
    if(!isset($permalink)) {
        $permalink = true;
    }
    
    $service_types = Configure::read('service_types');
?>
<li class="<?php echo $service_types[$item['Entry']['service_type']]['token']; ?> icon">
    <p>
    <span class="date">
        <?php
            if(!$with_date) {
                $label = $nicetime->show($item['Entry']['published_on']); 
            } else {
                $label = date('H:s', strtotime($item['Entry']['published_on'])); 
            }
            if($permalink) {
                echo $html->link($label, '/entry/' . $item['Entry']['id']);
            } else {
                echo $label;
            }
        ?>
    </span>
    <span>
        <?php
            $splitted = split('/', $item['Identity']['username']);
            $splitted2 = split('@', $splitted[count($splitted)-1]);
            $username = $splitted2[0];
            $intro = str_replace('@user@', '<a href="http://'.$item['Identity']['username'].'">'.$username.'</a>', $service_types[$item['Entry']['service_type']]['intro']);
            if($item['Entry']['service_type'] != 5 && $item['Entry']['url']) {
                $intro = str_replace('@item@', '»<a class="external" href="'.$item['Entry']['url'].'">'.$item['Entry']['title'].'</a>«', $intro);
            } else {
                $intro = str_replace('@item@', '»'.$item['Entry']['title'].'«', $intro);
            }
            echo $intro; 
        ?>
    </span>
	<?php if($item['Entry']['service_type'] == 1 || $item['Entry']['service_type'] == 6) { ?>
        <span>
            <br />
            <?php
                $content = $item['Entry']['content'];
                $raw_content = @unserialize(@base64_decode($item['Entry']['content']));
                if(!empty($raw_content['thumb'])) {
                    $image = $html->image($raw_content['thumb'], array('height' => 75, 'width' => 75));
                    echo $html->link($image, '/entry/' . $item['Entry']['id'], array(), false, false);
                } else {
                    echo $content;
                }
            ?>
        </span>
	<?php } else if(!$permalink && $item['Entry']['account_id'] == 0 && $item['Entry']['service_type_id'] == 3) { ?>
		<span>
			<br />
        	<?php echo $item['Entry']['content']; ?>
		</span>
	<?php } ?>
    <span>
        <?php echo sprintf(__('%d Favorites, %d Comments', true), count($item['FavoritedBy']), count($item['Comment'])); ?>
    </span>
    </p>
</li>