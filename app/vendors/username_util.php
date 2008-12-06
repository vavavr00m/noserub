<?php

class UsernameUtil {
	public static function isReservedUsername($username) {
		return in_array($username, split(',', NOSERUB_RESERVED_USERNAMES));
	}
	
	/**
     * Sanitizes non-namespace containing usernames.
     * This is used eg. when adding new contacts from
     * flickr, where usernames can be '0909ds7@N01'.
     * There, the @ is not allowed, so I want to sanitize
     * them, before giving them to the user as selection
     * for using as a real contact username.
     */
	public static function sanitizeUsername($username) {
		$username = str_replace('ä', 'ae', $username);
        $username = str_replace('ö', 'oe', $username);
        $username = str_replace('ü', 'ue', $username);
        $username = str_replace('ß', 'ss', $username);
        $username = str_replace('Ä', 'Ae', $username);
        $username = str_replace('Ö', 'Oe', $username);
        $username = str_replace('Ü', 'Ue', $username);
        $username = str_replace(' ', '-',  $username);
        
        $username = preg_replace('/[^\w\s.-]/', null, $username);
        return $username;
	}
}