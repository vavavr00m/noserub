<?php $no_error = true; ?>

<div id="bd-main">
	<div id="bd-main-hd">
		<h1><?php __('System Update'); ?></h1>
		<p id="message" class="info">
			<?php __('Information about the current state of your NoseRub instance. Please
			check back after each update you install, because new constants may have
			been introduced and/or changes in the database structure been made.'); ?>
		</p>
	</div>
	<div id="bd-main-bd">
		<h2><?php __('Extensions'); ?></h2>
		<?php if(empty($extensions)) { ?>
			<p class="message success">
				<?php __('Status'); ?>: <strong><?php __('OK'); ?></strong>
			</p>
		<?php } else { ?>
			<?php $no_error = false; ?>
			<p class="message alert">
				<?php __('Some extensions need to be fixed!'); ?>
			</p>
			<ul>
				<?php foreach($extensions as $extension => $reason) { ?>
					<li><?php echo '<strong>' . $extension . '</strong>: ' . $reason; ?></li>
				<?php } ?>
			</ul>
		<?php } ?>
		<h2><?php __('Random Number Generator'); ?></h2>
		<?php if($random_number_generator === true) { ?>
			<p class="message success">
				<?php __('Status'); ?>: <strong><?php __('OK'); ?></strong>
			</p>
		<?php } else { ?>
			<?php $no_error = false; ?>
			<p class="message alert">
				<?php echo $random_number_generator; ?>
			</p>
		<?php } ?>
		<h2><?php __('Directories'); ?></h2>
		<?php if(empty($directories)) { ?>
			<p class="message success">
				<?php __('Status'); ?>: <strong><?php __('OK'); ?></strong>
			</p>
		<?php } else { ?>
			<?php $no_error = false; ?>
			<p class="message alert">
				<?php __('Some directories are not writeable!'); ?>
			</p>
			<ul> 
				<?php foreach($directories as $directory) { ?>
					<li><?php echo '<strong>' . $directory . '</strong>: ' . __('not writeable', true); ?></li>
				<?php } ?>
			</ul>
		<?php } ?>   
		<h2><?php __('Settings'); ?></h2>
		<?php if(empty($constants)) { ?>
			<p class="message success">
				<?php __('Status'); ?>: <strong><?php __('OK'); ?></strong>
			</p>
		<?php } else { ?>
			<?php $no_error = false; ?>
			<p class="message alert">
				<?php __('Some constants need to be fixed!'); ?>
			</p>
			<ul> 
				<?php foreach($constants as $constant => $message) { ?>
					<li><?php echo '<strong>' . $constant . '</strong>: ' . $message; ?></li>
				<?php } ?>
			</ul>
		<?php } ?>
		<?php if(isset($database_status)) { ?>
			<h2><?php __('Database'); ?></h2>
			<?php if($database_status == 1) { ?>
				<p class="message success">
					<?php __('Configuration'); ?>: <strong><?php __('OK'); ?></strong>
				</p>
				<h2><?php __('Actual version #'); echo ' ' . $current_migration; ?></h2>
				<?php if(isset($migrations)) { ?>
					<?php __('Updating to #');echo ' ' . $most_recent_migration; ?>:<br />
					<ul>
						<?php foreach($migrations['sql'] as $idx => $migration) { ?>
							<li><?php echo $migration['name']; ?></li>
							<?php if(isset($migrations['php'][$idx]['name'])) { ?>
								<li><?php echo $migrations['php'][$idx]['name']; ?></li>
							<?php } ?>
						<?php } ?>
					</ul>
					<p class="message success">
						<?php __('Database up-to-date!'); ?>
					</p>
				<?php } else { ?>
					<p class="message success">
						<?php __('Status'); ?>: <strong><?php __('OK'); ?></strong>
					</p>
				<?php } ?>
			<?php } else { ?>
				<?php $no_error = false; ?>
				<p class="message alert">
					<?php __('Configuration'); ?>: <strong><?php
						switch($database_status) {
							case -1:
								echo __('database.php in /app/config/ not found!', true); break;
							case  0:
								echo __('Cannot connect to database!', true); break;
						}
					?></strong>
				</p>
			<?php } ?>
		<?php } else { ?>
			<?php $no_error = false; ?>
			<h2><?php __('Errors!'); ?></h2>
			<p>
				<?php __('Please correct the errors above and then return to this URL, to finish the update.'); ?>
			</p>
		<?php } ?>
		<?php if($no_error) { ?>
			<h2><?php __('Talk back to NoseRub.com'); ?></h2>
			<p class="message info">
				<?php __('If you want to, you can just click on the button and NoseRub.com will receive
				knowledge about your installation here. The decision wether to click that button
				or not is solely up to you.'); ?>
			</p>
			<form id="talkbackForm" method="POST" action="http://noserub.com/talkback">
				<fieldset>
					<legend>
						<?php __("We don't send any other data beside what you can verify here, once you click that button."); ?>
					</legend>
					<div class="input text">
						<input type="hidden" name="talkback[url]" value="<?php echo $network_url; ?>"><strong><?php __('URL'); ?>:</strong> <?php echo $network_url; ?>
					</div>
					<div class="input text">
						<input type="hidden" name="talkback[migrations]" value="<?php echo $most_recent_migration; ?>"><strong><?php __('DB Version'); ?>:</strong> <?php echo $most_recent_migration; ?>
					</div>
					<div class="input checkbox">
						<input type="checkbox" name="talkback[allow_add]">&nbsp;<strong><?php __('Add this site to http://noserub.com/'); ?></strong>
					</div>
				</fieldset>
				<fieldset>
					<input type="submit" value="<?php __('Send to NoseRub.com'); ?>"/>
				</fieldset>
			</form>
		<?php } ?>
	</div>
</div>
