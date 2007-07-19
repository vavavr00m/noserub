<h1>Network</h1>
<?php foreach($data as $item) { ?>
    <h4><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h4>
	<?php echo $item->get_content(); ?>
<?php } ?>