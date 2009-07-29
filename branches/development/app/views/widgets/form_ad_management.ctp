<div class="widget form-network-settings">
    <h2><?php __('AdSpots and assigned Ads'); ?></h2>
    <?php if(!empty($adspots)) {
        echo $form->create(array('url' => '/admins/ads/assign/'));
        foreach($adspots as $key => $data) {
            echo $data['name'] . ' (' . $data['size'] . ') <em>' . $data['info'] . '</em><br />';
            $value = isset($assignments[$key]) ? $assignments[$key]['ad_id'] : 0;
            echo $form->input('Assignment.' . $key . '.ad_id', array('label' => __('Ad', true), 'type' => 'select', 'options' => $ad_list, 'value' => $value));
        }
        echo $form->submit(__('Save', true));
        echo $form->end(null);
    } else {
        echo __('No AdSpots available for current theme');
    } ?>
    <hr>    
    <h2><?php __('Your Ads'); ?></h2>
    <?php if(!empty($ads)) {
        foreach($ads as $item) {
            echo $form->create(array('url' => '/admins/ads/edit/'));
            echo $form->input('Ad.id', array('value' => $item['Ad']['id'], 'type' =>'hidden'));
            echo $form->input('Ad.name', array('value' => $item['Ad']['name'], 'label' => __('Name', true)));
            echo $form->input('Ad.width', array('value' => $item['Ad']['width'], 'label' => __('Width', true)));
            echo $form->input('Ad.height', array('value' => $item['Ad']['height'], 'label' => __('Height', true)));
            echo $form->input('Ad.content', array('value' => $item['Ad']['content'], 'label' => false));
            echo $form->input('Ad.allow_php', array('value' => $item['Ad']['allow_php'], 'type' => 'checkbox', 'label' => __('Allow PHP Code in Ad', true)));
            echo $form->submit(__('Save', true));
            echo $form->end(null);
        } 
    } else {
            __('No Ads available yet');
    } ?>
    <h2><?php __('Create new Ad'); ?></h2>
    <?php
        echo $form->create(array('url' => '/admins/ads/add/'));
        echo $form->input('Ad.name', array('label' => __('Name', true)));
        echo $form->input('Ad.width', array('label' => __('Width', true)));
        echo $form->input('Ad.height', array('label' => __('Height', true)));
        echo $form->input('Ad.content', array('label' => false));
        echo $form->input('Ad.allow_php', array('type' => 'checkbox', 'label' => __('Allow PHP Code in Ad', true)));
        echo $form->submit(__('Create', true));
        echo $form->end(null);
    ?>
</div>