<dl>
    <?php if(isset($data['title'])) { ?>
        <dt>Title</dt>
        <dd><?php echo htmlentities($data['title']); ?></dd>
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
<form id="AccountAddFormStep3" method="post" action="<?php echo $this->here ?>">
    <input class="submitbutton" type="submit" name="submit" value="OK. Save it!"/>
    <input class="submitbutton" type="submit" name="cancel" value="Forget it"/>
</form>