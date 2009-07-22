<div class="widget widget-messages">
    <h2><?php echo $folder; ?></h2>
    <?php if(!$data) {
        __('No messages here');
    } else { ?>
        <table>
            <tr>
                <th><?php if($folder == 'sent') {
                    __('To');
                } else {
                    __('From');
                } ?></th>
                <th><?php __('Subject'); ?></th>
                <th><?php if($folder == 'sent') {
                    __('Sent');
                } else {
                    __('Recieved');
                } ?></th>
            </tr>
            <?php foreach($data as $item) { ?>
                <tr>
                    <td><?php echo $item['Message']['to_from']; ?></td>
                    <td><?php 
                        $subject = $html->link($item['Message']['subject'], '/messages/view/' . $item['Message']['id']);
                        if(!$item['Message']['read']) {
                            $subject = '* ' . $subject;
                        }
                        echo $subject;
                    ?></td>
                    <td><?php echo $item['Message']['created']; ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</div>