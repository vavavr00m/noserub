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
        
        if(!defined('NOSERUB_XMPP_FULL_FEED_USER') || !NOSERUB_XMPP_FULL_FEED_USER) {
            return true;
        }
        
        App::import('Vendor', 'xmpp2', array('file' => 'XMPPHP'.DS.'XMPP.php'));
        $conn = new XMPPHP_XMPP(
                NOSERUB_XMPP_FULL_FEED_SERVER, 
                NOSERUB_XMPP_FULL_FEED_PORT, 
                NOSERUB_XMPP_FULL_FEED_USER, 
                NOSERUB_XMPP_FULL_FEED_PASSWORD, 
                'xmpphp', 
                NOSERUB_XMPP_FULL_FEED_SERVER, 
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
                    echo 'messaging to: ' . $user . "\n";
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