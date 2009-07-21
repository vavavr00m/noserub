<?php $services = Configure::read('services.list'); ?>
<div class="widget widget-communications">
    <h2><?php __('Communications'); ?></h2>
    <?php if($communications) { ?>
        <ul class="block-contact">
            <?php foreach($communications as $communication) { ?>
                <li>
                    <a href="<?php echo $communication['Account']['account_url']; ?>" rel="me"><?php echo $services[$communication['Account']['service']]; ?></a>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p><?php
            __('This user currently has not added any communication information.');
        ?></p>
    <?php } ?>
</div>