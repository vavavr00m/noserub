<h2><?php __('Add something new'); ?></h2>
<?php 

$entry_add_modus = Context::entryAddModus();

$links = array();
foreach($filters as $key => $value) {
    if($key == $entry_add_modus) {
        $links[] = $value;
    } else {
        $links[] = $html->link($value, '/entry/add/modus:' . $key);
    }
}

echo join(' - ', $links) . '<br />';

echo $form->create(array('url' => '/entry/add/'));
echo $noserub->fnSecurityTokenInput();
echo $form->input('Entry.service_type', array('value' => $entry_add_modus, 'type' => 'hidden'));

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
}

echo $form->end(array('label' => __('Send', true))); 
?>