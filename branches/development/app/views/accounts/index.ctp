<?php $flashmessage->render(); ?>
<p class="infotext">
    Here you can add all your own social/online activities and import friends in your network.
</p>

<hr class="space" />

<div class="left">
    <?php echo $this->renderElement('accounts/index'); ?>
</div>

<div class="right">
    <form method="POST" action="<?php echo $this->here; ?>">
        <input type="hidden" name="security_token" value="<?php echo $security_token; ?>">
        <table class="listing">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contact_accounts as $account) { ?>
                    <tr>
                        <td>
                            <img class="whoisicon" alt="<?php echo $account['Service']['name']; ?>" src="/images/icons/services/<?php echo $account['Service']['icon']; ?>"/>
                            <?php echo $account['Service']['name']; ?>
                        </td>
                        <td>
                            <?php
                                $value = '';
                                foreach($data as $item) {
                                    if($item['Account']['service_id'] == $account['Service']['id']) {
                                        $value = $item['Account']['username'];
                                    }
                                    if($value) {
                                        break;
                                    }
                                }
                            ?>
                            <input type="text" size="32" value="<?php echo $value; ?>" name="data[Service][<?php echo $account['Service']['id']; ?>][username]">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <fieldset>
            <input class="submitbutton" type="submit" value="Save changes"/>
        </fieldset>
    </form>
</div>