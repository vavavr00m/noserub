<form id="AccountAddFormStep3" method="post" action="<?php echo $this->here ?>">
<dl>
    <?php if(isset($data['title'])) { ?>
        <dt>Title</dt>
        <dd>
		  <?php
		  if ($data['service_id'] === 8):
           echo $form->input('Account.title', array('value' => $data['title']));
		  else:
		  	echo htmlentities($data['title']);
			endif;
			?>
			</dd>
    <?php } ?>
    
    <dt>URL</dt>
    <dd><?php echo htmlentities($data['account_url']); ?></dd>
    
    <?php if($data['feed_url']) { ?>
        <dt>Feed</dt>
        <dd><?php echo htmlentities($data['feed_url']); ?></dd>
    
        <dt>Items</dt>
        <dd>
            <ul>
                <?php foreach($data['items'] as $item) { ?>
                    <li><a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a></li>
                <?php } ?>
            </ul>
        </dd>
    <?php } ?>
</dl>
    <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
    <input class="submitbutton" type="submit" name="submit" value="OK. Save it!"/>
    <input class="submitbutton" type="submit" name="cancel" value="Forget it"/>
</form>
