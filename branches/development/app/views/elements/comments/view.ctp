<?php if(isset($data['Comment']) && count($data['Comment']) > 0 ) { ?>
    <table>
        <?php foreach($data['Comment'] as $idx => $item) { ?>
            <tr>
                <td><?php echo $this->element('identities/mini_profile', array('data' => $item['Identity'])); ?></td>
                <td><?php echo $item['published_on'] . '<br />' . nl2br($item['content']); ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>