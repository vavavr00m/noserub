<?php 
$openidSiteData = isset($openidSite) ? $openidSite : false;
?>
<p>
A site identifying as <strong><?php echo $trustRoot; ?></strong> has asked us for confirmation that 
<strong><?php echo $identity; ?></strong> is your identity URL.
</p>
<form method="post" action="<?php echo $this->here; ?>">
	<?php if (!empty($required) || !empty($optional)): ?>
		<hr />
		<p>
		<?php echo $trustRoot; ?> also asked for additional information. 
		<?php if (isset($policyUrl)): ?>
			It asked that you view <?php echo $html->link('this page', $policyUrl); ?> about 
			the policy on the data collected.
		<?php else: ?>
			It did not provide a link to the policy on data it collects.
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
	    <input class="submitbutton" type="submit" name="AllowForever" value="Allow Forever" />
	    <input class="submitbutton" type="submit" name="AllowOnce" value="Allow Once" />
	    <input class="submitbutton" type="submit" name="Deny" value="Deny" />
    </p>
</form>