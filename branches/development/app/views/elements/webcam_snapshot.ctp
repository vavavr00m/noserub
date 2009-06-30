<h3><?php __('Snapshot from Webcam'); ?></h3>
<div id="webcam_snapshot">
	<p>
	    <?php __('You need the Flash PlugIn and JavaScript enabled to use this'); ?>
	</p>
	<p><a href="http://www.adobe.com/go/getflashplayer"><img 
		src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" 
		alt="Get Adobe Flash player" /></a></p>
</div>
<?php echo $javascript->link('webcam.js'); ?>