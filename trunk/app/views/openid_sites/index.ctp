<form method="post" action="<?php echo $this->here; ?>">
	<table class="listing">
		<tr>
			<th>Allowed</th>
			<th>Url</th>
			<th>Email</th>
			<th>Fullname</th>
			<th>Gender</th>
		</tr>
		<?php foreach ($openidSites as $openidSite): ?>
			<tr>
				<td><?php echo $form->checkbox('OpenidSite.'.$openidSite['OpenidSite']['id'], array('checked' => $openidSite['OpenidSite']['allowed'] ? true : false)); ?></td>
				<td><?php echo $openidSite['OpenidSite']['url']; ?></td>
				<td><?php echo $openidSite['OpenidSite']['email']; ?></td>
				<td><?php echo $openidSite['OpenidSite']['fullname']; ?></td>
				<td><?php echo $openidSite['OpenidSite']['gender']; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<input class="submitbutton" type="submit" name="Submit" value="Save changes" />
	</p>
</form>