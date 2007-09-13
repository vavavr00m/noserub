<p>
    Information about the current state of your NoseRub instance. Please
    check back after each update you install, because new Defines may have
    been introduced and/or changes in the Database structure been made.
</p>
<h1>Directories</h1>
    Status:
<?php if(empty($directories)) { ?>
    <strong>OK</strong>
<?php } else { ?>
    <ul> 
        <?php foreach($directories as $directory) { ?>
            <li><?php echo '<strong>' . $directory . '</strong>: not writeable'; ?></li>
        <?php } ?>
    </ul>
<?php } ?>
<br /><br />    
<h1>Settings</h1>
    Status:
<?php if(empty($constants)) { ?>
    <strong>OK</strong>
<?php } else { ?>
    <ul> 
        <?php foreach($constants as $constant => $message) { ?>
            <li><?php echo '<strong>' . $constant . '</strong>: ' . $message; ?></li>
        <?php } ?>
    </ul>
<?php } ?>
<br /><br />
<h1>Database</h1>
Configuration:
<?php
    switch($database_status) {
        case -1:
            echo ' <strong>database.php in /app/config/ not found!</strong>'; break;
        case  0:
            echo ' <strong>Cannot connect to database!'; break;
        case  1:
            echo ' <strong>OK</strong>'; break;
    }
?><br /><br />
Actual version #: <?php echo $current_migration; ?><br />
<?php if(isset($migrations)) { ?>
    Updating to #<?php echo $most_recent_migration; ?>:<br />
    <ul>
        <?php foreach($migrations['sql'] as $idx => $migration) { ?>
            <li><?php echo $migration['name']; ?></li>
            <?php if(isset($migrations['php'][$idx]['name'])) { ?>
                <li><?php echo $migrations['php'][$idx]['name']; ?></li>
            <?php } ?>
        <?php } ?>
    </ul>
    <strong>Done</strong>
<?php } else { ?>
    Database up-to-date.
<?php } ?>