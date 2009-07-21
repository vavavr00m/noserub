<?php 
    if(!isset($with_date)) {
        $with_date = true;
    }
    if(!isset($permalink)) {
        $permalink = true;
    }
?>
<table>
    <tr>
        <td><?php echo $this->element('identities/mini_profile'); ?></td>
        <td><?php switch($data['Entry']['service_type']) {
                case 1: // photo
                    echo $this->element('entries/photo'); break;
                case 6: // video
                    echo $this->element('entries/video'); break;
                default: echo $data['Entry']['content'];
        } ?></td>
    </tr>
</table>
<?php
    if($data['Entry']['account_id'] > 0) {
    	echo $html->link(__('External permalink', true), $data['Entry']['url']);
    }
    if(Context::isLoggedIn() && $data['Entry']['service_type'] != 0) {
        $label = isset($already_marked) ? __('Unmark Entry as favorite', true) : __('Mark Entry as favorite', true);
        echo '<br />' . $html->link($label, '/entry/mark/' . $data['Entry']['id'] .  '/_t:' . $noserub->fnSecurityToken());
    }
?>