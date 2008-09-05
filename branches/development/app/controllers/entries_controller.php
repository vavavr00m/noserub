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

        foreach($data as $item) {
            $this->Entry->updateByAccountId($item['Account']['id']);
        }
    }
}