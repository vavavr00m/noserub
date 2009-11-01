<div id="bd-main">
	<div id="bd-main-hd">
		<?php if($verify_ok): ?>
			<h1><?php __("Congratulations"); ?></h1>
		<?php else: ?>
			<h1><?php __("Sorry"); ?></h1>
		<?php endif; ?>
	</div>
	<div id="bd-main-bd">
		<?php if($verify_ok): ?>
			<p>
				<?php echo sprintf(__("You're now ready to %s to %s.", true), $html->link(__('login', true), '/pages/login/'), Context::read('network.name')); ?>
			</p>
		<?php else: ?>
			<p>
				<?php echo sprintf(__("The verification link did not work. Maybe you're already done and just need to proceed to %s?", true), $html->link(__('login', true), '/pages/login/')); ?>
			</p>
		<?php endif; ?>
	</div>
</div>
