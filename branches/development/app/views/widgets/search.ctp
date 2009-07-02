<div class="widget widget-search">
    <form action="<?php echo Router::url('/search/'); ?>" method="GET">
        <p>
            <input type="text" name="q" value="<?php echo isset($q) ? $q : ''; ?>" />
        </p>
    </form>
</div>