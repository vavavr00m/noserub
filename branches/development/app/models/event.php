<?php
/* SVN FILE: $Id:$ */
 
class Event extends AppModel {
    public $belongsTo = array('Identity', 'Location');
    public $hasMany = array(
        'Entry' => array(
            'foreignKey' => 'foreign_key',
            'conditions' => array(
                'Entry.model' => 'event'
            )
        )
    );
    
    public $actsAs = array(
        'Sluggable' => array(
            'label' => 'name', 
            'translation' => 'utf-8'
    ));
    
    public $validate = array(
        'name' => array(
            'rule' => array('minLength', 2),
            'required' => true,
            'allowEmpty' => false,
            'message' => ''
        ),
        'url' => array(
            'rule' => array('url', true),
            'allowEmpty' => true,
            'message' => ''
        )
    );
        
    public function __construct() {
        parent::__construct();
    
        $this->validate['name']['message'] = __('The name must be at least 2 characters long', true);
        $this->validate['name']['url'] = __('You need to give a valid URL, or leave the field blank', true);
    }
    
    public function getTypes() {
        return array(
            0 => __('Unknown', true),
            1 => __('Comedy', true),
            2 => __('Commercial', true),
            3 => __('Education', true),
            4 => __('Family', true),
            5 => __('Festival', true),
            6 => __('Media', true),
            7 => __('Music', true),
            8 => __('Performing / Visual Arts', true),
            9 => __('Politics', true),
            10 => __('Social', true),
            11 => __('Sports', true),
            12 => __('Other', true),
        );
    }
    
    public function add($data) {
        $this->create();
        if($this->save($data)) {
            // now set UID
            $url = Router::url('/events/view/' . $this->id . '/', true);
            $this->saveField('uid', md5($url));
            
            return true;
        }
    }
    
    /**
     * Returns the last $limit events this user created
     *
     * @param int $identity_id
     * @param int $limit
     *
     * @return array
     */
    public function getNew($identity_id, $limit = 5) {
        return $this->find('all', array(
            'contain' => false,
            'conditions' => array(
                'identity_id' => $identity_id
            ),
            'limit' => $limit,
            'order' => 'created DESC'
        ));
    }
    
    public function saveInContext($data) {
        Context::write('event', array(
            'id' => $data['Event']['id'],
            'slug' => $data['Event']['slug'],
            'name' => $data['Event']['name'],
            'latitude' => $data['Location']['latitude'],
            'longitude' => $data['Location']['longitude']
        ));
    }
}