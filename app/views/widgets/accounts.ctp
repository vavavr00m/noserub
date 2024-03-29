<?php $services = Configure::read('services.list'); ?>
<div class="widget widget-accounts">
    <h2><?php __('On the web'); ?></h2>
    <?php if($accounts) { ?>
        <ul class="block-links">
            <?php foreach($accounts as $account) { ?>
                <li>
                    <?php
                        if($account['Account']['service'] == 'RSS-Feed') {
                            $label = $account['Account']['title'];
                        } else {
                            $label = $services[$account['Account']['service']];
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
</div>