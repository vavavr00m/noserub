<?php

class Sex {
	const AVATAR_PATH = '/images/profile/avatar/';

	/**
	 * @param $sex int value, 0: undefined, 1: female, 2: male
	 */
	public static function getImageUrl($sex, $smallSize = false) {
		if ($sex < 0 || $sex > 2) throw new InvalidArgumentException();
		
		$suffix = '.gif';
		
		if ($smallSize) {
			$suffix = '-small'.$suffix;
		}
		
		$url = '';
		
		switch ($sex) {
			case 1: 
				$url = Router::url(Sex::AVATAR_PATH.'female'.$suffix, true);
				break;
			case 2: 
				$url = Router::url(Sex::AVATAR_PATH.'male'.$suffix, true);
				break;
			default: 
				$url = Router::url(Sex::AVATAR_PATH.'noinfo'.$suffix, true); 
		}
		
		return $url;
	}
	
	/**
	 * Returns "he", "she", or "he/she".
	 * @param $sex int value, 0: undefined, 1: female, 2: male
	 */
	public static function heOrShe($sex) {
		if ($sex < 0 || $sex > 2) throw new InvalidArgumentException();
		
		$pronouns = array('he/she', 'she', 'he');
		
		return $pronouns[$sex];	
	}
	
	/**
	 * Returns "him", "her", or "him/her".
	 * @param $sex int value, 0: undefined, 1: female, 2: male
	 */
	public static function himOrHer($sex) {
		if ($sex < 0 || $sex > 2) throw new InvalidArgumentException();
		
		$pronouns = array('him/her', 'her', 'him');
		
		return $pronouns[$sex];
	}
	
	/**
	 * Returns "his", "her", or "his/her".
	 * @param $sex int value, 0: undefined, 1: female, 2: male
	 */
	public static function hisOrHer($sex) {
		if ($sex < 0 || $sex > 2) throw new InvalidArgumentException();
		
		$pronouns = array('his/her', 'her', 'his');
		
		return $pronouns[$sex];
	}
}
?>