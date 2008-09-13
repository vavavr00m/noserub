<?php
/* SVN FILE: $Id:$ */
 
class Xmpp extends AppModel {
    public $useTable = false;
    
    public function broadcast($messages) {
        App::import('Vendor', 'xmpp2', array('file' => 'XMPPHP'.DS.'XMPP.php'));
        $conn = new XMPPHP_XMPP('jabber.identoo.com', 5222, 'fullfeed', '123_noserub_456', 'xmpphp', 'jabber.identoo.com', $printlog = false, $loglevel=XMPPHP_Log::LEVEL_VERBOSE);

        if(!is_array($messages)) {
            $messages = array($messages);
        }
        
        # get all the users that are online
        $users = array();
        $conn->connect();
        while(!$conn->isDisconnected()) {
            $payloads = $conn->processUntil(array('presence', 'end_stream', 'session_start'), 1);
            if(!$payloads) {
                foreach($users as $user) {
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
                }
            }
        }    
    }
}