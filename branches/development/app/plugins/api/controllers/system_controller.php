<?php

// TODO "System" is not very expressive, a better name is needed   
class SystemController extends ApiAppController {
	public $uses = array('Identity', 'Migration');
	
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
        }
        
        $this->set('data', $data);
        $this->Api->render();
    }
}