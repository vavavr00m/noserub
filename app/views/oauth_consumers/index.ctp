<?php 
    $url = Router::url('/' . $session_identity['local_username']);
	$flashmessage->render(); 
?>
<h3><?php __('OAuth parameters'); ?></h3>
<ul class="infotext">
	<li><?php __('Request Token URL'); ?>: <strong><?php echo Router::url('/pages/oauth/request_token', true); ?></strong></li>
	<li><?php __('User Authorization URL'); ?>: <strong><?php echo Router::url('/pages/oauth/authorize', true); ?></strong></li>
	<li><?php __('Access Token URL'); ?>: <strong><?php echo Router::url('/pages/oauth/access_token', true); ?></strong></li>
	<li><?php __('Signature method'); ?>: HMAC-SHA1</li>
</ul>

<h3><?php __('Your applications'); ?></h3>
<p class="infotext">
	<?php echo $html->link(__('Register new application', true), $url.'/settings/oauth/add', array('class' => 'addmore')); ?>
</p>
<?php if (empty($consumers)): ?>
	<p class="infotext">
		<?php __('You did not register any applications yet.'); ?>
	</p>
<?php else: ?>
	<table class="listing">
		<thead>
			<tr>
				<th><?php __('Application name'); ?></th>
				<th><?php __('Callback url'); ?></th>
				<th><?php __('Consumer key'); ?></th>
				<th><?php __('Consumer secret'); ?></th>
				<th></th>
			</tr>
		</thead>
		<?php foreach($consumers as $consumer): ?>
			<tr>
				<td><?php echo $consumer['Consumer']['application_name']; ?></td>
				<td><?php echo $consumer['Consumer']['callback_url']; ?></td>
				<td><?php echo $consumer['Consumer']['consumer_key']; ?></td>
				<td><?php echo $consumer['Consumer']['consumer_secret']; ?></td>
				<td>
					<ul>
                   		<li class="delete icon"><a href="<?php echo  $url . '/settings/oauth/'.  $consumer['Consumer']['id'] . '/delete/' . $security_token . '/'; ?>"><?php __('Delete'); ?></a></li>
                   		<li class="edit icon"><a href="<?php echo $url . '/settings/oauth/'.  $consumer['Consumer']['id'] . '/edit/'; ?>"><?php __('Edit'); ?></a></li>
                   	</ul>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>