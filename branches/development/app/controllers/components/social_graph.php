<?php

/*
 * SocialGraph API
 * eg. Google SocialGraph API
 */
App::import('Vendor', 'microformat'.DS.'google_social_graph');
App::import('Component', 'Json');

class SocialGraphComponent extends JsonComponent {

    /**
     * lookup
     *
     * @param mixed urls either as array or comma-separated string  
     * @return array
     */
    public function lookup($urls) {
        return $this->decode(GSocialGraph::lookup($urls));
    }
}

?>