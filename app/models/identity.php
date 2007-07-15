<?php
/* SVN FILE: $Id:$ */
 
class Identity extends AppModel {
    var $hasMany = array('Contact', 'Account');
    var $belongsTo = array('Login');
}