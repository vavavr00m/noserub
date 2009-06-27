<?php
   
class NetworkInfoController extends ApiAppController {
	public $uses = array('Identity', 'Migration');
	public $helpers = array('Xml');
	
	/**
     * used to return number of registered, active users, and some other
     * values.
     */
    public function info() {
        $data = array();
    	
    	if (Context::read('network.api_info_active')) {
            $restricted_hosts = Context::read('network.registration_restricted_hosts');
            $data = array(
                'num_users' => $this->Identity->Network->getNumberOfUsers(Context::read('network.id')),
                'registration_type' => Context::read('network.registration_type'),
                'restricted_hosts'  => $restricted_hosts ? 'yes' : 'no',
                'migration' => $this->Migration->getCurrentMigration(),
                'allow_subscriptions' => Context::read('network.allow_subscriptions')
            );
        } else {
        	$data = array('error' => 'Info-API is disabled');
        	header("HTTP/1.0 403 Forbidden");
        }
        
        $this->set('data', $data);
    }
}