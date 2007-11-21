<?php if (isset($manage) && $manage && $session->check('Identity')): ?>
<span class="more"><?php echo $html->link('manage', '/'.$session->read('Identity.local_username').'/contacts'); ?></span>
<?php endif; ?>
<h4>
	<?php echo $box_head; ?>
</h4>
<p class="contactsbox">
    <?php foreach($data as $item) { ?>
        <?php if(isset($item['WithIdentity'])) {
            $item = $item['WithIdentity'];
        } else if(isset($item['Identity'])) {
            $item = $item['Identity'];
        }?>
        <a href="http://<?php echo $item['username']; ?>" rel="friend">
            <?php if($item['photo']) {
                if(strpos($item['photo'], 'http://') === 0 ||
                   strpos($item['photo'], 'https://') === 0) {
                    # contains a complete path, eg. from not local identities
                    $photo_url = $item['photo'];
                    $contact_photo = str_replace('.jpg', '-small.jpg', $photo_url);
                } else {
                    $contact_photo = $static_base_url . $item['photo'].'-small.jpg';
                }	                
            } else {
                $contact_photo = $sex['img-small'][$item['sex']];
            } ?>
            <img src="<?php echo $contact_photo; ?>" width="35" height="35" alt="<?php echo $item['local_username']; ?>'s Picture" class="<?php echo $item['local']==1 ? 'internthumbs' : 'externthumbs'; ?>" />
        </a>
    <?php } ?>
</p>