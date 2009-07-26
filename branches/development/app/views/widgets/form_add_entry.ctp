<div class="widget form-add-entry">
    <?php if(Context::isLoggedIn() && 
             (!Context::groupId() ||
              (Context::groupId() && Context::isSubscribed()))) { ?>
        <h2><?php __('Add something new'); ?></h2>
        <?php 

        if(Context::groupId()) {
            $entry_add_modus = Context::entryGroupAddModus();
        } else {
            $entry_add_modus = Context::entryAddModus();
        }

        $links = array();
        foreach($filters as $key => $value) {
            if($key == $entry_add_modus) {
                $links[] = $value;
            } else {
                $url = '/entry/add/modus:' . $key;
                if(Context::groupId()) {
                    $url .= '/is_group:true';
                }
                $links[] = $html->link($value, $url);
            }
        }

        echo join(' - ', $links) . '<br />';

        echo $form->create(array('url' => '/entry/add/'));
        echo $noserub->fnSecurityTokenInput();
        echo $form->input('Entry.service_type', array('value' => $entry_add_modus, 'type' => 'hidden'));
        echo $form->input('Entry.group_id', array('value' => Context::groupId(), 'type' => 'hidden'));
    
        switch($entry_add_modus) {
            case 'micropublish':
                echo $form->input('Entry.text', array('type' => 'textarea', 'label' => false));
                break;
        
            case 'link':
                echo $form->input('Entry.description', array('label' => __('Description', true)));
                echo $form->input('Entry.url', array('label' => __('URL', true)));
                break;
        
            case 'text':
                echo $form->input('Entry.title', array('label' => __('Title', true)));
                echo $form->input('Entry.text', array('type' => 'textarea', 'label' => false));
                break;
            
            case 'photo':
                echo $this->element('webcam_snapshot');
                echo '<p />';
                break;
        }

		if ($entry_add_modus != 'photo') {
        	echo $form->end(array('label' => __('Send', true))); 
        }
    } ?> 
</div>