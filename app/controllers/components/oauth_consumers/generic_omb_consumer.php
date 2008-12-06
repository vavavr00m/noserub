<?php

class GenericOmbConsumer extends AbstractConsumer {
	public function __construct() {
		parent::__construct(Configure::read('NoseRub.full_base_url'), '');
	}
}