<?php
/* SVN FILE: $Id:$ */
 
class Syndication extends AppModel {
    var $belongsTo = array('Identity');
    
    var $hasAndBelongsToMany = array('Account');
    
}