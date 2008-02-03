<?php

/*
 * SocialGraph API
 * eg. Google SocialGraph API
 */
vendor('social_graph/google_api');

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