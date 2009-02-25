<?php __('Networks'); ?>
<?php if($networks) { ?>
    <ul>
        <?php foreach($networks as $network) { ?>
            <li>
                <?php if($network['id'] == $context['network_id']) {
                    echo $network['name'];
                } else {
                    echo $html->link($network['name'], $network['url']); 
                } ?>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p><?php
        __('This user currently is not subscribed to any network.');
    ?></p>
<?php } ?>