<h2><?php __('Networks'); ?></h2>
<?php if($networks) { ?>
    <ul class="block-groups">
        <?php foreach($networks as $network) { ?>
            <li>
                <?php if($network['id'] == Context::read('network.id')) {
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