<?php
/* SVN FILE: $Id:$ */
 
class Xmpp extends AppModel {
    public $useTable = false;
    
    public function broadcast($messages) {
        if(!is_array($messages)) {
            $messages = array($messages);
        }
        
        if(!$messages) {
            return;
        }
        
        if (Configure::read('NoseRub.xmpp_full_feed_user') === '') {
            return true;
        }
        
        App::import('Vendor', 'xmpp2', array('file' => 'XMPPHP'.DS.'XMPP.php'));
        $conn = new XMPPHP_XMPP(
                Configure::read('NoseRub.xmpp_full_feed_server'),
        		Configure::read('NoseRub.xmpp_full_feed_port'),
        		Configure::read('NoseRub.xmpp_full_feed_user'),
        		Configure::read('NoseRub.xmpp_full_feed_password'),
                'xmpphp', 
                Configure::read('NoseRub.xmpp_full_feed_server'), 
                $printlog = false, 
                $loglevel = XMPPHP_Log::LEVEL_VERBOSE
        );
        $conn->autoSubscribe();
        
        # get all the users that are online
        $users = array();
        $conn->connect();
        while(!$conn->isDisconnected()) {
            $payloads = $conn->processUntil(array('presence', 'end_stream', 'session_start'), 5);
            if(!$payloads) {
                foreach($users as $user) {
                    #echo 'messaging to: ' . $user . "\n";
                    foreach($messages as $message) {
                        $conn->message($user, $message);
                    }
                }
                $conn->disconnect();
                return true;
            }
            foreach($payloads as $event) {
                switch($event[0]) {
                    case 'session_start':
                        #$conn->getRoster();
                        $conn->presence($status="Cheese!");
                    break;
                    case 'presence':
                        $user = $event[1]['from'];
                        $user = substr($user, 0, strpos($user, '/'));
                        $users[] = $user;
                        break;
                    case 'end_stream':
                        $conn->disconnect();
                        return true;
                }
            }
        }    
        
        return true;
    }
}