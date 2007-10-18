<?php
    $url = Router::url('/' . $session_identity['local_username']);
?>
<p class="infotext">
    You can create Feeds from your own social activities or those of friends in your network. This feeds then can be used by your RSS-Reader or you can integrate it on your website to show everyone, what you did in the last couple of hours and days.
</p>

<hr class="space" />

<h2>Your feeds</h2>
<?php if(!$data) { ?>
    <p class="infotext">
        You did not create any feeds yet.
    </p>
<?php } else { ?>
    <table class="listing">
        <thead>
        <tr>
            <th>Name</th>
            <th>Links</th>
            <th></th>
        </tr>
        </thead>
        <?php foreach($data as $item) { ?>
            <tr>
                <td><?php echo $item['Syndication']['name']; ?></td>
                <td>
                    <a href="<?php echo $url . '/feeds/' . $item['Syndication']['hash']; ?>.rss">RSS</a> - 
                    <a href="<?php echo $url . '/feeds/' . $item['Syndication']['hash']; ?>.js">JSON</a>
                </td>
                <td>
                    <?php echo $html->link('Delete', '/' . $session_identity['local_username'] . '/settings/feeds/'.  $item['Syndication']['id'] . '/delete/'); ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<p class="infotext">
<a href="<?php echo $url . '/settings/feeds/add/'; ?>" class="addmore">Create a new Feed</a>
</p>