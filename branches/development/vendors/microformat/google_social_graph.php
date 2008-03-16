<?php

vendor('Zend'.DS.'Json');

class GSocialGraph {
    public function lookup($urls) {
        if(is_array($urls)) {
            $nodes = join(',', $urls);
        } else {
            $nodes = $urls;
        }

        $params = 'q=' . $nodes .
                  '&edo=0' . # Return edges out from returned nodes
                  '&edi=0' . # Return edges in to returned nodes.
                  '&fme=1' . # Follow me links, also returning reachable nodes.
                  '&sgn=0';   # Return internal representation of nodes. */
    
        $request = 'http://socialgraph.apis.google.com/lookup?' . $params;
        
        $result = Zend_Json::decode(file_get_contents($request));  
        $data = array();
        foreach($result['nodes'] as $url => $item) {
            $data[] = $url;
        }
        return $data;          
    }
}
?>