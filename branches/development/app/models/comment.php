<?php
/* SVN FILE: $Id:$ */
 
class Comment extends AppModel {
    public $belongsTo = array('Entry', 'Identity');                                                   

}
