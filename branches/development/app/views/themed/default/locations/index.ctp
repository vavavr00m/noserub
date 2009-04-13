<?php
    $url = Router::url('/' . Context::read('logged_in_identity.local_username'));
?>
<div id="inhalt">
    <p class="infotext">
        <?php __('Once you added some locations, like <em>Home</em> and <em>Office</em>, you 
        will be able to set this location on your profile page to tell, where you 
        currently are.'); ?>
    </p>

    <h2><?php __('Your locations'); ?></h2>
    <p class="infotext">
        <a href="/settings/locations/add/" class="addmore"><?php __('Create a new Location'); ?></a>
    </p>
    <?php if(!$data) { ?>
        <p class="infotext">
            <?php __('You did not create any locations yet.'); ?>
        </p>
    <?php } else { ?>
        <table class="listing">
            <thead>
            <tr>
                <th><?php __('Name'); ?></th>
                <th><?php __('Address (hidden)'); ?></th>
                <th><?php __('Location'); ?></th>
                <th></th>
            </tr>
            </thead>
            <?php foreach($data as $item) { ?>
                <tr>
                    <td><?php echo $item['Location']['name']; ?></td>
                    <td><?php echo $item['Location']['address'] == '' ? '<em>' . __('Not entered', true) . '</em>' : $item['Location']['address']; ?></td>
                    <td>
                        <?php __('Latitude'); ?>: <?php echo $item['Location']['latitude']; ?><br />
                        <?php __('Longitude'); ?>: <?php echo $item['Location']['longitude']; ?>
                    </td>
                    <td>
                    	<ul>
                       		<li class="delete icon"><a href="<?php echo '/settings/locations/'.  $item['Location']['id'] . '/delete/' . $noserub->fnSecurityToken() . '/'; ?>"><?php __('Delete'); ?></a></li>
                       		<li class="edit icon"><a href="<?php echo '/settings/locations/'.  $item['Location']['id'] . '/edit/'; ?>"><?php __('Edit'); ?></a></li>
                       	</ul>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</div>

<div id="rechts">
    <?php echo $noserub->widgetSettingsNavigation(); ?>
</div>