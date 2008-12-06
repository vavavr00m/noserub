<?php

class SearchesController extends AppController {
    public $uses = array('ServiceType', 'Entry');
    public $helpers = array('nicetime');
    public $components = array('cluster');
    
    /**
     * 
     */
    public function index() {
        $this->checkUnsecure();
        
        $q = isset($this->params['url']['q']) ? $this->params['url']['q'] : '';
        $q = strtolower(htmlspecialchars(strip_tags($q), ENT_QUOTES, 'UTF-8'));
        
        if($q) {
            $conditions = array(
                'search'      => $q
            );
            $items = $this->Entry->getForDisplay($conditions, 50);
            usort($items, 'sort_items');
            $items = $this->cluster->removeDuplicates($items);
            $items = $this->cluster->create($items);
        } else {
            $items = array();
        }
        
        $this->set('headline', __('Find public items...', true));        
    	$this->set('services', $this->ServiceType->getFilters());
    	$this->set('items', $items);
    	$this->set('filter', array());
    	$this->set('q', $q);
    }    
}