<div class="widget widget-groups-overview">
    <?php if($groups) {
        foreach($groups as $group) {
            $group = $group['Group'];
            echo '<h2>' . $html->link($group['name'], '/groups/view/' . $group['slug']) . '</h2>';
            echo $noserub->groupOverview(array(
                'group_id' => $group['id'],
                'num' => 3
            ));
        }
        # echo $this->element('groups/list');
    } else { ?>
        <p><?php
            if(Context::read('identity.id')) {
                __('This user currently is not subscribed to any group.');
            } else {
                __('There are currently no groups available.');
            }
        ?></p>
    <?php } ?>
    <?php if(!Context::read('identity.id')) {
        echo $noserub->link('/groups/add/'); 
    } ?>
</div>