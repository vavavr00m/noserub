<?php
/* SVN FILE: $Id:$ */
 
class Ad extends AppModel {
    public $belongsTo = array('Network');
    
    public $validate = array(
        'name' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'allowEmpty' => false,
            'message' => ''
        ),
        'width' => array(
            'rule' => 'numeric',
            'required' => true,
            'allowEmpty' => false,
            'message' => ''
        ),
        'height' => array(
            'rule' => 'numeric',
            'required' => true,
            'allowEmpty' => false,
            'message' => ''
        ),
        'content' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'message' => ''
        )
    );
    
    public function __construct() {
        parent::__construct();
        $this->validate['name']['message'] = __('You need to specify a name', true);
        $this->validate['width']['message'] = __('You need to specify the width', true);
        $this->validate['height']['message'] = __('You need to specify the height', true);
        $this->validate['content']['message'] = __('Empty ads are not allowed', true);
    }
    
    /**
     * return the content of /app/views/themed/$name/ads.php
     *
     * @param string $name
     *
     * @return array
     */
    public function getForTheme($name = 'default') {
        if(strpos($name, DS) !== false || 
           strpos($name, '.') !== false) {
            // directory traversal attempt?
            return array();
        }
        
        $path = VIEWS . 'themed' . DS . $name . DS . 'ads.php';
        if(!file_exists($path)) {
            return array();
        }
        @eval(file_get_contents($path));
        if(!empty($theme_ad_spots)) {
            return $theme_ad_spots;
        } else {
            return array();
        }
    }
    
    /**
     * Returns the assignment of ads for adspots of this theme
     *
     * @param string $name
     *
     * @return array
     */
    public function getAssignmentsForTheme($name = 'default') {
        if(strpos($name, DS) !== false || 
           strpos($name, '.') !== false) {
            // directory traversal attempt?
            return array();
        }
        
        $path = CACHE . Context::networkId() . '_theme_' . $name . '_ad_assignment.php';
        if(!file_exists($path)) {
            return array();
        }
        @eval(file_get_contents($path));
        if(!empty($ad_assignments)) {
            return $ad_assignments;
        } else {
            return array();
        }
    }
    
    /**
     * Saves the ad assignement for actual network and given theme
     *
     * @param array $data
     * @param string $name
     */
    public function saveAssignment($data, $name = 'default') {
        if(strpos($name, DS) !== false || 
           strpos($name, '.') !== false) {
            // directory traversal attempt?
            return array();
        }
        
        // save the assignment array
        $path = CACHE . Context::networkId() . '_theme_' . $name . '_ad_assignment.php';
        @file_put_contents($path, '$ad_assignments=' . var_export($data, 1) . ';');
        
        // save the actual ads for faster delivery
        $ads = $this->find('all', array(
            'contain' => false,
            'conditions' => array(
                'network_id' => Context::networkId()
            ),
            'fields' => array(
                'id', 'content', 'allow_php'
            )
        ));
        
        $adspots = $this->getForTheme($name);
        $path = CACHE . Context::networkId() . '_theme_' . $name . '_ads.php';
        $ad_data = array();
        foreach($data as $ad_spot_id => $ad_id) {
            foreach($ads as $item) {
                if($item['Ad']['id'] == $ad_id['ad_id']) {
                    // @todo use $allow_php
                    $ad_data[$adspots[$ad_spot_id]['name']] = $item['Ad']['content'];
                }
            }
        }
        
        @file_put_contents($path, '$ad_data=' . var_export($ad_data, 1) . ';');
    }
    
    /**
     * Returns ad for given theme an current network
     */
    public function getAd($name, $theme = 'default') {
        if(strpos($theme, DS) !== false || 
           strpos($theme, '.') !== false) {
            // directory traversal attempt?
            return '';
        }
        
        $path = CACHE . Context::networkId() . '_theme_' . $theme . '_ads.php';
        @eval(file_get_contents($path));
        if(isset($ad_data[$name])) {
            return $ad_data[$name];
        } else {
            return '';
        }
    }
}