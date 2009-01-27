<?php

class OmbAccessToken extends AppModel {
	public function deleteByContactId($contact_id) {
		$this->deleteAll(array('contact_id' => $contact_id));
	}
}