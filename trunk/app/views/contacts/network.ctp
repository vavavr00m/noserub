<h1>Network</h1>
<?php if($filter == 'photo') { ?>
    <?php foreach($data as $item) { ?>
        <div style="float:left;padding:10px;">
            <?php echo $item['content']; ?><br />
            <a href="<?php echo $item['link']; ?>"><?php echo $item['username']; ?></a>
        </div>
    <?php } ?>
<? } else { ?>
<ul>
    <?php foreach($data as $item) { ?>
        <li><?php 
                echo $item['datetime'] . ': ' . $item['username'] . ' => ';
                echo '<a href="'.$item['link'].'">[+]</a> ';
            echo $item['content']; 
        ?></li>
    <?php } ?>
</ul>
<?php } ?>