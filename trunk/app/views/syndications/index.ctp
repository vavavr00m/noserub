<p>
    You can create Feeds from your own social activities or those of friends in your network. This feeds then can be used by your RSS-Reader or you can integrate it on your website to show everyone, what you did in the last couple of hours and days.
</p>
<h2>Your feeds</h2>
<?php if(!$data) { ?>
    <p>
        You yet did not create a feed.
    </p>
<?php } else { ?>
    <table class="listing">
        <tr>
            <th>Name</th>
            <th>Links</th>
            <th></th>
        </tr>
        <?php foreach($data as $item) { ?>
            <tr>
                <td><?php echo $item['Syndication']['name']; ?></td>
                <td>
                    <a href="#<?php echo $item['Syndication']['hash']; ?>.rss">RSS</a> - 
                    <a href="#<?php echo $item['Syndication']['hash']; ?>.js">JSON</a>
                </td>
                <td>
                    <?php echo $html->link('Delete', '/' . $session_identity['local_username'] . '/settings/feeds/'.  $item['Syndication']['id'] . '/delete/'); ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
<a href="<?php echo Router::url('/' . $session_identity['local_username'] . '/settings/feeds/add/'); ?>">Create new Feed</a>