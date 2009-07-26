<div class="widget widget-location-info">
	<?php if(!empty($location_info)): ?>
	    <h2><?php echo $location_info['Location']['name']; ?></h2>
        <ul>
            <li><?php echo __('Address', true) . ': ' . $location_info['Location']['address']; ?></li>
            <li><?php echo __('Type', true) . ': ' . $types[$location_info['Location']['type']]; ?></li>
            <?php if($location_info['Location']['url']) { ?>
                <li><?php echo __('Link to website', true) . ': ' . $html->link($location_info['Location']['url']); ?></li>
            <?php } ?>
            <li><?php echo __('Created by', true) . ': ' . $html->link($location_info['Identity']['username'], 'http://' . $location_info['Identity']['username']); ?></li>
        </ul>
        <p>
            <?php echo $location_info['Location']['description']; ?>
        </p>
	<?php endif; ?>
</div>
