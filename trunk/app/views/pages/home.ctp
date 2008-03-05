<h2>The latest NoseRub accounts</h2>
<ul>
	<?php foreach($identities as $identity) { ?>
		<li><a href="http://<?php echo $identity['Identity']['username']; ?>"><?php echo $identity['Identity']['local_username']; ?></a></li>
	<?php } ?>
</ul>
<p>
    If you want your account to be displayed here, too, you need to change the 
    pricavy settings, so we will display your account updates on this page.
</p>
<?php if(!$session->check('Identity.id')) { ?>
    <p>
        Right now, you should either <?php echo $html->link('register', '/pages/register/'); ?> a new account or <?php echo $html->link('login', '/pages/login/'); ?> with your already existing username and password.
    </p>
<?php } ?>
