<?php

abstract class OmbParam {
	private $value = null;
	
	public function __construct($value) {
		$this->value = $value;
	}
	
	abstract public function getKey();

	public function getValue() {
		return $this->value;
	}
}