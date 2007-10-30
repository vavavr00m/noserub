<?php

	class FilterSanitizeComponent extends Object {
		
		public function sanitize($filter) {
			$sanitizedFilter = false;
    	
	    	switch($filter) {
	            case 'photo':
	            case 'video':
	            case 'audio':
	            case 'link':
	            case 'text':
	            case 'event':
	            case 'micropublish':
	            case 'document':
	            case 'location':
	                $sanitizedFilter = $filter; 
	                break;
	        }
	        
	        return $sanitizedFilter;
		}
	}
?>