<dl>
    <?php if(isset($data['title'])) { ?>
        <dt>Title</dt>
        <dd><?php echo htmlentities($data['title']); ?></dd>
    <?php } ?>
    
    <dt>URL</dt>
    <dd><?php echo htmlentities($data['account_url']); ?></dd>
    
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
</dl>
<form id="AccountAddFormStep3" method="post" action="<?php echo $this->here ?>">
    <?php echo $form->submit('OK. Save it!', array('name' => 'submit')); ?>
    <?php echo $form->submit('Forget it', array('name' => 'cancel')); ?>
<?php echo $form->end(); ?>