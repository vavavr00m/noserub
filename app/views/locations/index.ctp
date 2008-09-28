<?php
    $url = Router::url('/' . $session_identity['local_username']);
?>
<?php $flashmessage->render(); ?>
<p class="infotext">
    Once you added some locations, like <em>Home</em> and <em>Office</em>, you 
    will be able to set this location on your profile page to tell, where you 
    currently are.
</p>

<h2>Your locations</h2>
<p class="infotext">
    <a href="<?php echo $url . '/settings/locations/add/'; ?>" class="addmore">Create a new Location</a>
</p>
<?php if(!$data) { ?>
    <p class="infotext">
        You did not create any locations yet.
    </p>
<?php } else { ?>
    <table class="listing">
        <thead>
        <tr>
            <th>Name</th>
            <th>Address (hidden)</th>
            <th>Location</th>
            <th></th>
        </tr>
        </thead>
        <?php foreach($data as $item) { ?>
            <tr>
                <td><?php echo $item['Location']['name']; ?></td>
                <td><?php echo $item['Location']['address'] == '' ? '<em>Not entered</em>' : $item['Location']['address']; ?></td>
                <td>
                    Latitude: <?php echo $item['Location']['latitude']; ?><br />
                    Longitude: <?php echo $item['Location']['longitude']; ?>
                </td>
                <td>
                	<ul>
                   		<li class="delete icon"><a href="<?php echo  $url . '/settings/locations/'.  $item['Location']['id'] . '/delete/' . $security_token . '/'; ?>">Delete</a></li>
                   		<li class="edit icon"><a href="<?php echo $url . '/settings/locations/'.  $item['Location']['id'] . '/edit/'; ?>">Edit</a></li>
                   	</ul>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>