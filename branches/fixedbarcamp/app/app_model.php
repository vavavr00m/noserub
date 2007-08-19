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