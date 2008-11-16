<?php

class Sex {
	const AVATAR_PATH = '/images/profile/avatar/';
	const UNDEFINED = 0;
	const FEMALE = 1;
	const MALE = 2;

	public static function getImageUrl($sex) {
		if (!in_array($sex, Sex::getSexes())) throw new InvalidArgumentException();
		
		return Sex::constructImageUrl($sex, '.gif');
	}
	
	public static function getSmallImageUrl($sex) {
		if (!in_array($sex, Sex::getSexes())) throw new InvalidArgumentException();
		
		return Sex::constructImageUrl($sex, '-small.gif');
	}
	
	/**
	 * Returns "he", "she", or "he/she".
	 */
	public static function heOrShe($sex) {
		if (!in_array($sex, Sex::getSexes())) throw new InvalidArgumentException();
		
		$pronouns = array(Sex::UNDEFINED => __('he/she', true), 
						  Sex::FEMALE => __('she', true), 
						  Sex::MALE => __('he', true));
		
		return $pronouns[$sex];	
	}
	
	/**
	 * Returns "him", "her", or "him/her".
	 */
	public static function himOrHer($sex) {
		if (!in_array($sex, Sex::getSexes())) throw new InvalidArgumentException();
		
		$pronouns = array(Sex::UNDEFINED => __('him/her', true), 
						  Sex::FEMALE => __('her', true), 
						  Sex::MALE => __('him', true));
		
		return $pronouns[$sex];
	}
	
	/**
	 * Returns "his", "her", or "his/her".
	 */
	public static function hisOrHer($sex) {
		if (!in_array($sex, Sex::getSexes())) throw new InvalidArgumentException();
		
		$pronouns = array(Sex::UNDEFINED => __('his/her', true), 
						  Sex::FEMALE => __('her', true), 
						  Sex::MALE => __('his', true));
		
		return $pronouns[$sex];
	}
	
	private static function constructImageUrl($sex, $suffix) {
		$url = '';
		
		switch ($sex) {
			case Sex::FEMALE: 
				$url = Router::url(Sex::AVATAR_PATH.'female'.$suffix, true);
				break;
			case SEX::MALE: 
				$url = Router::url(Sex::AVATAR_PATH.'male'.$suffix, true);
				break;
			case SEX::UNDEFINED: 
				$url = Router::url(Sex::AVATAR_PATH.'noinfo'.$suffix, true);
				break; 
		}
		
		return $url;
	}
	
	private static function getSexes() {
		return array(Sex::UNDEFINED, Sex::FEMALE, Sex::MALE);
	}
}