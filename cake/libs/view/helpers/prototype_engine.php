<?php
/**
 * Prototype Engine Helper for JsHelper
 *
 * Provides Prototype specific Javascript for JsHelper. Requires at least
 * Prototype 1.6
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.libs.view.helpers
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Helper', 'Js');

class PrototypeEngineHelper extends JsBaseEngineHelper {
/**
 * Is the current selection a multiple selection? or is it just a single element.
 *
 * @var boolean
 **/
	var $_multiple = false;
/**
 * Option mappings for Prototype
 *
 * @var array
 **/
	var $_optionMap = array(
		'request' => array(
			'async' => 'asynchronous',
			'data' => 'parameters',
			'before' => 'onCreate',
			'success' => 'onSuccess',
			'complete' => 'onComplete',
			'error' => 'onFailure'
		),
		'sortable' => array(
			'start' => 'onStart',
			'sort' => 'onChange',
			'complete' => 'onUpdate',
			'distance' => 'snap',
		),
		'drag' => array(
			'snapGrid' => 'snap',
			'container' => 'constraint',
			'stop' => 'onEnd',
			'start' => 'onStart',
			'drag' => 'onDrag',
		),
		'drop' => array(
			'hover' => 'onHover',
			'drop' => 'onDrop',
			'hoverClass' => 'hoverclass',
		),
		'slider' => array(
			'direction' => 'axis',
			'change' => 'onSlide',
			'complete' => 'onChange',
			'value' => 'sliderValue',
		)
	);

/**
 * Contains a list of callback names -> default arguments.
 *
 * @var array
 **/
	var $_callbackArguments = array(
		'slider' => array(
			'onSlide' => 'value',
			'onChange' => 'value',
		),
		'drag' => array(
			'onStart' => 'event',
			'onDrag' => 'event',
			'change' => 'draggable',
			'onEnd' => 'event',
		),
		'drop' => array(
			'onHover' => 'draggable, droppable, event',
			'onDrop' => 'draggable, droppable, event',
		),
		'request' => array(
			'onCreate' => 'transport',
			'onComplete' => 'transport',
			'onFailure' => 'response, jsonHeader',
			'onRequest' => 'transport',
			'onSuccess' => 'response, jsonHeader'
		),
		'sortable' => array(
			'onStart' => 'element',
			'onChange' => 'element',
			'onUpdate' => 'element',
		),
	);

/**
 * Create javascript selector for a CSS rule
 *
 * @param string $selector The selector that is targeted
 * @return object instance of $this. Allows chained methods.
 **/
	function get($selector) {
		$this->_multiple = false;
		if ($selector == 'window' || $selector == 'document') {
			$this->selection = "$(" . $selector .")";
			return $this;
		}
		if (preg_match('/^#[^\s.]+$/', $selector)) {
			$this->selection = '$("' . substr($selector, 1) . '")';
			return $this;
		}
		$this->_multiple = true;
		$this->selection = '$$("' . $selector . '")';
		return $this;
	}
/**
 * Add an event to the script cache. Operates on the currently selected elements.
 *
 * ### Options
 *
 * - 'wrap' - Whether you want the callback wrapped in an anonymous function. (defaults true)
 * - 'stop' - Whether you want the event to stopped. (defaults true)
 *
 * @param string $type Type of event to bind to the current 946 id
 * @param string $callback The Javascript function you wish to trigger or the function literal
 * @param array $options Options for the event.
 * @return string completed event handler
 **/
	function event($type, $callback, $options = array()) {
		$defaults = array('wrap' => true, 'stop' => true);
		$options = array_merge($defaults, $options);

		$function = 'function (event) {%s}';
		if ($options['wrap'] && $options['stop']) {
			$callback = "event.stop();\n" . $callback;
		}
		if ($options['wrap']) {
			$callback = sprintf($function, $callback);
		}
		$out = $this->selection . ".observe(\"{$type}\", $callback);";
		return $out;
	}
/**
 * Create a domReady event. This is a special event in many libraries
 *
 * @param string $functionBody The code to run on domReady
 * @return string completed domReady method
 **/
	function domReady($functionBody) {
		$this->selection = 'document';
		return $this->event('dom:loaded', $functionBody, array('stop' => false));
	}
/**
 * Create an iteration over the current selection result.
 *
 * @param string $method The method you want to apply to the selection
 * @param string $callback The function body you wish to apply during the iteration.
 * @return string completed iteration
 **/
	function each($callback) {
		return $this->selection . '.each(function (item, index) {' . $callback . '});';
	}
/**
 * Trigger an Effect.
 *
 * #### Note: Effects require Scriptaculous to be loaded.
 *
 * @param string $name The name of the effect to trigger.
 * @param array $options Array of options for the effect.
 * @return string completed string with effect.
 * @see JsBaseEngineHelper::effect()
 **/
	function effect($name, $options = array()) {
		$effect = '';
		$optionString = null;
		if (isset($options['speed'])) {
			if ($options['speed'] == 'fast') {
				$options['duration'] = 0.5;
			} elseif ($options['speed'] == 'slow') {
				$options['duration'] = 2;
			} else {
				$options['duration'] = 1;
			}
			unset($options['speed']);
		}
		if (!empty($options)) {
			$optionString = ', {' . $this->_parseOptions($options) . '}';
		}
		switch ($name) {
			case 'hide':
			case 'show':
				$effect = $this->selection . '.' . $name . '();';
			break;
			case 'slideIn':
			case 'slideOut':
				$name = ($name == 'slideIn') ? 'slideDown' : 'slideUp';
				$effect = 'Effect.' . $name . '(' . $this->selection . $optionString . ');';
			break;
			case 'fadeIn':
			case 'fadeOut':
				$name = ($name == 'fadeIn') ? 'appear' : 'fade';
				$effect = $this->selection . '.' . $name .'(' . substr($optionString, 2) . ');';
			break;
		}
		return $effect;
	}
/**
 * Create an Ajax or Ajax.Updater call.
 *
 * @param mixed $url
 * @param array $options
 * @return string The completed ajax call.
 **/
	function request($url, $options = array()) {
		$url = '"'. $this->url($url) . '"';
		$options = $this->_mapOptions('request', $options);
		$type = '.Request';
		$data = null;
		if (isset($options['type']) && strtolower($options['type']) == 'json') {
			unset($options['type']);
		}
		if (isset($options['update'])) {
			$url = '"' . str_replace('#', '', $options['update']) . '", ' . $url;
			$type = '.Updater';
			unset($options['update'], $options['type']);
		}
		$safe = array();
		if (isset($options['dataExpression'])) {
			$safe[] = 'parameters';
			unset($options['dataExpression']);
		}
		$safe = array_merge($safe, array_keys($this->_callbackArguments['request']));
		$options = $this->_prepareCallbacks('request', $options, $safe);
		$options = $this->_parseOptions($options, $safe);
		if (!empty($options)) {
			$options = ', {' . $options . '}';
		}
		return "var jsRequest = new Ajax$type($url$options);";
	}
/**
 * Create a sortable element.
 *
 * #### Note: Requires scriptaculous to be loaded.
 *
 * @param array $options Array of options for the sortable.
 * @return string Completed sortable script.
 * @see JsHelper::sortable() for options list.
 **/
	function sortable($options = array()) {
		$options = $this->_processOptions('sortable', $options);
		if (!empty($options)) {
			$options = ', {' . $options . '}';
		}
		return 'var jsSortable = Sortable.create(' . $this->selection . $options . ');';
	}
/**
 * Create a Draggable element.
 *
 * #### Note: Requires scriptaculous to be loaded.
 *
 * @param array $options Array of options for the draggable.
 * @return string Completed draggable script.
 * @see JsHelper::draggable() for options list.
 **/
	function drag($options = array()) {
		$options = $this->_processOptions('drag', $options);
		if (!empty($options)) {
			$options = ', {' . $options . '}';
		}
		if ($this->_multiple) {
			return $this->each('new Draggable(item' . $options . ');');
		}
		return 'var jsDrag = new Draggable(' . $this->selection . $options . ');';
	}
/**
 * Create a Droppable element.
 *
 * #### Note: Requires scriptaculous to be loaded.
 *
 * @param array $options Array of options for the droppable.
 * @return string Completed droppable script.
 * @see JsHelper::droppable() for options list.
 **/
	function drop($options = array()) {
		$options = $this->_processOptions('drop', $options);
		if (!empty($options)) {
			$options = ', {' . $options . '}';
		}
		return 'Droppables.add(' . $this->selection . $options . ');';
	}
/**
 * Creates a slider control widget.
 *
 * ### Note: Requires scriptaculous to be loaded.
 *
 * @param array $options Array of options for the slider.
 * @return string Completed slider script.
 * @see JsHelper::slider() for options list.
 **/
	function slider($options = array()) {
		$slider = $this->selection;
		$this->get($options['handle']);
		unset($options['handle']);

		if (isset($options['min']) && isset($options['max'])) {
			$options['range'] = array($options['min'], $options['max']);
			unset($options['min'], $options['max']);
		}
		$optionString = $this->_processOptions('slider', $options);
		if (!empty($optionString)) {
			$optionString = ', {' . $optionString . '}';
		}
		$out = 'var jsSlider = new Control.Slider(' . $this->selection . ', ' . $slider . $optionString . ');';
		$this->selection = $slider;
		return $out;
	}
/**
 * Serialize the form attached to $selector.
 *
 * @param array $options Array of options.
 * @return string Completed serializeForm() snippet
 * @see JsHelper::serializeForm()
 **/
	function serializeForm($options = array()) {
		$options = array_merge(array('isForm' => false, 'inline' => false), $options);
		$selection = $this->selection;
		if (!$options['isForm']) {
			$selection = '$(' . $this->selection . '.form)';
		}
		$method = '.serialize()';
		if (!$options['inline']) {
			$method .= ';';
		}
		return $selection . $method;
	}
}
?>