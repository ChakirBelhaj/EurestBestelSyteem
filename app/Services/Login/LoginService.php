<?php

namespace App\Services\Login;

use App\Services\Session\Session;
use App\Models\User;

/*
	Responsible for handling user login and sessions
 */
class LoginService	{
	
	// Session keys
	const kIsLoggedIn = 'is_logged_in';
	const kLoggedInUserId = 'logged_in_user_id';

	/*
		Returns a boolean on wether the user is logged in or not
	 */
	public static function isLoggedIn() {
		return Session::get(self::kIsLoggedIn, 'login');
	}

	/*
		Sets the currently logged in user
	 */
	public static function setLoggedInUserId($userid) {
		Session::set(self::kIsLoggedIn, true, 'login');
		Session::set(self::kLoggedInUserId, $userid, 'login');
	}

	/*
		Get's the current user in a App\Models\User object, or false when no user
	 */
	public static function getCurrentUser() {
		if (!self::isLoggedIn()) {
			return false;
		}

		$uid = Session::get(self::kLoggedInUserId, 'login');

		$user = User::find($uid);

		if ($user) {
		    return $user;
        }

		return false;
	}

	/*
		Cleans up the session and logs the user out
	 */
	public static function logout() {
		Session::set(self::kIsLoggedIn, false, 'login');
		Session::set(self::kLoggedInUserId, NULL, 'login');
	}
}