<table>
	<tr>
		<th>Url</th>
		<th>Allowed</th>
		<th>Email</th>
		<th>Fullname</th>
		<th>Gender</th>
	</tr>
	<?php foreach ($openidSites as $openidSite): ?>
		<tr>
			<td><?php echo $openidSite['OpenidSite']['url']; ?></td>
			<td><?php echo $openidSite['OpenidSite']['allowed']; ?></td>
			<td><?php echo $openidSite['OpenidSite']['email']; ?></td>
			<td><?php echo $openidSite['OpenidSite']['fullname']; ?></td>
			<td><?php echo $openidSite['OpenidSite']['gender']; ?></td>
		</tr>
	<?php endforeach; ?>
</table>