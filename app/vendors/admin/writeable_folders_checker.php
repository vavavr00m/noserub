<?php

class WriteableFoldersChecker {
	
	public static function check() {
    	$writeableDirectories = array(APP.'tmp', WWW_ROOT.'static'.DS.'avatars');
    	
        $out = array();
        foreach($writeableDirectories as $directory) {
            if(!is_writeable($directory)) {
                $out[] = $directory;
            }
        }
        
        return $out;
    }
}