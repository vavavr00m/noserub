<?php
App::import('Helper', array('Form', 'Html'));

class HelperFactory {
	public static function createFormHelper() {
		$formHelper = new FormHelper();
		$formHelper->Html = new HtmlHelper();
		
		return $formHelper;
	}
}