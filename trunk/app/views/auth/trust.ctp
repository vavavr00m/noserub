<?php 
    $openidSiteData = isset($openidSite) ? $openidSite : false;
?>
<p>
    <?php echo sprintf(__('A site identifying as <strong>%s</strong> has asked us for confirmation that <strong>%s</strong> is your identity URL.', true), $trustRoot, $identity); ?>
</p>
<form method="post" action="<?php echo $this->here; ?>">
	<?php if (!empty($required) || !empty($optional)): ?>
		<hr />
		<p>
		<?php echo sprintf(__('%s also asked for additional information.', true), $trustRoot); ?>
		<?php if (isset($policyUrl)): ?>
		    <?php echo sprintf(__('It asked that you view %s about the policy on the data collected.', true), $html->link(__('this page', true), $policyUrl)); ?>
		<?php else: ?>
			<?php __('It did not provide a link to the policy on data it collects.'); ?>
		<?php endif; ?>
		</p>
		<table>
			<?php if (!empty($required)): ?>
				<?php foreach ($required as $key => $value) : ?>
					<tr>
						<td><?php echo $nicesreg->checkboxForSupportedFields($key, $openidSiteData); ?></td>
						<td><strong><?php echo $nicesreg->key($key); ?></strong></td>
						<td><?php echo $nicesreg->value($key, $value); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if (!empty($optional)): ?>
				<?php foreach ($optional as $key => $value) : ?>
					<tr>
						<td><?php echo $nicesreg->checkboxForSupportedFields($key, $openidSiteData); ?></td>
						<td><?php echo $nicesreg->key($key); ?></td>
						<td><?php echo $nicesreg->value($key, $value); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
	<?php endif; ?>
	<p>
	    <input class="submitbutton" type="submit" name="AllowForever" value="<?php __('Allow Forever'); ?>" />
	    <input class="submitbutton" type="submit" name="AllowOnce" value="<?php __('Allow Once'); ?>" />
	    <input class="submitbutton" type="submit" name="Deny" value="<?php __('Deny'); ?>" />
    </p>
</form>