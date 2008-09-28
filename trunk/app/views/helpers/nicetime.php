<?php
/**
 * NiceTime helper - transforms time differences to human 
 * readable format
 */
class NiceTimeHelper extends Helper {
    
    public function show($date = null) {
    	
    	# check if parameters are timestamps - else convert
    	if(!preg_match('/^\d{10}$/', $date)) {
    	   $date = strtotime($date);	
    	}

        return $this->convert($date);        
    }
    
    
    protected function convert($date) {
    	$now = time();
    	
    	$diff = $now - $date;
    	if($diff < 0) {
    	    $diff = $diff * -1;
    	}
    	
    	$return = '';
    	if($diff < 60){
            if($diff>=1) {
            	$string = $diff .' seconds';
            } else {
                $string = '1 second';	
            }
    	} elseif($diff >= 60 && $diff < 3600) {
    	    if($diff/60>=1.5) {
                $string = round($diff/60) .' minutes';
            } else {
                $string = '1 minute';  
            }
    	} elseif($diff >= 3600 && $diff < 86400) {
    	    if($diff/3600>=1.5) {
                $string = round($diff/3600) .' hours';
            } else {
                $string = '1 hour';  
            }   
        } else {
            $return = date('Y-m-d H:s:i', $date);
        }
        
        if($return === '') {
            if($now - $date < 0) {
                $return = 'in ' . $string;
            } else {
                $return = $string . ' ago';
            }
        }
        return $return;
    }
}