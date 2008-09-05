<div id="mainnav">
	<?php 
	    if(isset($mainMenu)) {
 	        echo $this->element('menu', array('menuItems' => $mainMenu->getMenuItems())); 
        }
	?>
</div>
