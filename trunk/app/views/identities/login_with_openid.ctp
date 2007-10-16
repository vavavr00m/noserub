<?php if(isset($form_error) && !empty($form_error)) { 
    echo '<p>'. $form_error . '</p>';
} ?>
<?php echo $this->element('identities/openid_login_form'); ?>