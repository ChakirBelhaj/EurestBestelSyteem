<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\Login\LoginService;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class LoginController extends Controller
{

    /**
     * Display's the login page
     */
    public function index()
    {
        // Redirect to homepage if already logged in
        if (LoginService::isLoggedIn()) {
            return $this->redirect('/');
        }
        
        return $this->view('frontend/login/index.twig');
    }

    /*
        handles the actual login
     */
    public function postLogin() {
    	$errors = [];
    	$emailValid = v::email()->validate(app()->request->get('email'));

        // Check email field for valid email
    	if (!$emailValid) {
    		$errors[] = 'Email ongeldig';
    	} else {
    		$user = User::where('email', app()->request->get('email'))->first();

            // Check wether a user with that email exists
    		if ($user) {

                // Verify the users password and login if correct
    			if (password_verify(app()->request->get('password'), $user->password)) {
    				LoginService::setLoggedInUserId($user->id);

                    return $this->redirect('/');
    			}	
    			else {
    				$errors[] = 'Wachtwoord incorrect';
    			}
    		}
    		else {
    			$errors[] = 'Account niet gevonden';
    		}
    	}

    	return $this->view('frontend/login/index.twig', ['errors' => $errors]);
    }

    /*
        Handles logging out
     */
    public function logout() {
        LoginService::logout();

        $this->redirect('/login');
    }
}