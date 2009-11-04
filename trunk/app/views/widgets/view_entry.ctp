<div class="widget widget-view-entry">
    <?php if(!$data) { 
        __('There is no such entry available.');
    } else { 
        echo $this->element('entries/view');
        echo $this->element('entries/favorited_by');
        echo '<hr />';
        echo $this->element('comments/view');
    } ?>
</div>