<?php 
    $url = Router::url('/' . $session_identity['local_username']);
	$flashmessage->render(); 
?>
<p class="infotext">
	Currently, our API doesn't support OAuth, but it's coming soon.
</p>

<h3>Your applications</h3>
<p class="infotext">
	<?php echo $html->link('Register new application', $url.'/settings/oauth/add', array('class' => 'addmore')); ?>
</p>
<?php if (empty($consumers)): ?>
	<p class="infotext">
		You did not register any applications yet.
	</p>
<?php else: ?>
	<table class="listing">
		<thead>
			<tr>
				<th>Application name</th>
				<th>Application key</th>
				<th>Secret</th>
				<th></th>
			</tr>
		</thead>
		<?php foreach($consumers as $consumer): ?>
			<tr>
				<td><?php echo $consumer['Consumer']['application_name']; ?></td>
				<td><?php echo $consumer['Consumer']['consumer_key']; ?></td>
				<td><?php echo $consumer['Consumer']['consumer_secret']; ?></td>
				<td>
					<ul>
                   		<li class="delete icon"><a href="<?php echo  $url . '/settings/oauth/'.  $consumer['Consumer']['id'] . '/delete/' . $security_token . '/'; ?>">Delete</a></li>
                   		<li class="edit icon"><a href="<?php echo $url . '/settings/oauth/'.  $consumer['Consumer']['id'] . '/edit/'; ?>">Edit</a></li>
                   	</ul>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>