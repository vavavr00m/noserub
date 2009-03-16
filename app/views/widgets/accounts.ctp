<?php __('Accounts'); ?>
<?php if($accounts) { ?>
    <ul>
        <?php foreach($accounts as $account) { ?>
            <li>
                <?php
                    if($account['Account']['service_id'] == 8) {
                        $label = $account['Account']['title'];
                    } else {
                        $label = $account['Service']['name'];
                    }
                    echo $html->link($label, $account['Account']['account_url'], array('rel' => 'me')); 
                ?>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p><?php
        __('This user currently has not added any account.');
    ?></p>
<?php } ?>