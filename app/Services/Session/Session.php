<?php

namespace App\Services\Session;

class Session
{
	/**
	 * Starts the session and handles the flash data
	 */
	public static function init()
	{
        session_start();

		if(!isset($_SESSION['data']))
		{
			$_SESSION['data'] = array();
			$_SESSION['data']['app'] = array();
		}
		if(!isset($_SESSION['flash']))	
		{
			$_SESSION['flash'] = array();
		}
		if(!isset($_SESSION['tmp_flash']))
		{
			$_SESSION['tmp_flash'] = array();
		}

		// Load tmp flash
		$_SESSION['tmp_flash'] = array();

		Session::clearFlash();
	}

	/**
	 * Adds message to the session data
	 * 
	 * @param string 	$key    	Key
	 * @param mixed 	$message 	Data
	 */
	public static function set($key, $message, $segment = "data")
	{
		if(!isset($_SESSION[$segment]))
		{
			$_SESSION[$segment] = array();
		}

		$_SESSION[$segment][$key] = $message;
	}

	/**
	 * Adds message to the flash data
	 * 
	 * @param string 	$key    	Key
	 * @param mixed 	$message 	Data
	 */
	public static function setFlash($key, $message)
	{
		$_SESSION['flash'][$key] = $message;
	}

	/**
	 * Get session data
	 * 
	 * @param  string $key 	Key for the data you want to get from the sessionz
	 * 
	 * @return mixed      	returns session key data
	 */
	public static function get($key, $segment = "data")
	{
		// Check segment exists
		if(!array_key_exists($segment, $_SESSION)) {
			return null;
		}
		// Check 
		if(!array_key_exists($key, $_SESSION[$segment])) {
			return null;
		}

		return $_SESSION[$segment][$key];
	}

	/**
	 * Get flash data
	 * 
	 * @param  string $key 	Key for the data in the session
	 * 
	 * @return mixed     	returns session key data
	 */
	public static function getFlash($key)
	{
		if(!array_key_exists($key, $_SESSION['flash']))
		{
			return null;
		}

		return $_SESSION['flash'][$key];
	}
	
	/**
	 * Set data
	 * 
	 * @return [type] [description]
	 */
	public static function remove($key, $segment = "data")
	{
		unset($_SESSION[$segment][$key]);
	}

	/**
	 * Set flash data
	 * 
	 * @return [type] [description]
	 */
	public static function removeFlash($key)
	{
		unset($_SESSION['flash'][$key]);
	}

	/**
	 * Clear data
	 */
	private function clear($segment = "data")
	{
		$_SESSION[$segment] = array();
	}

	/**
	 * Clear flash
	 */
	private function clearFlash()
	{
		$_SESSION['flash'] = array();
	}
}