<?php
/**
 * Automatic generation of HTML FORMs from given data.
 *
 * Used for scaffolding.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.helpers
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Form helper library.
 *
 * Automatic generation of HTML FORMs from given data.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.view.helpers
 */
class FormHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html');

/**
 * Holds the fields array('field_name' => array('type'=> 'string', 'length'=> 100),
 * primaryKey and validates array('field_name')
 *
 * @access public
 */
	var $fieldset = array();

/**
 * Options used by DateTime fields
 *
 * @var array
 */
	var $__options = array(
		'day' => array(), 'minute' => array(), 'hour' => array(),
		'month' => array(), 'year' => array(), 'meridian' => array()
	);

/**
 * List of fields created, used with secure forms.
 *
 * @var array
 * @access public
 */
	var $fields = array();

/**
 * Defines the type of form being created.  Set by FormHelper::create().
 *
 * @var string
 * @access public
 */
	var $requestType = null;

/**
 * The default model being used for the current form.
 *
 * @var string
 */
	var $defaultModel = null;


/**
 * Persistent default options used by input(). Set by FormHelper::create().
 *
 * @var array
 * @access protected
 */
	var $_inputDefaults = array();

/**
 * Introspects model information and extracts information related
 * to validation, field length and field type. Appends information into
 * $this->fieldset.
 *
 * @return Model Returns a model instance
 * @access protected
 */
	function &_introspectModel($model) {
		$object = null;
		if (is_string($model) && strpos($model, '.') !== false) {
			$path = explode('.', $model);
			$model = end($path);
		}

		if (ClassRegistry::isKeySet($model)) {
			$object =& ClassRegistry::getObject($model);
		}

		if (!empty($object)) {
			$fields = $object->schema();
			foreach ($fields as $key => $value) {
				unset($fields[$key]);
				$fields[$key] = $value;
			}

			if (!empty($object->hasAndBelongsToMany)) {
				foreach ($object->hasAndBelongsToMany as $alias => $assocData) {
					$fields[$alias] = array('type' => 'multiple');
				}
			}
			$validates = array();
			if (!empty($object->validate)) {
				foreach ($object->validate as $validateField => $validateProperties) {
					if (is_array($validateProperties)) {
						$dims = Set::countDim($validateProperties);
						if (($dims == 1 && !isset($validateProperties['required']) || (array_key_exists('required', $validateProperties) && $validateProperties['required'] !== false))) {
							$validates[] = $validateField;
						} elseif ($dims > 1) {
							foreach ($validateProperties as $rule => $validateProp) {
								if (is_array($validateProp) && (array_key_exists('required', $validateProp) && $validateProp['required'] !== false)) {
									$validates[] = $validateField;
								}
							}
						}
					}
				}
			}
			$defaults = array('fields' => array(), 'key' => 'id', 'validates' => array());
			$key = $object->primaryKey;
			$this->fieldset[$object->name] = array_merge($defaults,compact('fields', 'key', 'validates'));
		}

		return $object;
	}

/**
 * Returns an HTML FORM element.
 *
 * #### Options:
 *
 * - `type` Form method defaults to POST
 * - `action`  The Action the form submits to. Can be a string or array,
 * - `url`  The url the form submits to. Can be a string or a url array,
 * - `default`  Allows for the creation of Ajax forms.
 * - `onsubmit` Used in conjunction with 'default' to create ajax forms.
 * - `inputDefaults' set the default $options for FormHelper::input(). Any options that would
 *    be set when using FormHelper::input() can be set here.  Options set with `inputDefaults`
 *    can be overridden when calling input()
 * - `encoding` Set the accept-charset encoding for the form.  Defaults to `Configure::read('App.encoding')`
 *
 * @access public
 * @param string $model The model object which the form is being defined for
 * @param array $options An array of html attributes and options.
 * @return string An formatted opening FORM tag.
 */
	function create($model = null, $options = array()) {
		$created = $id = false;
		$append = '';
		$view =& ClassRegistry::getObject('view');

		if (is_array($model) && empty($options)) {
			$options = $model;
			$model = null;
		} elseif (empty($model) && $model !== false && !empty($this->params['models'])) {
			$model = $this->params['models'][0];
			$this->defaultModel = $this->params['models'][0];
		} elseif (empty($model) && empty($this->params['models'])) {
			$model = false;
		}

		$models = ClassRegistry::keys();
		foreach ($models as $currentModel) {
			if (ClassRegistry::isKeySet($currentModel)) {
				$currentObject =& ClassRegistry::getObject($currentModel);
				if (is_a($currentObject, 'Model') && !empty($currentObject->validationErrors)) {
					$this->validationErrors[Inflector::camelize($currentModel)] =& $currentObject->validationErrors;
				}
			}
		}

		$object =& $this->_introspectModel($model);
		$this->setEntity($model . '.', true);

		if (isset($this->fieldset[$this->model()]['key'])) {
			$data = $this->fieldset[$this->model()];
			$recordExists = (
				isset($this->data[$model]) &&
				!empty($this->data[$model][$data['key']])
			);

			if ($recordExists) {
				$created = true;
				$id = $this->data[$model][$data['key']];
			}
		}

		$options = array_merge(array(
			'type' => ($created && empty($options['action'])) ? 'put' : 'post',
			'action' => null,
			'url' => null,
			'default' => true,
			'encoding' => strtolower(Configure::read('App.encoding')),
			'inputDefaults' => array()),
		$options);
		$this->_inputDefaults = $options['inputDefaults'];
		unset($options['inputDefaults']);

		if (empty($options['url']) || is_array($options['url'])) {
			if (empty($options['url']['controller'])) {
				if (!empty($model) && $model != $this->defaultModel) {
					$options['url']['controller'] = Inflector::underscore(Inflector::pluralize($model));
				} elseif (!empty($this->params['controller'])) {
					$options['url']['controller'] = Inflector::underscore($this->params['controller']);
				}
			}
			if (empty($options['action'])) {
				$options['action'] = $this->params['action'];
			}

			$actionDefaults = array(
				'plugin' => $this->plugin,
				'controller' => $view->viewPath,
				'action' => $options['action'],
				0 => $id
			);
			if (!empty($options['action']) && !isset($options['id'])) {
				$options['id'] = $model . Inflector::camelize($options['action']) . 'Form';
			}
			$options['action'] = array_merge($actionDefaults, (array)$options['url']);
		} elseif (is_string($options['url'])) {
			$options['action'] = $options['url'];
		}
		unset($options['url']);

		switch (strtolower($options['type'])) {
			case 'get':
				$htmlAttributes['method'] = 'get';
			break;
			case 'file':
				$htmlAttributes['enctype'] = 'multipart/form-data';
				$options['type'] = ($created) ? 'put' : 'post';
			case 'post':
			case 'put':
			case 'delete':
				$append .= $this->hidden('_method', array(
					'name' => '_method', 'value' => strtoupper($options['type']), 'id' => null
				));
			default:
				$htmlAttributes['method'] = 'post';
			break;
		}
		$this->requestType = strtolower($options['type']);

		$htmlAttributes['action'] = $this->url($options['action']);
		unset($options['type'], $options['action']);

		if ($options['default'] == false) {
			if (isset($htmlAttributes['onSubmit']) || isset($htmlAttributes['onsubmit'])) {
				$htmlAttributes['onsubmit'] .= ' event.returnValue = false; return false;';
			} else {
				$htmlAttributes['onsubmit'] = 'event.returnValue = false; return false;';
			}
		}

		if (!empty($options['encoding'])) {
			$htmlAttributes['accept-charset'] = $options['encoding'];
			unset($options['encoding']);
		}

		unset($options['default']);
		$htmlAttributes = array_merge($options, $htmlAttributes);

		if (isset($this->params['_Token']) && !empty($this->params['_Token'])) {
			$append .= $this->hidden('_Token.key', array(
				'value' => $this->params['_Token']['key'], 'id' => 'Token' . mt_rand())
			);
		}

		if (!empty($append)) {
			$append = sprintf($this->Html->tags['fieldset'], ' style="display:none;"', $append);
		}

		$this->setEntity($model . '.', true);
		$attributes = $this->_parseAttributes($htmlAttributes, null, '');
		return $this->output(sprintf($this->Html->tags['form'], $attributes)) . $append;
	}

/**
 * Closes an HTML form, cleans up values set by FormHelper::create(), and writes hidden
 * input fields where appropriate.
 *
 * If $options is set a form submit button will be created. Options can be either a string or an array.
 *
 * {{{
 * array usage:
 * array('label' => 'save'); value="save"
 * array('label' => 'save', 'name' => 'Whatever'); value="save" name="Whatever"
 * array('name' => 'Whatever'); value="Submit" name="Whatever"
 * array('label' => 'save', 'name' => 'Whatever', 'div' => 'good') <div class="good"> value="save" name="Whatever"
 * array('label' => 'save', 'name' => 'Whatever', 'div' => array('class' => 'good')); <div class="good"> value="save" name="Whatever"
 * }}}
 *
 * @param mixed $options as a string will use $options as the value of button,
 * @return string a closing FORM tag optional submit button.
 * @access public
 */
	function end($options = null) {
		if (!empty($this->params['models'])) {
			$models = $this->params['models'][0];
		}
		$out = null;
		$submit = null;

		if ($options !== null) {
			$submitOptions = array();
			if (is_string($options)) {
				$submit = $options;
			} else {
				if (isset($options['label'])) {
					$submit = $options['label'];
					unset($options['label']);
				}
				$submitOptions = $options;

				if (!$submit) {
					$submit = __('Submit', true);
				}
			}
			$out .= $this->submit($submit, $submitOptions);
		}
		if (isset($this->params['_Token']) && !empty($this->params['_Token'])) {
			$out .= $this->secure($this->fields);
			$this->fields = array();
		}
		$this->setEntity(null);
		$out .= $this->Html->tags['formend'];

		$view =& ClassRegistry::getObject('view');
		$view->modelScope = false;
		return $this->output($out);
	}

/**
 * Generates a hidden field with a security hash based on the fields used in the form.
 *
 * @param array $fields The list of fields to use when generating the hash
 * @return string A hidden input field with a security hash
 * @access public
 */
	function secure($fields = array()) {
		if (!isset($this->params['_Token']) || empty($this->params['_Token'])) {
			return;
		}
		$out = '<fieldset style="display:none;">';
		$locked = array();

		foreach ($fields as $key => $value) {
			if (!is_int($key)) {
				$locked[$key] = $value;
				unset($fields[$key]);
			}
		}
		sort($fields, SORT_STRING);
		ksort($locked, SORT_STRING);
		$fields += $locked;

		$fields = Security::hash(serialize($fields) . Configure::read('Security.salt'));
		$locked = str_rot13(serialize(array_keys($locked)));

		$out .= $this->hidden('_Token.fields', array(
			'value' => urlencode($fields . ':' . $locked),
			'id' => 'TokenFields' . mt_rand()
		));
		return $out .= '</fieldset>';
	}

/**
 * Determine which fields of a form should be used for hash
 *
 * @param mixed $field Reference to field to be secured
 * @param mixed $value Field value, if value should not be tampered with
 * @access private
 */
	function __secure($field = null, $value = null) {
		if (!$field) {
			$view =& ClassRegistry::getObject('view');
			$field = $view->entity();
		} elseif (is_string($field)) {
			$field = Set::filter(explode('.', $field), true);
		}

		if (!empty($this->params['_Token']['disabledFields'])) {
			foreach ((array)$this->params['_Token']['disabledFields'] as $disabled) {
				$disabled = explode('.', $disabled);
				if (array_values(array_intersect($field, $disabled)) === $disabled) {
					return;
				}
			}
		}
		$field = join('.', $field);
		if (!in_array($field, $this->fields)) {
			if ($value !== null) {
				return $this->fields[$field] = $value;
			}
			$this->fields[] = $field;
		}
	}

/**
 * Returns true if there is an error for the given field, otherwise false
 *
 * @param string $field This should be "Modelname.fieldname"
 * @return boolean If there are errors this method returns true, else false.
 * @access public
 */
	function isFieldError($field) {
		$this->setEntity($field);
		return (bool)$this->tagIsInvalid();
	}

/**
 * Returns a formatted error message for given FORM field, NULL if no errors.
 *
 * Options:
 *
 * - 'escape'  bool  Whether or not to html escape the contents of the error.
 * - 'wrap'  mixed  Whether or not the error message should be wrapped in a div. If a
 *   string, will be used as the HTML tag to use.
 * - 'class'  string  The classname for the error message
 *
 * @param string $field A field name, like "Modelname.fieldname"
 * @param mixed $text Error message or array of $options
 * @param array $options Rendering options for <div /> wrapper tag
 * @return string If there are errors this method returns an error message, otherwise null.
 * @access public
 */
	function error($field, $text = null, $options = array()) {
		$defaults = array('wrap' => true, 'class' => 'error-message', 'escape' => true);
		$options = array_merge($defaults, $options);
		$this->setEntity($field);

		if ($error = $this->tagIsInvalid()) {
			if (is_array($error)) {
				list(,,$field) = explode('.', $field);
				if (isset($error[$field])) {
					$error = $error[$field];
				} else {
					return null;
				}
			}

			if (is_array($text) && is_numeric($error) && $error > 0) {
				$error--;
			}
			if (is_array($text)) {
				$options = array_merge($options, $text);
				$text = isset($text[$error]) ? $text[$error] : null;
				unset($options[$error]);
			}

			if ($text != null) {
				$error = $text;
			} elseif (is_numeric($error)) {
				$error = sprintf(__('Error in field %s', true), Inflector::humanize($this->field()));
			}
			if ($options['escape']) {
				$error = h($error);
				unset($options['escape']);
			}
			if ($options['wrap']) {
				$tag = is_string($options['wrap']) ? $options['wrap'] : 'div';
				unset($options['wrap']);
				return $this->Html->tag($tag, $error, $options);
			} else {
				return $error;
			}
		} else {
			return null;
		}
	}

/**
 * Returns a formatted LABEL element for HTML FORMs.
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param string $text Text that will appear in the label field.
 * @param mixed $options An array of HTML attributes, or a string, to be used as a class name.
 * @return string The formatted LABEL element
 */
	function label($fieldName = null, $text = null, $options = array()) {
		if (empty($fieldName)) {
			$view = ClassRegistry::getObject('view');
			$fieldName = implode('.', $view->entity());
		}

		if ($text === null) {
			if (strpos($fieldName, '.') !== false) {
				$text = array_pop(explode('.', $fieldName));
			} else {
				$text = $fieldName;
			}
			if (substr($text, -3) == '_id') {
				$text = substr($text, 0, strlen($text) - 3);
			}
			$text = __(Inflector::humanize(Inflector::underscore($text)), true);
		}

		if (is_string($options)) {
			$options = array('class' => $options);
		}

		if (isset($options['for'])) {
			$labelFor = $options['for'];
			unset($options['for']);
		} else {
			$labelFor = $this->domId($fieldName);
		}

		return $this->output(sprintf(
			$this->Html->tags['label'],
			$labelFor,
			$this->_parseAttributes($options), $text
		));
	}

/**
 * Generate a set of inputs for `$fields`.  If $fields is null the current model
 * will be used.
 *
 * In addition to controller fields output, `$fields` can be used to control legend
 * and fieldset rendering with the `fieldset` and `legend` keys.
 * `$form->inputs(array('legend' => 'My legend'));` Would generate an input set with
 * a custom legend.  You can customize individual inputs through `$fields` as well.
 *
 * {{{
 *	$form->inputs(array(
 *		'name' => array('label' => 'custom label')
 *	));
 * }}}
 *
 * @param mixed $fields An array of fields to generate inputs for, or null.
 * @param array $blacklist a simple array of fields to skip.
 * @return string Completed form inputs.
 * @access public
 */
	function inputs($fields = null, $blacklist = null) {
		$fieldset = $legend = true;

		if (is_array($fields)) {
			if (array_key_exists('legend', $fields)) {
				$legend = $fields['legend'];
				unset($fields['legend']);
			}

			if (isset($fields['fieldset'])) {
				$fieldset = $fields['fieldset'];
				unset($fields['fieldset']);
			}
		} elseif ($fields !== null) {
			$fieldset = $legend = $fields;
			if (!is_bool($fieldset)) {
				$fieldset = true;
			}
			$fields = array();
		}

		if (empty($fields)) {
			$fields = array_keys($this->fieldset[$this->model()]['fields']);
		}

		if ($legend === true) {
			$actionName = __('New %s', true);
			$isEdit = (
				strpos($this->action, 'update') !== false ||
				strpos($this->action, 'edit') !== false
			);
			if ($isEdit) {
				$actionName = __('Edit %s', true);
			}
			$modelName = Inflector::humanize(Inflector::underscore($this->model()));
			$legend = sprintf($actionName, __($modelName, true));
		}

		$out = null;
		foreach ($fields as $name => $options) {
			if (is_numeric($name) && !is_array($options)) {
				$name = $options;
				$options = array();
			}
			$entity = explode('.', $name);
			$blacklisted = (
				is_array($blacklist) &&
				(in_array($name, $blacklist) || in_array(end($entity), $blacklist))
			);
			if ($blacklisted) {
				continue;
			}
			$out .= $this->input($name, $options);
		}

		if (is_string($fieldset)) {
			$fieldsetClass = sprintf(' class="%s"', $fieldset);
		} else {
			$fieldsetClass = '';
		}

		if ($fieldset && $legend) {
			return sprintf(
				$this->Html->tags['fieldset'],
				$fieldsetClass,
				sprintf($this->Html->tags['legend'], $legend) . $out
			);
		} elseif ($fieldset) {
			return sprintf($this->Html->tags['fieldset'], $fieldsetClass, $out);
		} else {
			return $out;
		}
	}

/**
 * Generates a form input element complete with label and wrapper div
 *
 * Options - See each field type method for more information. Any options that are part of
 * $attributes or $options for the different type methods can be included in $options for input().
 *
 * - `type` - Force the type of widget you want. e.g. `type => 'select'`
 * - `label` - control the label
 * - `div` - control the wrapping div element
 * - `options` - for widgets that take options e.g. radio, select
 * - `error` - control the error message that is produced
 * - `empty` - String or boolean to enable empty select box options.
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $options Each type of input takes different options.
 * @return string Completed form widget
 */
	function input($fieldName, $options = array()) {
		$this->setEntity($fieldName);

		$options = array_merge(
			array('before' => null, 'between' => null, 'after' => null),
			$this->_inputDefaults,
			$options
		);

		$modelKey = $this->model();
		$fieldKey = $this->field();
		if (!isset($this->fieldset[$modelKey])) {
			$this->_introspectModel($modelKey);
		}

		$userType = isset($options['type']) ? true : false;

		if (!$userType) {
			$options['type'] = 'text';
			$fieldDef = array();
			if (isset($options['options'])) {
				$options['type'] = 'select';
			} elseif (in_array($fieldKey, array('psword', 'passwd', 'password'))) {
				$options['type'] = 'password';
			} elseif (isset($this->fieldset[$modelKey]['fields'][$fieldKey])) {
				$fieldDef = $this->fieldset[$modelKey]['fields'][$fieldKey];
				$type = $fieldDef['type'];
				$primaryKey = $this->fieldset[$modelKey]['key'];
			}

			if (isset($type)) {
				$map = array(
					'string'  => 'text',     'datetime'  => 'datetime',
					'boolean' => 'checkbox', 'timestamp' => 'datetime',
					'text'    => 'textarea', 'time'      => 'time',
					'date'    => 'date',     'float'     => 'text'
				);

				if (isset($this->map[$type])) {
					$options['type'] = $this->map[$type];
				} elseif (isset($map[$type])) {
					$options['type'] = $map[$type];
				}
				if ($fieldKey == $primaryKey) {
					$options['type'] = 'hidden';
				}
			}

			if ($modelKey === $fieldKey) {
				$options['type'] = 'select';
				if (!isset($options['multiple'])) {
					$options['multiple'] = 'multiple';
				}
			}
		}
		$types = array('text', 'checkbox', 'radio', 'select');

		if (!isset($options['options']) && in_array($options['type'], $types) && !$userType) {
			$view =& ClassRegistry::getObject('view');
			$varName = Inflector::variable(
				Inflector::pluralize(preg_replace('/_id$/', '', $fieldKey))
			);
			$varOptions = $view->getVar($varName);
			if (is_array($varOptions)) {
				if ($options['type'] !== 'radio') {
					$options['type'] = 'select';
				}
				$options['options'] = $varOptions;
			}
		}

		$autoLength = (!array_key_exists('maxlength', $options) && isset($fieldDef['length']));
		if ($autoLength && $options['type'] == 'text') {
			$options['maxlength'] = $fieldDef['length'];
		}
		if ($autoLength && $fieldDef['type'] == 'float') {
			$options['maxlength'] = array_sum(explode(',', $fieldDef['length']))+1;
		}

		$out = '';
		$div = true;
		$divOptions = array();

		if (array_key_exists('div', $options)) {
			$div = $options['div'];
			unset($options['div']);
		}

		if (!empty($div)) {
			$divOptions['class'] = 'input';
			$divOptions = $this->addClass($divOptions, $options['type']);
			if (is_string($div)) {
				$divOptions['class'] = $div;
			} elseif (is_array($div)) {
				$divOptions = array_merge($divOptions, $div);
			}
			if (
				isset($this->fieldset[$modelKey]) &&
				in_array($fieldKey, $this->fieldset[$modelKey]['validates'])
				) {
				$divOptions = $this->addClass($divOptions, 'required');
			}
			if (!isset($divOptions['tag'])) {
				$divOptions['tag'] = 'div';
			}
		}

		$label = null;
		if (isset($options['label']) && $options['type'] !== 'radio') {
			$label = $options['label'];
			unset($options['label']);
		}

		if ($options['type'] === 'radio') {
			$label = false;
			if (isset($options['options'])) {
				if (is_array($options['options'])) {
					$radioOptions = $options['options'];
				} else {
					$radioOptions = array($options['options']);
				}
				unset($options['options']);
			}
		}

		if ($label !== false) {
			$labelAttributes = $this->domId(array(), 'for');
			if ($options['type'] === 'date' || $options['type'] === 'datetime') {
				if (isset($options['dateFormat']) && $options['dateFormat'] === 'NONE') {
					$labelAttributes['for'] .= 'Hour';
				} else {
					$labelAttributes['for'] .= 'Month';
				}
			} elseif ($options['type'] === 'time') {
				$labelAttributes['for'] .= 'Hour';
			}

			if (is_array($label)) {
				$labelText = null;
				if (isset($label['text'])) {
					$labelText = $label['text'];
					unset($label['text']);
				}
				$labelAttributes = array_merge($labelAttributes, $label);
			} else {
				$labelText = $label;
			}

			if (isset($options['id'])) {
				$labelAttributes = array_merge($labelAttributes, array('for' => $options['id']));
			}
			$out = $this->label($fieldName, $labelText, $labelAttributes);
		}

		$error = null;
		if (isset($options['error'])) {
			$error = $options['error'];
			unset($options['error']);
		}

		$selected = null;
		if (array_key_exists('selected', $options)) {
			$selected = $options['selected'];
			unset($options['selected']);
		}
		if (isset($options['rows']) || isset($options['cols'])) {
			$options['type'] = 'textarea';
		}

		$timeFormat = 12;
		if (isset($options['timeFormat'])) {
			$timeFormat = $options['timeFormat'];
			unset($options['timeFormat']);
		}

		$dateFormat = 'MDY';
		if (isset($options['dateFormat'])) {
			$dateFormat = $options['dateFormat'];
			unset($options['dateFormat']);
		}

		if ($options['type'] === 'datetime' || $options['type'] === 'date' || $options['type'] === 'time' || $options['type'] === 'select') {
			$options += array('empty' => false);
		}

		$type = $options['type'];
		$before  = $options['before'];
		$between = $options['between'];
		$after = $options['after'];
		unset($options['type'], $options['before'], $options['between'], $options['after']);

		switch ($type) {
			case 'hidden':
				$out = $this->hidden($fieldName, $options);
				unset($divOptions);
			break;
			case 'checkbox':
				$out = $before . $this->checkbox($fieldName, $options) . $between . $out;
			break;
			case 'radio':
				$out = $before . $out . $this->radio($fieldName, $radioOptions, $options) . $between;
			break;
			case 'text':
			case 'password':
				$out = $before . $out . $between . $this->{$type}($fieldName, $options);
			break;
			case 'file':
				$out = $before . $out . $between . $this->file($fieldName, $options);
			break;
			case 'select':
				$options = array_merge(array('options' => array()), $options);
				$list = $options['options'];
				unset($options['options']);
				$out = $before . $out . $between . $this->select(
					$fieldName, $list, $selected, $options
				);
			break;
			case 'time':
				$out = $before . $out . $between . $this->dateTime(
					$fieldName, null, $timeFormat, $selected, $options
				);
			break;
			case 'date':
				$out = $before . $out . $between . $this->dateTime(
					$fieldName, $dateFormat, null, $selected, $options
				);
			break;
			case 'datetime':
				$out = $before . $out . $between . $this->dateTime(
					$fieldName, $dateFormat, $timeFormat, $selected, $options
				);
			break;
			case 'textarea':
			default:
				$out = $before . $out . $between . $this->textarea($fieldName, array_merge(
					array('cols' => '30', 'rows' => '6'), $options
				));
			break;
		}

		if ($type != 'hidden') {
			$out .= $after;
			if ($error !== false) {
				$errMsg = $this->error($fieldName, $error);
				if ($errMsg) {
					$out .= $errMsg;
					$divOptions = $this->addClass($divOptions, 'error');
				}
			}
		}
		if (isset($divOptions) && isset($divOptions['tag'])) {
			$tag = $divOptions['tag'];
			unset($divOptions['tag']);
			$out = $this->Html->tag($tag, $out, $divOptions);
		}
		return $out;
	}

/**
 * Creates a checkbox input widget.
 *
 * Options:
 *
 * - `value` - the value of the checkbox
 * - `checked` - boolean indicate that this checkbox is checked.
 * - `hiddenField` - boolean to indicate if you want the results of checkbox() to include
 *    a hidden input with a value of ''.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element
 */
	function checkbox($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options) + array('hiddenField' => true);
		$value = current($this->value());
		$output = "";

		if (empty($options['value'])) {
			$options['value'] = 1;
		} elseif (!empty($value) && $value === $options['value']) {
			$options['checked'] = 'checked';
		}
		if ($options['hiddenField']) {
			$hiddenOptions = array(
				'id' => $options['id'] . '_', 'name' => $options['name'],
				'value' => '0', 'secure' => false
			);
			if (isset($options['disabled']) && $options['disabled'] == true) {
				$hiddenOptions['disabled'] = 'disabled';
			}
			$output = $this->hidden($fieldName, $hiddenOptions);
		}
		unset($options['hiddenField']);

		return $this->output($output . sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		));
	}

/**
 * Creates a set of radio widgets.
 *
 * Attributes:
 *
 * - `separator` - define the string in between the radio buttons
 * - `legend` - control whether or not the widget set has a fieldset & legend
 * - `value` - indicate a value that is should be checked
 * - `label` - boolean to indicate whether or not labels for widgets show be displayed
 * - `hiddenField` - boolean to indicate if you want the results of radio() to include
 *    a hidden input with a value of ''. This is useful for creating radio sets that non-continuous
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Radio button options array.
 * @param array $attributes Array of HTML attributes.
 * @return string
 */
	function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes = $this->_initInputField($fieldName, $attributes);
		$legend = false;

		if (isset($attributes['legend'])) {
			$legend = $attributes['legend'];
			unset($attributes['legend']);
		} elseif (count($options) > 1) {
			$legend = __(Inflector::humanize($this->field()), true);
		}
		$label = true;

		if (isset($attributes['label'])) {
			$label = $attributes['label'];
			unset($attributes['label']);
		}
		$inbetween = null;

		if (isset($attributes['separator'])) {
			$inbetween = $attributes['separator'];
			unset($attributes['separator']);
		}

		if (isset($attributes['value'])) {
			$value = $attributes['value'];
		} else {
			$value =  $this->value($fieldName);
		}
		$out = array();

		$hiddenField = isset($attributes['hiddenField']) ? $attributes['hiddenField'] : true;
		unset($attributes['hiddenField']);

		foreach ($options as $optValue => $optTitle) {
			$optionsHere = array('value' => $optValue);

			if (isset($value) && $optValue == $value) {
				$optionsHere['checked'] = 'checked';
			}
			$parsedOptions = $this->_parseAttributes(
				array_merge($attributes, $optionsHere),
				array('name', 'type', 'id'), '', ' '
			);
			$tagName = Inflector::camelize(
				$attributes['id'] . '_' . Inflector::underscore($optValue)
			);

			if ($label) {
				$optTitle =  sprintf($this->Html->tags['label'], $tagName, null, $optTitle);
			}
			$out[] =  sprintf(
				$this->Html->tags['radio'], $attributes['name'],
				$tagName, $parsedOptions, $optTitle
			);
		}
		$hidden = null;

		if ($hiddenField) {
			if (!isset($value) || $value === '') {
				$hidden = $this->hidden($fieldName, array(
					'id' => $attributes['id'] . '_', 'value' => '', 'name' => $attributes['name']
				));
			}
		}
		$out = $hidden . join($inbetween, $out);

		if ($legend) {
			$out = sprintf(
				$this->Html->tags['fieldset'], '',
				sprintf($this->Html->tags['legend'], $legend) . $out
			);
		}
		return $this->output($out);
	}

/**
 * Creates a text input widget.
 *
 * @param string $fieldName Name of a field, in the form "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element
 */
	function text($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, array_merge(
			array('type' => 'text'), $options
		));
		return $this->output(sprintf(
			$this->Html->tags['input'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		));
	}

/**
 * Creates a password input widget.
 *
 * @param string $fieldName Name of a field, like in the form "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string
 */
	function password($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options);
		return $this->output(sprintf(
			$this->Html->tags['password'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		));
	}

/**
 * Creates a textarea widget.
 *
 * @param string $fieldName Name of a field, in the form "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element
 */
	function textarea($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options);
		$value = null;

		if (array_key_exists('value', $options)) {
			$value = $options['value'];
			if (!array_key_exists('escape', $options) || $options['escape'] !== false) {
				$value = h($value);
			}
			unset($options['value']);
		}
		return $this->output(sprintf(
			$this->Html->tags['textarea'],
			$options['name'],
			$this->_parseAttributes($options, array('type', 'name'), null, ' '),
			$value
		));
	}

/**
 * Creates a hidden input field.
 *
 * @param string $fieldName Name of a field, in the form"Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string
 * @access public
 */
	function hidden($fieldName, $options = array()) {
		$secure = true;

		if (isset($options['secure'])) {
			$secure = $options['secure'];
			unset($options['secure']);
		}
		$options = $this->_initInputField($fieldName, array_merge(
			$options, array('secure' => false)
		));
		$model = $this->model();

		if ($fieldName !== '_method' && $model !== '_Token' && $secure) {
			$this->__secure(null, '' . $options['value']);
		}

		return $this->output(sprintf(
			$this->Html->tags['hidden'],
			$options['name'],
			$this->_parseAttributes($options, array('name', 'class'), '', ' ')
		));
	}

/**
 * Creates file input widget.
 *
 * @param string $fieldName Name of a field, in the form "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string
 * @access public
 */
	function file($fieldName, $options = array()) {
		$options = array_merge($options, array('secure' => false));
		$options = $this->_initInputField($fieldName, $options);
		$view =& ClassRegistry::getObject('view');
		$field = $view->entity();

		foreach (array('name', 'type', 'tmp_name', 'error', 'size') as $suffix) {
			$this->__secure(array_merge($field, array($suffix)));
		}

		$attributes = $this->_parseAttributes($options, array('name'), '', ' ');
		return $this->output(sprintf($this->Html->tags['file'], $options['name'], $attributes));
	}

/**
 * Creates a <button> tag.
 *
 * Options:
 *
 * - `escape` - HTML entity encode the $title of the button. Defaults to false.
 *
 * @param string $title  The button's caption. Not automatically HTML encoded
 * @param array $options Array of options and HTML attributes.
 * @return string A HTML button tag.
 * @access public
 */
	function button($title, $options = array()) {
		$options += array('type' => 'submit', 'escape' => false);
		if ($options['escape']) {
			$title = h($title);
		}
		return $this->output(sprintf(
			$this->Html->tags['button'],
			$options['type'],
			$this->_parseAttributes($options, array('type'), '', ' '),
			$title
		));
	}

/**
 * Creates a submit button element.
 *
 * @param string $caption The label appearing on the button OR if string contains :// or the
 *  extension .jpg, .jpe, .jpeg, .gif, .png use an image if the extension
 *  exists, AND the first character is /, image is relative to webroot,
 *  OR if the first character is not /, image is relative to webroot/img.
 * @param array $options
 * @return string A HTML submit button
 */
	function submit($caption = null, $options = array()) {
		if (!$caption) {
			$caption = __('Submit', true);
		}
		$out = null;
		$div = true;

		if (isset($options['div'])) {
			$div = $options['div'];
			unset($options['div']);
		}
		$options += array('type' => 'submit', 'before' => null, 'after' => null);
		$divOptions = array('tag' => 'div');

		if ($div === true) {
			$divOptions['class'] = 'submit';
		} elseif ($div === false) {
			unset($divOptions);
		} elseif (is_string($div)) {
			$divOptions['class'] = $div;
		} elseif (is_array($div)) {
			$divOptions = array_merge(array('class' => 'submit', 'tag' => 'div'), $div);
		}

		$before = $options['before'];
		$after = $options['after'];
		unset($options['before'], $options['after']);

		if (strpos($caption, '://') !== false) {
			unset($options['type']);
			$out .=  $this->output($before . sprintf(
				$this->Html->tags['submitimage'],
				$caption,
				$this->_parseAttributes($options, null, '', ' ')
			) . $after);
		} elseif (preg_match('/\.(jpg|jpe|jpeg|gif|png|ico)$/', $caption)) {
			unset($options['type']);
			if ($caption{0} !== '/') {
				$url = $this->webroot(IMAGES_URL . $caption);
			} else {
				$caption = trim($caption, '/');
				$url = $this->webroot($caption);
			}
			$out .= $this->output($before . sprintf(
				$this->Html->tags['submitimage'],
				$url,
				$this->_parseAttributes($options, null, '', ' ')
			) . $after);
		} else {
			$options['value'] = $caption;
			$out .= $this->output($before . sprintf(
				$this->Html->tags['submit'],
				$this->_parseAttributes($options, null, '', ' ')
			). $after);
		}

		if (isset($divOptions)) {
			$tag = $divOptions['tag'];
			unset($divOptions['tag']);
			$out = $this->Html->tag($tag, $out, $divOptions);
		}
		return $out;
	}

/**
 * Returns a formatted SELECT element.
 *
 * Attributes:
 *
 * - `showParents` - If included in the array and set to true, an additional option element
 *   will be added for the parent of each option group.
 * - `multiple` - show a multiple select box.  If set to 'checkbox' multiple checkboxes will be
 *   created instead.
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param array $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
 *    SELECT element
 * @param mixed $selected The option selected by default.  If null, the default value
 *   from POST data will be used when available.
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted SELECT element
 */
	function select($fieldName, $options = array(), $selected = null, $attributes = array()) {
		$select = array();
		$showParents = false;
		$escapeOptions = true;
		$style = null;
		$tag = null;
		$showEmpty = '';

		if (isset($attributes['escape'])) {
			$escapeOptions = $attributes['escape'];
			unset($attributes['escape']);
		}
		if (isset($attributes['empty'])) {
			$showEmpty = $attributes['empty'];
			unset($attributes['empty']);
		}
		$attributes = $this->_initInputField($fieldName, array_merge(
			(array)$attributes, array('secure' => false)
		));

		if (is_string($options) && isset($this->__options[$options])) {
			$options = $this->__generateOptions($options);
		} elseif (!is_array($options)) {
			$options = array();
		}
		if (isset($attributes['type'])) {
			unset($attributes['type']);
		}
		if (in_array('showParents', $attributes)) {
			$showParents = true;
			unset($attributes['showParents']);
		}

		if (!isset($selected)) {
			$selected = $attributes['value'];
		}

		if (isset($attributes) && array_key_exists('multiple', $attributes)) {
			$style = ($attributes['multiple'] === 'checkbox') ? 'checkbox' : null;
			$template = ($style) ? 'checkboxmultiplestart' : 'selectmultiplestart';
			$tag = $this->Html->tags[$template];
			$select[] = $this->hidden(null, array('value' => '', 'id' => null, 'secure' => false));
		} else {
			$tag = $this->Html->tags['selectstart'];
		}

		if (!empty($tag) || isset($template)) {
			$this->__secure();
			$select[] = sprintf($tag, $attributes['name'], $this->_parseAttributes(
				$attributes, array('name', 'value'))
			);
		}
		$emptyMulti = (
			$showEmpty !== null && $showEmpty !== false && !(
				empty($showEmpty) && (isset($attributes) &&
				array_key_exists('multiple', $attributes))
			)
		);

		if ($emptyMulti) {
			$showEmpty = ($showEmpty === true) ? '' : $showEmpty;
			$options = array_reverse($options, true);
			$options[''] = $showEmpty;
			$options = array_reverse($options, true);
		}

		$select = array_merge($select, $this->__selectOptions(
			array_reverse($options, true),
			$selected,
			array(),
			$showParents,
			array('escape' => $escapeOptions, 'style' => $style)
		));

		$template = ($style == 'checkbox') ? 'checkboxmultipleend' : 'selectend';
		$select[] = $this->Html->tags[$template];
		return $this->output(implode("\n", $select));
	}

/**
 * Returns a SELECT element for days.
 *
 * Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $selected Option which is selected.
 * @param array $attributes HTML attributes for the select element
 * @return string
 */
	function day($fieldName, $selected = null, $attributes = array()) {
		$attributes += array('empty' => true);
		$selected = $this->__dateTimeSelected('day', $fieldName, $selected, $attributes);

		if (strlen($selected) > 2) {
			$selected = date('d', strtotime($selected));
		} elseif ($selected === false) {
			$selected = null;
		}
		return $this->select($fieldName . ".day", $this->__generateOptions('day'), $selected, $attributes);
	}

/**
 * Returns a SELECT element for years
 *
 * Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param integer $minYear First year in sequence
 * @param integer $maxYear Last year in sequence
 * @param string $selected Option which is selected.
 * @param array $attributes Attribute array for the select elements.
 * @return string Completed year select input
 */
	function year($fieldName, $minYear = null, $maxYear = null, $selected = null, $attributes = array()) {
		$attributes += array('empty' => true);
		if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$selected = $year;
			} else {
				if (empty($value)) {
					if (!$attributes['empty'] && !$maxYear) {
						$selected = 'now';

					} elseif (!$attributes['empty'] && $maxYear && !$selected) {
						$selected = $maxYear;
					}
				} else {
					$selected = $value;
				}
			}
		}

		if (strlen($selected) > 4 || $selected === 'now') {
			$selected = date('Y', strtotime($selected));
		} elseif ($selected === false) {
			$selected = null;
		}
		$yearOptions = array('min' => $minYear, 'max' => $maxYear);
		return $this->select(
			$fieldName . ".year", $this->__generateOptions('year', $yearOptions),
			$selected, $attributes
		);
	}

/**
 * Returns a SELECT element for months.
 *
 * Attributes:
 *
 * - `monthNames` - If false, 2 digit numbers will be used instead of text.
 *   If a array, the given array will be used.
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $selected Option which is selected.
 * @param array $attributes Attributes for the select element
 * @return string
 */
	function month($fieldName, $selected = null, $attributes = array()) {
		$attributes += array('empty' => true);
		$selected = $this->__dateTimeSelected('month', $fieldName, $selected, $attributes);

		if (strlen($selected) > 2) {
			$selected = date('m', strtotime($selected));
		} elseif ($selected === false) {
			$selected = null;
		}
		$defaults = array('monthNames' => true);
		$attributes = array_merge($defaults, (array) $attributes);
		$monthNames = $attributes['monthNames'];
		unset($attributes['monthNames']);

		return $this->select(
			$fieldName . ".month",
			$this->__generateOptions('month', array('monthNames' => $monthNames)),
			$selected, $attributes
		);
	}

/**
 * Returns a SELECT element for hours.
 *
 * Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param boolean $format24Hours True for 24 hours format
 * @param string $selected Option which is selected.
 * @param array $attributes List of HTML attributes
 * @return string Completed hour select input
 */
	function hour($fieldName, $format24Hours = false, $selected = null, $attributes = array()) {
		$attributes += array('empty' => true);
		$selected = $this->__dateTimeSelected('hour', $fieldName, $selected, $attributes);

		if (strlen($selected) > 2) {
			if ($format24Hours) {
				$selected = date('H', strtotime($selected));
			} else {
				$selected = date('g', strtotime($selected));
			}
		} elseif ($selected === false) {
			$selected = null;
		}
		return $this->select(
			$fieldName . ".hour",
			$this->__generateOptions($format24Hours ? 'hour24' : 'hour'),
			$selected, $attributes
		);
	}

/**
 * Returns a SELECT element for minutes.
 *
 * Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $selected Option which is selected.
 * @param string $attributes Array of Attributes
 * @return string Completed minute select input
 */
	function minute($fieldName, $selected = null, $attributes = array()) {
		$attributes += array('empty' => true);
		$selected = $this->__dateTimeSelected('minute', $fieldName, $selected, $attributes);

		if (strlen($selected) > 2) {
			$selected = date('i', strtotime($selected));
		} elseif ($selected === false) {
			$selected = null;
		}
		$minuteOptions = array();

		if (isset($attributes['interval'])) {
			$minuteOptions['interval'] = $attributes['interval'];
			unset($attributes['interval']);
		}
		return $this->select(
			$fieldName . ".min", $this->__generateOptions('minute', $minuteOptions),
			$selected, $attributes
		);
	}

/**
 * Selects values for dateTime selects.
 *
 * @param string $select Name of element field. ex. 'day'
 * @param string $fieldName Name of fieldName being generated ex. Model.created
 * @param mixed $selected The current selected value.
 * @param array $attributes Array of attributes, must contain 'empty' key.
 * @return string Currently selected value.
 */
	function __dateTimeSelected($select, $fieldName, $selected, $attributes) {
		if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
			if (is_array($value) && isset($value[$select])) {
				$selected = $value[$select];
			} else {
				if (empty($value)) {
					if (!$attributes['empty']) {
						$selected = 'now';
					}
				} else {
					$selected = $value;
				}
			}
		}
		return $selected;
	}

/**
 * Returns a SELECT element for AM or PM.
 *
 * Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $selected Option which is selected.
 * @param string $attributes Array of Attributes
 * @param bool $showEmpty Show/Hide an empty option
 * @return string Completed meridian select input
 */
	function meridian($fieldName, $selected = null, $attributes = array()) {
		$attributes += array('empty' => true);
		if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$selected = $meridian;
			} else {
				if (empty($value)) {
					if (!$attribues['empty']) {
						$selected = date('a');
					}
				} else {
					$selected = date('a', strtotime($value));
				}
			}
		}

		if ($selected === false) {
			$selected = null;
		}
		return $this->select(
			$fieldName . ".meridian", $this->__generateOptions('meridian'),
			$selected, $attributes
		);
	}

/**
 * Returns a set of SELECT elements for a full datetime setup: day, month and year, and then time.
 *
 * Attributes:
 *
 * - `monthNames` If false, 2 digit numbers will be used instead of text.
 *   If a array, the given array will be used.
 * - `minYear` The lowest year to use in the year select
 * - `maxYear` The maximum year to use in the year select
 * - `interval` The interval for the minutes select. Defaults to 1
 * - `separator` The contents of the string between select elements. Defaults to '-'
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $dateFormat DMY, MDY, YMD.
 * @param string $timeFormat 12, 24.
 * @param string $selected Option which is selected.
 * @param string $attributes array of Attributes
 * @return string The HTML formatted OPTION element
 */
	function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $selected = null, $attributes = array()) {
		$attributes += array('empty' => true);
		$year = $month = $day = $hour = $min = $meridian = null;

		if (empty($selected)) {
			$selected = $this->value($fieldName);
		}

		if ($selected === null && $attributes['empty'] != true) {
			$selected = time();
		}

		if (!empty($selected)) {
			if (is_array($selected)) {
				extract($selected);
			} else {
				if (is_numeric($selected)) {
					$selected = strftime('%Y-%m-%d %H:%M:%S', $selected);
				}
				$meridian = 'am';
				$pos = strpos($selected, '-');
				if ($pos !== false) {
					$date = explode('-', $selected);
					$days = explode(' ', $date[2]);
					$day = $days[0];
					$month = $date[1];
					$year = $date[0];
				} else {
					$days[1] = $selected;
				}

				if (!empty($timeFormat)) {
					$time = explode(':', $days[1]);
					$check = str_replace(':', '', $days[1]);

					if (($check > 115959) && $timeFormat == '12') {
						$time[0] = $time[0] - 12;
						$meridian = 'pm';
					} elseif ($time[0] == '00' && $timeFormat == '12') {
						$time[0] = 12;
					} elseif ($time[0] > 12) {
						$meridian = 'pm';
					}
					if ($time[0] == 0 && $timeFormat == '12') {
						$time[0] = 12;
					}
					$hour = $time[0];
					$min = $time[1];
				}
			}
		}

		$elements = array('Day','Month','Year','Hour','Minute','Meridian');
		$defaults = array(
			'minYear' => null, 'maxYear' => null, 'separator' => '-',
			'interval' => 1, 'monthNames' => true
		);
		$attributes = array_merge($defaults, (array) $attributes);
		if (isset($attributes['minuteInterval'])) {
			$attributes['interval'] = $attributes['minuteInterval'];
			unset($attributes['minuteInterval']);
		}
		$minYear = $attributes['minYear'];
		$maxYear = $attributes['maxYear'];
		$separator = $attributes['separator'];
		$interval = $attributes['interval'];
		$monthNames = $attributes['monthNames'];
		$attributes = array_diff_key($attributes, $defaults);

		if (isset($attributes['id'])) {
			if (is_string($attributes['id'])) {
				// build out an array version
				foreach ($elements as $element) {
					$selectAttrName = 'select' . $element . 'Attr';
					${$selectAttrName} = $attributes;
					${$selectAttrName}['id'] = $attributes['id'] . $element;
				}
			} elseif (is_array($attributes['id'])) {
				// check for missing ones and build selectAttr for each element
				foreach ($elements as $element) {
					$selectAttrName = 'select' . $element . 'Attr';
					${$selectAttrName} = $attributes;
					${$selectAttrName}['id'] = $attributes['id'][strtolower($element)];
				}
			}
		} else {
			// build the selectAttrName with empty id's to pass
			foreach ($elements as $element) {
				$selectAttrName = 'select' . $element . 'Attr';
				${$selectAttrName} = $attributes;
			}
		}

		$selects = array();
		foreach (preg_split('//', $dateFormat, -1, PREG_SPLIT_NO_EMPTY) as $char) {
			switch ($char) {
				case 'Y':
					$selects[] = $this->year(
						$fieldName, $minYear, $maxYear, $year, $selectYearAttr
					);
				break;
				case 'M':
					$selectMonthAttr['monthNames'] = $monthNames;
					$selects[] = $this->month($fieldName, $month, $selectMonthAttr);
				break;
				case 'D':
					$selects[] = $this->day($fieldName, $day, $selectDayAttr);
				break;
			}
		}
		$opt = implode($separator, $selects);

		if (!empty($interval) && $interval > 1 && !empty($min)) {
			$min = round($min * (1 / $interval)) * $interval;
		}
		$selectMinuteAttr['interval'] = $interval;
		switch ($timeFormat) {
			case '24':
				$opt .= $this->hour($fieldName, true, $hour, $selectHourAttr) . ':' .
				$this->minute($fieldName, $min, $selectMinuteAttr);
			break;
			case '12':
				$opt .= $this->hour($fieldName, false, $hour, $selectHourAttr) . ':' .
				$this->minute($fieldName, $min, $selectMinuteAttr) . ' ' .
				$this->meridian($fieldName, $meridian, $selectMeridianAttr);
			break;
			default:
				$opt .= '';
			break;
		}
		return $opt;
	}

/**
 * Gets the input field name for the current tag
 *
 * @param array $options
 * @param string $key
 * @return array
 */
	function __name($options = array(), $field = null, $key = 'name') {
		if ($this->requestType == 'get') {
			if ($options === null) {
				$options = array();
			} elseif (is_string($options)) {
				$field = $options;
				$options = 0;
			}

			if (!empty($field)) {
				$this->setEntity($field);
			}

			if (is_array($options) && isset($options[$key])) {
				return $options;
			}
			$name = $this->field();

			if (is_array($options)) {
				$options[$key] = $name;
				return $options;
			} else {
				return $name;
			}
		}
		return parent::__name($options, $field, $key);
	}

/**
 * Returns an array of formatted OPTION/OPTGROUP elements
 * @access private
 * @return array
 */
	function __selectOptions($elements = array(), $selected = null, $parents = array(), $showParents = null, $attributes = array()) {
		$select = array();
		$attributes = array_merge(array('escape' => true, 'style' => null), $attributes);
		$selectedIsEmpty = ($selected === '' || $selected === null);
		$selectedIsArray = is_array($selected);

		foreach ($elements as $name => $title) {
			$htmlOptions = array();
			if (is_array($title) && (!isset($title['name']) || !isset($title['value']))) {
				if (!empty($name)) {
					if ($attributes['style'] === 'checkbox') {
						$select[] = $this->Html->tags['fieldsetend'];
					} else {
						$select[] = $this->Html->tags['optiongroupend'];
					}
					$parents[] = $name;
				}
				$select = array_merge($select, $this->__selectOptions(
					$title, $selected, $parents, $showParents, $attributes
				));

				if (!empty($name)) {
					if ($attributes['style'] === 'checkbox') {
						$select[] = sprintf($this->Html->tags['fieldsetstart'], $name);
					} else {
						$select[] = sprintf($this->Html->tags['optiongroup'], $name, '');
					}
				}
				$name = null;
			} elseif (is_array($title)) {
				$htmlOptions = $title;
				$name = $title['value'];
				$title = $title['name'];
				unset($htmlOptions['name'], $htmlOptions['value']);
			}

			if ($name !== null) {
				if ((!$selectedIsEmpty && (string)$selected == (string)$name) || ($selectedIsArray && in_array($name, $selected))) {
					if ($attributes['style'] === 'checkbox') {
						$htmlOptions['checked'] = true;
					} else {
						$htmlOptions['selected'] = 'selected';
					}
				}

				if ($showParents || (!in_array($title, $parents))) {
					$title = ($attributes['escape']) ? h($title) : $title;

					if ($attributes['style'] === 'checkbox') {
						$htmlOptions['value'] = $name;

						$tagName = Inflector::camelize(
							$this->model() . '_' . $this->field().'_'.Inflector::underscore($name)
						);
						$htmlOptions['id'] = $tagName;
						$label = array('for' => $tagName);

						if (isset($htmlOptions['checked']) && $htmlOptions['checked'] === true) {
							$label['class'] = 'selected';
						}

						list($name) = array_values($this->__name());

						if (empty($attributes['class'])) {
							$attributes['class'] = 'checkbox';
						}
						$label = $this->label(null, $title, $label);
						$item = sprintf(
							$this->Html->tags['checkboxmultiple'], $name,
							$this->_parseAttributes($htmlOptions)
						);
						$select[] = $this->Html->div($attributes['class'], $item . $label);
					} else {
						$select[] = sprintf(
							$this->Html->tags['selectoption'],
							$name, $this->_parseAttributes($htmlOptions), $title
						);
					}
				}
			}
		}

		return array_reverse($select, true);
	}

/**
 * Generates option lists for common <select /> menus
 * @access private
 */
	function __generateOptions($name, $options = array()) {
		if (!empty($this->options[$name])) {
			return $this->options[$name];
		}
		$data = array();

		switch ($name) {
			case 'minute':
				if (isset($options['interval'])) {
					$interval = $options['interval'];
				} else {
					$interval = 1;
				}
				$i = 0;
				while ($i < 60) {
					$data[sprintf('%02d', $i)] = sprintf('%02d', $i);
					$i += $interval;
				}
			break;
			case 'hour':
				for ($i = 1; $i <= 12; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'hour24':
				for ($i = 0; $i <= 23; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'meridian':
				$data = array('am' => 'am', 'pm' => 'pm');
			break;
			case 'day':
				$min = 1;
				$max = 31;

				if (isset($options['min'])) {
					$min = $options['min'];
				}
				if (isset($options['max'])) {
					$max = $options['max'];
				}

				for ($i = $min; $i <= $max; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'month':
				if ($options['monthNames'] === true) {
					$data['01'] = __('January', true);
					$data['02'] = __('February', true);
					$data['03'] = __('March', true);
					$data['04'] = __('April', true);
					$data['05'] = __('May', true);
					$data['06'] = __('June', true);
					$data['07'] = __('July', true);
					$data['08'] = __('August', true);
					$data['09'] = __('September', true);
					$data['10'] = __('October', true);
					$data['11'] = __('November', true);
					$data['12'] = __('December', true);
				} else if (is_array($options['monthNames'])) {
					$data = $options['monthNames'];
				} else {
					for ($m = 1; $m <= 12; $m++) {
						$data[sprintf("%02s", $m)] = strftime("%m", mktime(1, 1, 1, $m, 1, 1999));
					}
				}
			break;
			case 'year':
				$current = intval(date('Y'));

				if (!isset($options['min'])) {
					$min = $current - 20;
				} else {
					$min = $options['min'];
				}

				if (!isset($options['max'])) {
					$max = $current + 20;
				} else {
					$max = $options['max'];
				}
				if ($min > $max) {
					list($min, $max) = array($max, $min);
				}
				for ($i = $min; $i <= $max; $i++) {
					$data[$i] = $i;
				}
				$data = array_reverse($data, true);
			break;
		}
		$this->__options[$name] = $data;
		return $this->__options[$name];
	}

/**
 * Sets field defaults and adds field to form security input hash
 *
 * Options
 *
 *  - `secure` - boolean whether or not the the field should be added to the security fields.
 *
 * @param string $field
 * @param array $options
 * @return array
 * @access protected
 */
	function _initInputField($field, $options = array()) {
		if (isset($options['secure'])) {
			$secure = $options['secure'];
			unset($options['secure']);
		} else {
			$secure = (isset($this->params['_Token']) && !empty($this->params['_Token']));
		}
		$result = parent::_initInputField($field, $options);

		if ($secure) {
			$this->__secure();
		}
		return $result;
	}
}

?>