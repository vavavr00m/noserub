<div id="bd-main" class="with-sidebar">
    <div id="bd-main-hd">
	</div>
	<div id="bd-main-bd">
        <p id="message" class="info">
            <strong><?php __('No OpenID sites found.'); ?></strong>
        </p>

        <p class="right">
        	<img src="<?php echo Router::url('/images/openid.gif'); ?>" width="200" height="66" alt="OpenID" />
        </p>

        <p class="infotext">
            <?php __('You probably never used your <strong>NoseRub-ID</strong> to <strong>log in at any OpenID site</strong>. Did you know, that your NoseRub-ID <strong>is</strong> an OpenID?'); ?>
        </p>

        <p class="infotext">
        	<?php __('You can use your OpenID on any one of a growing number of sites (nearly ten-thousand) which support OpenID.<br />If one of your favorite sites doesn’t support OpenID yet, ask them when they will!'); ?>
        </p>

        <p class="infotext">
        	<?php echo sprintf(__('%s gives you an overview of sites where you can use your OpenID to log in today.', true), '<a href="http://openiddirectory.com/">The OpenID Directory</a>'); ?>
        </p>
    </div>
    <div id="bd-main-sidebar">
		<?php echo $noserub->widgetSettingsNavigation(); ?>
	</div>
</div>