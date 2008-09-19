<?php
/* SVN FILE: $Id:$ */
 
class Syndication extends AppModel {
    public $belongsTo = array('Identity');
    public $hasAndBelongsToMany = array('Account');
}