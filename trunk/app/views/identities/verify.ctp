<?php if($verify_ok) { ?>
    <p>
        Congratulations. You're now ready to <?php echo $html->link('login', '/pages/login/'); ?> to NoseRub.
    </p>
<?php } else { ?>
    <p>
        Sorry. The verification link did not work. Maybe you're already done and just need to proceed to <?php echo $html->link('login', '/pages/login/'); ?>?
    </p>
<?php } ?>