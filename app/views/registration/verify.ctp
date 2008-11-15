<?php if($verify_ok) { ?>
    <p>
        <?php sprintf(__("Congratulations. You're now ready to %s to %s.", true), $html->link(__('login', true), '/pages/login/'), Configure::read('Noserub.app_name')); ?>
    </p>
<?php } else { ?>
    <p>
        <?php sprintf(__("Sorry. The verification link did not work. Maybe you're already done and just need to proceed to %s?", true), $html->link(__('login', true), '/pages/login/')); ?>
    </p>
<?php } ?>