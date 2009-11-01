<?php

class XmppController extends AppController {
    /**
     * the main loop for a running XMPP deamon
     */
    public function shell_run() {
        if (Configure::read('NoseRub.xmpp_full_feed_user') === '') {
            return;
        }
        App::import('Vendor', 'xmpp', array('file' => 'XMPPHP'.DS.'XMPP.php'));

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
     
        try {
            $conn->connect();
            while(!$conn->isDisconnected()) {
                $payloads = $conn->processUntil(array('message', 'presence', 'end_stream', 'session_start'));
                foreach($payloads as $event) {
                	$pl = $event[1];
                    
                	switch($event[0]) {
                    	case 'message': 
					        if($pl['body']=='') {
					          #  print $pl['from'] . ' is typing...' . "\n";
					            break;
					        }
					        if($pl['body'] == 'uptime') {
					            $conn->message($pl['from'], uptime(), $pl['type']);
					            break;
					        }
                            print "---------------------------------------------------------------------------------\n";
                            print "Message from: {$pl['from']}\n";
                            if(isset($pl['Subject']) && $pl['subject']) print "Subject: {$pl['subject']}\n";
                            print $pl['body'] . "\n";
                            print "---------------------------------------------------------------------------------\n";
                            $conn->message($pl['from'], $body="Thanks for sending me \"{$pl['body']}\".", $type=$pl['type']);
                            if($pl['body'] == 'quit') $conn->disconnect();
                            if($pl['body'] == 'break') $conn->send("</end>");
                            break;
                        case 'presence':
                            print "Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}\n";
                            break;
                        case 'session_start':
                            print "Session Start\n";
                            $conn->getRoster();
                            $conn->presence($status="Cheese!");
                            break;
                    }
                }
            }
        } catch(XMPPHP_Exception $e) {
            die($e->getMessage());
        }
    }
}