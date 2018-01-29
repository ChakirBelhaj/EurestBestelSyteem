<?php

namespace App\Services\GateKeeper;

use App\Services\Redirect;
use App\Services\Session\Session;
use App\Services\Login\LoginService;

class GateKeeper
{
	private $guards = array();
	private $errors = array();
	private $settings = array();
	private $roles = array();
	private $availible_guards = array(
		'login','role'
	);
	private $default_settings = array(
		'login' => true,
		'role' => 'admin',
		'roleStrict' => false
	);

	/**
	 * GateKeeper constructor
	 */
	public function __construct()
	{
		// Set default settings
		$this->settings = $this->default_settings;

		// Set default guards
		$this->addGuards($this->default_settings);

		// Get all roles from the database
		$roles = app('database')->table('roles')->select(['id','name'])->get();

		// Load all roles
		foreach($roles as $key => $value)
		{
			$this->roles[$value->id] = $value->name;
		}
	}

	/**
	 * Adds all given guards and check guard values
	 */
	public function handle()
	{
		foreach($this->guards as $key => $value)
		{
			$this->checkGuard($key, $value);
		}

		// Check every guard was true
		if(!empty($this->errors))
		{
			app()->resolve('messenger')->CreateError($this->errors[0]);

			Redirect::to('/');
		}
	}

	/**
	 * Adds guards to the GateKeeper, for checking during the handle()
	 *
	 * @param  array 	$guards 	Guards you want to check
	 */
	public function addGuards($guards)
	{
		foreach($guards as $guard => $value)
		{
			if(in_array($guard, $this->availible_guards))
			{
				$this->addGuard($guard,$value);
				$this->settings[$guard] = $value;
			}
			elseif(array_key_exists($guard, $this->settings))
			{
				$this->settings[$guard] = $value;
			}
			else
			{
				$this->errors[] = 'Unknown guard/settings found: '. $guard .'/'.$value;
			}
		}
	}

	/**
	 * Add guard to guards array
	 *
	 * @param  string 		$guard 	The guard
	 * @param  string|bool 	$value 	The value of the guard
	 */
	public function addGuard($guard, $value)
	{
		$this->guards[$guard] = $value;
	}

	/**
	 * Calls the correct guard function to check the guard value
	 *
	 * @param  string 		$guard 	The guard
	 * @param  string|bool 	$value 	The value of the guard
	 *
	 * @return bool        			Returns true if all guards return true, otherwise false
	 */
	public function checkGuard($guard, $value)
	{
		switch($guard)
		{
			case 'login':
				if($this->checkLogin($value) !== true)
				{
					$this->errors[] = 'You need to be logged in to access the requested page!';
				}
			break;
			case 'role':

				if($this->settings['login'] === true && LoginService::isLoggedIn() === true) 
				{
					if($this->checkRole($value, $this->settings['roleStrict']) !== true)
					{
						$this->errors[] = 'You don\'t have sufficient permission to view the requested page';
					}
				}
			break;
		}
	}

	/**
	 * Checks if the user is logged in
	 *
	 * @return bool 	Returns true if the user is logged in
	 */
	private function checkLogin()
	{	
		// Check user needs to be logged in
		if($this->settings['login'] === true) {
			return LoginService::isLoggedIn();
		} else {
			return true;
		}
	}

	/**
	 * Checks if the user has the correct role or higher
	 *
	 * @param  string $role   	The role the user needs to have
	 * @param  bool   $strict 	If higher role is allowed
	 *
	 * @return bool         	Returns true if the user has the correct or higher role
	 */
	private function checkRole($role, $strict)
	{
		// Fetch user role
		$roleID  = app('database')->table('users')->select(['role_id'])->where('id', LoginService::getCurrentUser()->id)->first()->role_id;

		// Check role exists
		if(in_array($role, $this->roles))
		{
			if($strict === true)
			{
				return $role == $this->roles[$roleID];
			}
			else
			{
				// Turn the needed role as a string into the ID
				$neededRole = array_search($role, $this->roles);

				// Check wether our current role ID is higher or equal to the needed role ID, if so allow access.
				return $roleID >= $neededRole;
			}
		}
		else
		{
			// Role not found!
			return false;
		}
	}

	private function getAllRoles()
	{
		$roles  = app('database')->table('roles')->select(['role_id'])->fetchAll();
	}

	private function getUserRole()
	{
		// Check user role is equal or higher than the required role
		if($strict === true) {
			if($roleID === $this->roles[$role]) {
				return true;
			}
		} else {
			if($roleID >= $this->roles[$role]) {
				return true;
			}
		}

		return false;
	}
}