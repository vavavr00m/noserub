<?php

class GenericOmbConsumer extends AbstractConsumer {
	public function __construct() {
		parent::__construct(Configure::read('Noserub.full_base_url'), '');
	}
}