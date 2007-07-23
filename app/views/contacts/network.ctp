<h1>Network</h1>
<ul>
    <?php foreach($data as $item) { ?>
        <li><?php 
                echo $item['datetime'] . ': ' . $item['username'] . ' => ';
                echo '<a href="'.$item['link'].'">[+]</a> ';
            echo $item['content']; 
        ?></li>
    <?php } ?>
</ul>