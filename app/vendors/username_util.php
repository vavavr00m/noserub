<?php

class UsernameUtil {
	public static function isReservedUsername($username) {
		return in_array($username, split(',', NOSERUB_RESERVED_USERNAMES));
	}
}