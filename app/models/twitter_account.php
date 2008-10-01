<?php

class TwitterAccount extends AppModel {
	public $belongsTo = array('Identity');
	
	public function update($identity_id, $data) {
		$id = $this->field('id', array('identity_id' => $identity_id));
		
		if ($id && $data[$this->name]['username'] == '') {
			$this->delete($id);
		} else {
			$data[$this->name]['id'] = $id ? $id : '';
			$data[$this->name]['identity_id'] = $identity_id;
			$this->save($data);
		}
	}
}