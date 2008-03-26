<?php

class microformat_base {
    protected function getValues($node, $attr, $value, $tag = '*') {
        $result = array();
        
        if(is_string($node)) {
            $doc = new DOMDocument();
            @$doc->loadHTML($node);
            $values = $doc->getElementsByTagName($tag);
        } else {
            $values = $node->getElementsByTagName($tag);
        }
    
        foreach($values as $node) {
            $classes = $node->getAttribute($attr);
            if($classes) {
                $classes_arr = split(' ', $classes);
                if(in_array($value, $classes_arr)) {
                    $result[] = $node;
                    #echo $node->nodeName.' => '.$node->getAttribute('class') . "\n";
                }
            }
        }

        return $result;
    }
}