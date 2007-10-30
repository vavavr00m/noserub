<?php if (!$session->check('Noserub.lastOpenIDRequest')): ?>
	<p class="infotext">
		<?php echo $html->link('Login', '/pages/login/withopenid'); ?> with your <img src="/images/openid_small.gif" alt="OpenID logo" /> OpenID.
	</p>
<?php endif; ?>
<?php if(isset($form_error) && !empty($form_error)) { 
    echo '<p>'. $form_error . '</p>';
} ?>
<form id="Identity/pages/login/Form" method="post" action="<?php echo $this->here; ?>">
    <fieldset>
        <?php echo $form->input('Identity.username'); ?>
        <?php echo $form->input('Identity.password', array('type' => 'password')); ?>
        <input class="submitbutton" type="submit" value="Login"/>
    </fieldset>
<?php echo $form->end(); ?>
