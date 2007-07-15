<?php
/* SVN FILE: $Id:$ */
 
class Account extends AppModel {
    var $belongsTo = array('Identity');
    var $hasOne = array('Service');
}