<?php $services = Configure::read('services.data'); ?>
<div id="bd-main" class="with-sidebar">
    <div id="bd-main-hd">
		<?php echo $noserub->widgetProfile(); ?>
	</div>
	<div id="bd-main-bd">
		<dl id="hcard-<?php echo $contact['WithIdentity']['local_username']; ?>" class="vcards contacts <?php echo $contact['WithIdentity']['local']==1 ? '' : 'externalcontact'; ?>">
			<dt>
				<a href="<?php echo 'http://' . $contact['WithIdentity']['username']; ?>">
					<img class="photo" src="<?php echo $contact_photo; ?>" width="80" height="80" alt="<?php echo $contact['WithIdentity']['single_username']; ?>'s Picture" />
				</a>
			</dt>                     
			<dt>
				<a class="url nickname" href="<?php echo 'http://' . $contact['WithIdentity']['username']; ?>"><?php echo $contact['WithIdentity']['single_username']; ?></a>
			</dt>
			<dd class="fn"><?php echo $contact['WithIdentity']['name']; ?></dd>
			
			<!-- send e-Mail -->
			<?php if($contact['WithIdentity']['local'] == 1 && $contact['WithIdentity']['allow_emails'] != 0) { ?>
				<dd class="sendmail">
					<img src="<?php echo Router::url('/images/icons/services/email.gif'); ?>" height="16" width="16" alt="e-Mail" class="sendmail_icon" /> <a href="http://<?php echo $contact['WithIdentity']['username']; ?>/messages/new/"><?php __('Send e-Mail'); ?></a>
				</dd>
			<?php } ?>
		</dl>	
		
		<br class="clear" />
		<?php if($accounts) { ?>
			<h2><?php __('Accounts'); ?></h2>
			<ul class="whoissidebar">
				<?php foreach($accounts as $item) { ?>
					<li>
						<?php if(!$item['Account']['service']) { ?>
							<?php echo $item['Account']['account_url']; ?>
						<?php } else { ?>
							<img src="<?php echo Router::url('/images/icons/services/') . $services[$item['Account']['service']]['icon']; ?>" height="16" width="16" alt="<?php echo $services[$item['Account']['service']]['name']; ?>" class="whoisicon" />
							<a rel="me" class="taggedlink" href="<?php echo $item['Account']['account_url']; ?>"><?php echo isset($item['Account']['title']) ? $item['Account']['title'] : $services[$item['Account']['service']]['name']; ?></a>
							<?php if($item['Account']['feed_url']) { ?>
								<a rel="me" class="taggedlink" href="<?php echo $item['Account']['feed_url']; ?>">
									<img src="<?php echo Router::url('/images/icons/services/rss.gif') ?>" height="16" width="16" alt="RSS-Feed" class="whoisicon" />
								</a>
							<?php } ?>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
		<a href="<?php echo Router::url('/contacts/') ?>"><?php __('Back to list of contacts'); ?></a>
	</div>
    <div id="bd-main-sidebar">
		<?php echo $noserub->widgetContacts(); ?>
	</div>
</div>