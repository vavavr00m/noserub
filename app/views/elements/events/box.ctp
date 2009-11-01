<ul>
    <?php foreach($data as $item) { ?>
        <li><?php 
            $label = date('Y-m-d', strtotime($item['Event']['from_datetime'])) . ': ' . $item['Event']['name'];
            echo $html->link($label, '/events/view/' . $item['Event']['id'] . '/' . $item['Event']['slug']);
        ?></li>
    <?php } ?>
</ul>