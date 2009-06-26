<?php if(!empty($data)) { ?>
    <table>
        <tr>
            <th><?php __('Title'); ?></th>
            <th><?php __('Comments'); ?></th>
            <th><?php __('Created'); ?></th>
            <th><?php __('Last Update'); ?></th>
        </tr>
        <?php foreach($data as $item) { ?>
            <tr>
                <td><?php 
                    echo $html->link(
                        $item['Entry']['title'], 
                        '/groups/entry/' . Context::groupSlug() . '/' . $item['Entry']['id']
                    ); 
                ?></td>
                <td>0</td>
                <td><?php echo $item['Entry']['published_on']; ?></td>
                <td>-</td>
            </tr>
        <?php } ?>
    </table>
<?php } else {
    __('There is currently no entry in this group.');
} ?>