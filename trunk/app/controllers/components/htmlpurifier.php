<?php

class HtmlpurifierComponent extends Object {
	
	/**
	 * Just a stub for calling the code
	 * from the helper. Not *that* nice, but
	 * this way we only have to make changes to
	 * the code in the helper to have the same
	 * functionality in controllers and views.
	 */
	public function clean($content) {
	    App::import('Helper', 'Htmlpurifier');
        $htmlpurifier = new HtmlpurifierHelper();
        
        return $htmlpurifier->clean($content);
	}
}    