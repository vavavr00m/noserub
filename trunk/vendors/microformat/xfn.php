<?php

require_once 'google_social_graph.php';
require_once 'base.php';

class xfn extends microformat_base {
    public function getByUrl($url) {
        $xfn_google = GSocialGraph::lookup($url);
        
        $content = WebExtractor::fetchUrl($url);
        $xfn_url = $this->getByContent($content);
        $merge   = array_merge($xfn_google, $xfn_url);
        
        $result = array();
        foreach($merge as $url) {
            if(!in_array($url, $result)) {
                $result[] = $url;
            }
        }
        
        return $result;
    }
    
    private function getByContent($content) {
        $result = array();
        
        $relmes = $this->getValues($content, 'rel', 'me', 'a');

        foreach($relmes as $relme) {
            $href = $relme->getAttribute('href');
            if($href) {
                $result[] = $href;
            }
        }

        return $result;
    }
}