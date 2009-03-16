<?php __('Communications'); ?>
<?php if($communications) { ?>
    <ul>
        <?php foreach($communications as $communication) { ?>
            <li>
                <a href="<?php echo $communication['Account']['account_url']; ?>" rel="me"><?php echo $communication['Service']['name'] ?></a>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p><?php
        __('This user currently has not added any communication information.');
    ?></p>
<?php } ?>