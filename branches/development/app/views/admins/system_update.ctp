<div id="content" class="wrapper">
<?php $no_error = true; ?>
<p id="message" class="info">
    Information about the current state of your NoseRub instance. Please
    check back after each update you install, because new Defines may have
    been introduced and/or changes in the Database structure been made.
</p>
<h2>Extension</h2>
<?php if(empty($extensions)) { ?>
    <p id="message" class="success">
        Status: <strong>OK</strong>
    </p>
<?php } else { ?>
    <?php $no_error = false; ?>
    <p id="message" class="alert">
        Some extensions need to be fixed!
    </p>
    <?php foreach($extensions as $extension => $reason) { ?>
        <li><?php echo '<strong>' . $extension . '</strong>: ' . $reason; ?></li>
    <?php } ?>
<?php } ?>
<h2>Directories</h2>
<?php if(empty($directories)) { ?>
    <p id="message" class="success">
        Status: <strong>OK</strong>
    </p>
<?php } else { ?>
    <?php $no_error = false; ?>
    <p id="message" class="alert">
        Some directories are not writeable!
    </p>
    <ul> 
        <?php foreach($directories as $directory) { ?>
            <li><?php echo '<strong>' . $directory . '</strong>: not writeable'; ?></li>
        <?php } ?>
    </ul>
<?php } ?>   
<h2>Settings</h2>
<?php if(empty($constants)) { ?>
    <p id="message" class="success">
        Status: <strong>OK</strong>
    </p>
<?php } else { ?>
    <?php $no_error = false; ?>
    <p id="message" class="alert">
        Some constants need to be fixed!
    </p>
    <ul> 
        <?php foreach($constants as $constant => $message) { ?>
            <li><?php echo '<strong>' . $constant . '</strong>: ' . $message; ?></li>
        <?php } ?>
    </ul>
<?php } ?>
<?php if(isset($database_status)) { ?>
    <h2>Database</h2>
    <?php if($database_status == 1) { ?>
        <p id="message" class="success">
            Configuration: <strong>OK</strong>
        </p>
        <h2>Actual version #: <?php echo $current_migration; ?></h2>
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
            <p id="message" class="success">
                Database up-to-date!
            </p>
        <?php } else { ?>
            <p id="message" class="success">
                Status: <strong>OK</strong>
            </p>
        <?php } ?>
    <?php } else { ?>
        <?php $no_error = false; ?>
        <p id="message" class="alert">
            Configuration: <strong><?php
                switch($database_status) {
                    case -1:
                        echo ' <strong>database.php in /app/config/ not found!</strong>'; break;
                    case  0:
                        echo ' <strong>Cannot connect to database!'; break;
                }
            ?></strong>
        </p>
    <?php } ?>
<?php } else { ?>
    <?php $no_error = false; ?>
    <h2>Errors!</h2>
    <p>
        Please correct the errors above and then return to this URL, to finish the update.
    </p>
<?php } ?>
<?php if($no_error) { ?>
    <h2>Talk back to NoseRub.com</h2>
    <p id="message" class="info">
        If you want to, you can just click on the button and NoseRub.com will receive
        knowledge about your installation here. The decision wether to click that button
        or not is solely up to you.
    </p>
    <form method="POST" action="http://noserub.com/talkback">
        <fieldset>
            <legend>
                We don't send any other data beside what you can verify here, once you click that button.
            </legend>
            <div class="input text">
                <input type="hidden" name="talkback[url]" value="<?php echo NOSERUB_FULL_BASE_URL; ?>"><strong>URL:</strong> <?php echo NOSERUB_FULL_BASE_URL; ?>
            </div>
            <div class="input text">
                <input type="hidden" name="talkback[migrations]" value="<?php echo $most_recent_migration; ?>"><strong>DB Version:</strong> <?php echo $most_recent_migration; ?>
            </div>
        </fieldset>
        <fieldset>
            <input class="submitbutton" type="submit" value="Send to NoseRub.com"/>
        </fieldset>
    </form>
<?php } ?>
</div>