<?php 
    if(!isset($with_date)) {
        $with_date = true;
    }
    if(!isset($permalink)) {
        $permalink = true;
    }
?>
<li class="<?php echo $item['ServiceType']['token'] == 'photo' ? 'photos' : $item['ServiceType']['token']; ?> icon">
    <span class="date">
        <?php if($permalink) { ?>
            <a href="/entry/<?php echo $item['Entry']['id']; ?>/">
        <?php } ?>
        <?php if(!$with_date) {
            echo $nicetime->show($item['Entry']['published_on']); 
        } else {
            echo date('H:s', strtotime($item['Entry']['published_on'])); 
        } ?>
        <?php if($permalink) { ?>
            </a>
        <?php } ?>
    </span>
    <span>
        <?php
            $splitted = split('/', $item['Identity']['username']);
            $splitted2 = split('@', $splitted[count($splitted)-1]);
            $username = $splitted2[0];
            $intro = str_replace('@user@', '<a href="http://'.$item['Identity']['username'].'">'.$username.'</a>', $item['ServiceType']['intro']);
            if($item['Entry']['url']) {
                $intro = str_replace('@item@', '»<a class="external" href="'.$item['Entry']['url'].'">'.$item['Entry']['title'].'</a>«', $intro);
            } else {
                $intro = str_replace('@item@', '»'.$item['Entry']['title'].'«', $intro);
            }
            echo $intro; 
        ?>
    </span>
    <span>
        <br />
        <?php echo $this->renderElement('entries/favorited_by', array('data' => $item)); ?>
    </span>
</li>