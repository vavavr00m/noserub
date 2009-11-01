<div class="widget widget-event-info">
	<?php if(!empty($event_info)): ?>
	    <h2><?php echo $event_info['Event']['name']; ?></h2>
        <ul>
            <li><?php 
                echo __('Location', true) . ': ';
                if($event_info['Location']['id']) {
                    echo $html->link($event_info['Location']['name'], '/locations/view/' . $event_info['Location']['id'] . '/' . $event_info['Location']['slug']);
                } else {
                    echo __('Not yet specified', true);
                }
            ?></li>
            <li><?php echo __('Type', true) . ': ' . $types[$event_info['Event']['type']]; ?></li>
            <?php if($event_info['Event']['url']) { ?>
                <li><?php echo __('Link to website', true) . ': ' . $html->link($event_info['Event']['url']); ?></li>
            <?php } ?>
            <li><?php echo __('Created by', true) . ': ' . $html->link($event_info['Identity']['username'], 'http://' . $event_info['Identity']['username']); ?></li>
        </ul>
        <p>
            <?php echo $event_info['Event']['description']; ?>
        </p>
	<?php endif; ?>
</div>
