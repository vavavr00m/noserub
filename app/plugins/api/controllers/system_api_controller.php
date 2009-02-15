<?php

// TODO "SystemApi" is not very expressive, a better name is needed   
class SystemApiController extends ApiAppController {
	public $uses = array('Identity', 'Migration');
	
	/**
     * used to return number of registered, active users, and some other
     * values.
     */
    public function info() {
        $data = array();
    	
    	if (Configure::read('NoseRub.api_info_active')) {
            $restricted_hosts = Configure::read('NoseRub.registration_restricted_hosts');
            $data = array(
                'num_users' => $this->getNumberOfUsers(),
                'registration_type' => Configure::read('NoseRub.registration_type'),
                'restricted_hosts'  => $restricted_hosts ? 'yes' : 'no',
                'migration' => $this->Migration->getCurrentMigration()
            );
        }
        
        $this->set('data', $data);
        $this->Api->render();
    }
    
    private function getNumberOfUsers() {
    	$this->Identity->contain();
        $conditions = array(
            'network_id' => $this->context['network_id'],
            'email <>'   => '',
            'hash'       => '',
            'NOT username LIKE "%@"'
        );
        
        return $this->Identity->find('count', array('conditions' => $conditions));
    }
}