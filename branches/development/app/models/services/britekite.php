<?php
class BritekiteService extends AbstractService {
	

	public function detectService($url) {
		return $this->extractUsername($url, array('#brightkite.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://brightkite.com/people/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://brightkite.com/people/' . $username . '/friends', '/<a href="\/people\/.*"/simU', '/Next È<\/a>/iU', '?page=');
	}
	
	public function getContent($feeditem) {

		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://brightkite.com/people/'.$username.'/objects.rss';
	}
	
}