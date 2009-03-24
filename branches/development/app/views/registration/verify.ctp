<div id="inhalt">
    <?php if($verify_ok) { ?>
        <p>
            <?php echo sprintf(__("Congratulations. You're now ready to %s to %s.", true), $html->link(__('login', true), '/pages/login/'), Configure::read('context.network.name')); ?>
        </p>
    <?php } else { ?>
        <p>
            <?php echo sprintf(__("Sorry. The verification link did not work. Maybe you're already done and just need to proceed to %s?", true), $html->link(__('login', true), '/pages/login/')); ?>
        </p>
    <?php } ?>
</div>