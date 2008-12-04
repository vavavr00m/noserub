<?php
/* SVN FILE: $Id:$ */
 
class Favorite extends AppModel {
    public $belongsTo = array('Identity', 'Entry');
}