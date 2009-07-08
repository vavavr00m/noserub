<?php 
    if(!isset($with_date)) {
        $with_date = true;
    }
    if(!isset($permalink)) {
        $permalink = true;
    }
?>
<li class="<?php echo $item['ServiceType']['token'] == 'photo' ? 'photos' : $item['ServiceType']['token']; ?> icon">
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
            $intro = str_replace('@user@', '<a href="http://'.$item['Identity']['username'].'">'.$username.'</a>', $item['ServiceType']['intro']);
            if($item['Entry']['service_type_id'] != 5 && $item['Entry']['url']) {
                $intro = str_replace('@item@', '»<a class="external" href="'.$item['Entry']['url'].'">'.$item['Entry']['title'].'</a>«', $intro);
            } else {
                $intro = str_replace('@item@', '»'.$item['Entry']['title'].'«', $intro);
            }
            echo $intro; 
        ?>
    </span>
    <?php switch($item['Entry']['service_type_id']) {
        case 1: // photo
            echo $this->element('entries/photo', array('data' => $item)); break;
        case 6: // video
            echo $this->element('entries/video', array('data' => $item)); break;
        default: echo $item['Entry']['content'];
    } ?>
    <span>
        <?php echo $this->renderElement('entries/favorited_by', array('data' => $item)); ?>
    </span>
    <span>
        <?php echo $this->renderElement('comments/view', array('data' => $item)); ?>
    </span>
    </p>
</li>