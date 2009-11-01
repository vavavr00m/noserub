<div class="widget widget-view-message">
    <?php if(!$data) { 
        __('There is no such message available.');
    } else {
        if($data['Message']['folder'] == 'sent') {
            $label_to_from = __('To', true);
            $label_created = __('Sent', true);
        } else {
            $label_to_from = __('From', true);
            $label_created = __('Recieved', true);
        }
        echo __('Subject', true) . ': ' . $data['Message']['subject'] . '<br />';
        echo $label_to_from . ': ' . $data['Message']['to_from'] . '<br />';
        echo $label_created . ': ' . $data['Message']['created'] . '<br />';
        echo __('Folder', true) . ': ' . $data['Message']['folder'] . '<br />';
        echo '<hr>';
        echo nl2br($data['Message']['text']);
        echo '<hr>';
        if($data['Message']['folder'] != 'sent') {
            echo $html->link(__('Reply', true), '/messages/reply/' . $data['Message']['id']);
        }
    } ?>
</div>