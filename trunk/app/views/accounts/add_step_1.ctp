<h1>Add new Account - Service or Blog/RSS-Feed?</h1>
<form id="AccountAddFormStep1" method="post" action="<?php echo $this->here ?>">
    <fieldset>
        <?php
            $services = 'Service: ' . $form->select('Account.service_id', $services, null, null, false); 
            echo $form->radio('Account.type', array(1 => '', 2 => ''), $services.'<br />');
        ?>
        Any RSS-Feed (eg. Blogs)
        
        <?php echo $form->submit('Next step >'); ?>
    </fieldset>
<?php echo $form->end(); ?>