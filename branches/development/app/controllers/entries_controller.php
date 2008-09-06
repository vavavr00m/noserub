<?php
class EntriesController extends AppController {
    public $uses = array('Entry');
    
    /**
     * Go through all accounts and update
     * the entries.
     *
     * @param  
     * @return 
     * @access 
     */
    public function shell_update() {
        $this->jobs_update();       
    } 

    public function jobs_update() {
        $this->Entry->Account->contain();
        $data = $this->Entry->Account->find(
            'all',
            array(
                'fields' => 'id',
            )
        );

        $entries = array();
        foreach($data as $item) {
            $entries[] = $this->Entry->updateByAccountId($item['Account']['id']);
        }
        
        $msg = count($entries) . ' entries added/updated';
        
        $this->set('data', $msg);
    }
}