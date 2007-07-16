<h1>Verification of E-Mail address</h1>
<?php if($verify_ok) { ?>
    <p>
        Congratulations. You're now ready to <a href="/login/">login</a> to NoseRub.
    </p>
<?php } else { ?>
    <p>
        Sorry. The verification link did not work. Maybe you're already done and just need to proceed to <a href="/login/">login</a>?
    </p>
<?php } ?>