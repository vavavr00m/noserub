<?php

class GenericOmbConsumer extends AbstractConsumer {
	public function __construct() {
		parent::__construct(Context::read('network.url'), '');
	}
}