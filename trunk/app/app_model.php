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

    private $excludeSanitize = array('Feed');
    
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
        if(!empty ($data)) {
            foreach ($data as $key => $item) {
                if(is_array($item)) {
                    foreach ($item as $model => $attributes) {
                        if(in_array($model, $this->excludeSanitize)) {
                            continue;
                        }                         
                        # de-sanitize for whatever purpose - we have to talk about this!
                        if(is_array($attributes)) {
                            foreach($attributes as $fieldName => $field) {
                                if(!is_array($field) && !empty ($field)) {
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
                                    $field = preg_replace($patterns, $replacements, $field);
                                    $data[$key][$model][$fieldName] = $field;
                                }
                            }
                        }
                    }
                }
            }
        }
       
       # moved from Identity Model to here, so that all the associated
       # afterFinds could be catched, too
        if(is_array($data)) {
            foreach($data as $key => $item) {
                $checkModels = array('WithIdentity', 'Identity');
                foreach($checkModels as $modelName) {
                    if(isset($item[$modelName]['username'])) {
                        $username = Identity::splitUsername($item[$modelName]['username']);
                        $item[$modelName]['local_username'] = $username['local_username'];
                        $item[$modelName]['username']       = $username['username'];
                        $item[$modelName]['namespace']      = $username['namespace'];
                        $item[$modelName]['local']          = $username['local'];
                        $item[$modelName]['name']           = trim($item[$modelName]['firstname'] . ' ' . $item[$modelName]['lastname']);
                        $data[$key] = $item;
                    }
                }
            }
        }
        
        # not quite sure, if this is still neccessary. was copied from
        # a cake 1.1 project
        #
        # fixing bug with expects and belongsTo associations
        # probably caused by joining belongsTo associations cake doesn't reset
        # the bindings of this associations
        foreach ($this->belongsTo as $model => $association) {
            $this->$model->__resetAssociations();
        }
		foreach ($this->hasAndBelongsToMany as $model => $association) {
            $this->$model->__resetAssociations();
        }
        
        return $data;
    }
    
    /**
     * Expects unbindsAll except the given models
     *
     * link: http://bakery.cakephp.org/articles/view/185
     *
     * @return
     * @access public
     */
    public function expects() {
        $models = array ();

        $arguments = func_get_args();

        # flatten arguments (backwards compatibility - array notation)
        foreach ($arguments as $index => $argument) {
            if (is_array($argument)) {
                if (count($argument) > 0) {
                    $arguments = array_merge($arguments, $argument);
                }

                unset ($arguments[$index]);
            }
        }

        if (count($arguments) == 0) {
            # no arguments - only the model itself
            $models[$this->name] = array ();
        } else {
            foreach ($arguments as $argument) {
                # check dot notation
                if (strpos($argument, '.') !== false) {
                    $model = substr($argument, 0, strpos($argument, '.'));
                    $child = substr($argument, strpos($argument, '.') + 1);

                    if ($child == $model) {
                        $models[$model] = array ();
                    } else {
                        $models[$model][] = $child;
                    }
                } else {
                    $models[$this->name][] = $argument;
                }
            }
        }
        foreach ($models as $model => $children) {
            if ($model != $this->name && isset ($this-> $model)) {
                $this-> $model->expects($children);
            }
        }

        if (isset ($models[$this->name])) {
            foreach ($models as $model => $children) {
                if ($model != $this->name) {
                    $models[$this->name][] = $model;
                }
            }
            $models = array_unique($models[$this->name]);

            $unbind = array ();

            $relations = array (
                'belongsTo',
                'hasOne',
                'hasMany',
                'hasAndBelongsToMany'
            );

            foreach ($relations as $relation) {
                if (isset ($this-> $relation)) {
                    foreach ($this-> $relation as $name => $currentModel) {
                        if (!in_array($name, $models)) {
                            $unbind[$relation][] = $name;
                        }
                    }
                }
            }

            if (count($unbind) > 0) {
                $this->unbindModel($unbind);
            }
        }
    }
}