<h1>Verification of E-Mail address</h1>
<?php if($verify_ok) { ?>
    <p>
        Congratulations. You're now ready to <?php echo $html->link('login', '/login/'); ?> to NoseRub.
    </p>
<?php } else { ?>
    <p>
        Sorry. The verification link did not work. Maybe you're already done and just need to proceed to <?php echo $html->link('login', '/login/'); ?>?
    </p>
<?php } ?>