<h3><?php __('Snapshot from Webcam'); ?></h3>
<?php echo $form->input('Entry.webcam_title', array('label' => __('Title', true))); ?>
<p />
<div id="webcam_snapshot">
	<p>
	    <?php __('You need the Flash PlugIn and JavaScript enabled to use this'); ?>
	</p>
	<p><a href="http://www.adobe.com/go/getflashplayer"><img 
		src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" 
		alt="Get Adobe Flash player" /></a></p>
</div>
<br />
<span>
    <?php echo $html->link( __('Snapshot', true), '#', array('id' => 'noserub_webcam_snapshot')); ?>
    <?php echo $html->link( __('Cancel', true), '#', array('id' => 'noserub_webcam_cancel')); ?>
    <?php echo $html->link( __('Send', true), '#', array('id' => 'noserub_webcam_upload')); ?>
</span>
<?php echo $html->script('webcam.js'); ?>