<?php 
    if(!isset($with_date)) {
        $with_date = true;
    }
    if(!isset($permalink)) {
        $permalink = true;
    }
    
    $service_types = Configure::read('service_types');
?>
<li class="<?php echo $service_types[$item['Entry']['service_type']]['token']; ?>">
	<span class="icon"></span>
    <span class="date">
        <?php
            if(!$with_date) {
                $label = $nicetime->show($item['Entry']['published_on']); 
            } else {
                $label = date('H:s', strtotime($item['Entry']['published_on'])); 
            }
            echo $label;
        ?>
    </span>
	<span class="comments">
	    <?php echo $html->link('', '/entry/' . $item['Entry']['id']); ?>
		<?php 
			if(count($item['Comment'])) {
				echo sprintf('&nbsp;(%d)', count($item['Comment']));
			}
		?>
	</span>
    <span class="favorites">
        <?php if($item['Entry']['service_type'] != 0) {
            echo $noserub->link('/entry/toggle/mark/', $item['Entry']['id']); 
        } ?>
		<?php 
			if(count($item['FavoritedBy'])) {
				echo sprintf('&nbsp;(%d)', count($item['FavoritedBy']));
			}
		?>
    </span>
    <p>
    <?php
        $splitted = split('/', $item['Identity']['username']);
        $splitted2 = split('@', $splitted[count($splitted)-1]);
        $username = $splitted2[0];
        $intro = str_replace('@user@', '<a class="user" href="http://'.$item['Identity']['username'].'">'.$username.'</a>', $service_types[$item['Entry']['service_type']]['intro']);
        if($item['Entry']['service_type'] != 5 && $item['Entry']['url']) {
            $intro = str_replace('@item@', '<a class="external" href="'.$item['Entry']['url'].'">'.$item['Entry']['title'].'</a>', $intro);
        } else {
            $intro = str_replace('@item@', $item['Entry']['title'], $intro);
        }
        echo $intro; 
    ?>
    </p>
	<?php if($item['Entry']['service_type'] == 1 || $item['Entry']['service_type'] == 6) { ?>
        <p>
            <?php
                $content = $item['Entry']['content'];
                $raw_content = @unserialize(@base64_decode($item['Entry']['content']));
                if(!empty($raw_content['thumb'])) {
                    $image = $html->image($raw_content['thumb'], array('height' => 75, 'width' => 75));
                    echo $html->link($image, '/entry/' . $item['Entry']['id'], array('class' => 'photo'), false, false);
                } else {
                    echo $content;
                }
            ?>
        </p>
	<?php } else if(!$permalink && $item['Entry']['account_id'] == 0 && $item['Entry']['service_type_id'] == 3) { ?>
		<p>
        	<?php echo $item['Entry']['content']; ?>
		</p>
	<?php } ?>
</li>
