<?php

/**
 * Class title
 *
 * Class description goes here
 *
 * @package    profiles
 * @subpackage models
 */
class AppModel extends Model {
	public $actsAs = array('Containable');
	private $sanitizeExclusion = array('Ad', 'Entry', 'Comment');
    private $old_locale = null;
    
    /**
     * 	configure sanitization details based on the model and the 
     * 	incoming data
     * 
     * 	@var array $sanitization
     */
    public $sanitization = array (
        /*
        'Task' => array (
            'ignore_keys' => null, # define an array of the keys you don't want to sanitize
            'allow_tags' => array('<a>'),  # define an array of allowed tags
            'purify_keys' => null  # define which key value has to be purified (purifier must be installed first)
        )
        */
    );
    
    
    /**
     * afterFind hook
     * 
     * used here to sanitize data that comes from the DB
     *
     * @param  
     * @return 
     * @access 
     */
    public function afterFind($data) {
        # de-sanitize some elements
        
        if(empty($data)) {
            return $data;
        }
        
        foreach ($data as $key => $item) {
            if(!is_array($item)) {
                continue;
            }

            foreach($item as $model => $attributes) {
                if(!in_array($model, $this->sanitizeExclusion)) {
                    
                    # de-sanitize for whatever purpose - we have to talk about this!
                    if (is_array($attributes)) {
                        foreach ($attributes as $fieldName => $field) {
                            if (!is_array($field) && !empty ($field)) {
                                $data[$key][$model][$fieldName] = $this->deSanitize($field);
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
    
    /**
     * beforeSave
     * 
     * This function sanitizes the input by the specified rules in the 
     * $ignore_keys, $purify_keys and $allwed_tags array. 
     *
     * There's also an automatic and always active UTF7-filter to prevent 
     * XSS by UTF7 html chunk injection in ie6 and older
     *
     * @return bool
     * @access public
     */
    public function beforeSave($options = array()) {
        # set locale to an english version, so that floats (like
        # in longitude+latitude) don't get messed up for mysql.
        if(is_null($this->old_locale)) {
            $this->old_locale = setlocale(LC_ALL, '0');
        }
        setlocale(LC_NUMERIC, 'C');
        
        # sanitize some elements
        if(!empty ($this->data)) {
            if(!in_array($this->name, $this->sanitizeExclusion)) {

                if(array_key_exists($this->name, $this->sanitization)) {
                    $ignore_keys = $this->sanitization[$this->name]['ignore_keys'];
                    $purify_keys = $this->sanitization[$this->name]['purify_keys'];
                    $allowed_tags = $this->sanitization[$this->name]['allow_tags'];
                } else {
                    $ignore_keys = null;
                    $allowed_tags = null;
                    $purify_keys = null;
                }

                foreach($this->data as $modelName => $model) {
                    if(is_array($model)) {
                        foreach ($model as $fieldName => $field) {
                            if(!is_array($field) && $field === null) {
                                # preserve null values
                                continue;
                            }
                            if(!is_array($field)) {
                                if(is_array($ignore_keys) && in_array($fieldName, $ignore_keys)) {
                                    $this->data[$modelName][$fieldName] = preg_replace('/\+A[\w]+\-/i','',$field);
                                }
                                else if(is_array($purify_keys) && in_array($fieldName, $purify_keys)) {
                                    #replace this line by the html purifier code
                                    $this->data[$modelName][$fieldName] = $field;
                                } else {
                                    if(is_array($allowed_tags) && count($allowed_tags) != 0) {
                                        #html and dangerous attributes
                                    	$this->data[$modelName][$fieldName] = strip_tags($field, implode(',', $allowed_tags));
                                        $this->data[$modelName][$fieldName] = preg_replace('/\+A[\w]+\-/iDs','', $this->data[$modelName][$fieldName]);
                                        $this->data[$modelName][$fieldName] = preg_replace('/<[^>].*(style|on).*\s*=.*>/iDs',' ', $this->data[$modelName][$fieldName]);
                                    } else {
                                        #html and dangerous attributes
                                    	$this->data[$modelName][$fieldName] = strip_tags($field);
                                        $this->data[$modelName][$fieldName] = str_replace('"', '”', $this->data[$modelName][$fieldName]);
                                        $this->data[$modelName][$fieldName] = preg_replace('/\+A[\w]+\-/iDs','', $this->data[$modelName][$fieldName]);
                                        
                                        #dangerous unicode characters
								        #$this->data[$modelName][$fieldName] = urldecode(preg_replace('/(?:%E(?:2|3)%8(?:0|1)%(?:A|8|9)\w|%EF%BB%BF)|(?:&#(?:65|8)\d{3};?)/i', ' ', urlencode($this->data[$modelName][$fieldName])));
                                        #$this->data[$modelName][$fieldName] = urldecode(preg_replace('/(?:&#(?:65|8)\d{3};?)|(?:&#x(?:fe|20)\w{2};?)/i', ' ', $this->data[$modelName][$fieldName]));
                                    }
                                }
                            } else {
                                /**
                                 * @todo: add some logic here to enable recursive filtering
                                 */
                            }
                        }
                    }
                }
            }
        }
        
        return parent::beforeSave($options);
    }
    
    /**
     * hook from CakePHP
     */
    public function afterSave($created) {
        # reset locale to version from beforeSave()
        if(!is_null($this->old_locale)) {
            $this->old_locale = split(';', $this->old_locale);
            setlocale(LC_ALL, $this->old_locale);
            $this->old_locale = null;
        }
        return parent::afterSave($created);
    }
    
    private function deSanitize($field) {
    	$replacements = array (
			"&",
			"%",
			"<",
			">",
			'"',
			"'",
			"(",
			")",
			"+",
			"-"
		);
		$patterns = array (
			"/\&amp;/",
			"/\&#37;/",
			"/\&lt;/",
			"/\&gt;/",
			"/\&quot;/",
			"/\&#39;/",
			"/\&#40;/",
			"/\&#41;/",
			"/\&#43;/",
			"/\&#45;/"
		);
		
		return preg_replace($patterns, $replacements, $field);
    }    
    
    /**
     * Helper methods for getting data out of a options array
     */
    protected function getValue($options, $key, $default = false) {
        $value = $default;
        if(is_array($options) && isset($options[$key])) {
            $value = $options[$key];
        }
        
        return $value;
    }
}