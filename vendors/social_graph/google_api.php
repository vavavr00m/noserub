<?php
    class GSocialGraph {
        public function lookup($urls) {
            if(is_array($urls)) {
                $nodes = join(',', $urls);
            } else {
                $nodes = $urls;
            }

            $params = 'q=' . $nodes .
                      '&edo=1' . # Return edges out from returned nodes
                      '&edi=1' . # Return edges in to returned nodes.
                      '&fme=1' . # Follow me links, also returning reachable nodes.
                      '&sgn=1';   # Return internal representation of nodes. */
        
            $request = 'http://socialgraph.apis.google.com/lookup?' . $params;
            
            return file_get_contents($request);            
        }
    }
?>