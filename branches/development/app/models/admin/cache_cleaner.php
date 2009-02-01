<?php

class CacheCleaner {
	public static function cleanUp() {
    	CacheCleaner::deleteDirectoryContent(CACHE.'models');
    	CacheCleaner::deleteDirectoryContent(CACHE.'persistent');
	}
	
	private static function deleteDirectoryContent($path) {
		$dir = new DirectoryIterator($path);
	    
    	foreach ($dir as $fileinfo) {
	        if ($fileinfo->isFile()) {
	            unlink($fileinfo->getPathName());
	        } 
	    }
	}
}
