<p>
A site identifying as <?php echo $trustRoot; ?> has asked us for confirmation that <?php echo $identity; ?> is your identity URL.
</p>
<?php if (!empty($required)): ?>
	<p>
		<table>
			<tr>
				<th>Required:</th>
			</tr>
			<?php foreach ($required as $key => $value) : ?>
				<tr>
					<td><?php echo $key; ?></td>
					<td><?php echo $value; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</p>
<?php endif; ?>
<?php if (!empty($optional)): ?>
	<p>
		<table>
			<tr>
				<th>Optional:</th>
			</tr>
			<?php foreach ($optional as $key => $value) : ?>
				<tr>
					<td><?php echo $key; ?></td>
					<td><?php echo $value; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</p>
<?php endif; ?>
<form method="post" action="<?php echo $this->here; ?>">
    <input class="submitbutton" type="submit" name="Allow" value="Allow" />
    <input class="submitbutton" type="submit" name="Deny" value="Deny" />
</form>