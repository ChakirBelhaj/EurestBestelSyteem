<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;
use App\Services\Mailer\UserMailer;
use App\Models\User;

class PasswordController extends Controller {

	public function index() {

		// Render the default view (request password reset form)
		return $this->view('frontend/password/index.twig');
	}

	// Called when someone requests a password reset
	public function postRequest() {
		$errors = [];
		$messages = [];

		// Validate the email address
		if (!v::email()->validate(app()->request->get('email'))) {
			$errors[] = 'Email ongeldig';
		} else {

			// Fetch the user from the database
			$user = app('database')->table('users')->select()->where('email', app()->request->get('email'))->first();

			// User found
			if ($user) {

				// Instantiate a user object from the user data
				$user = new User($user);

				// Generate a reset token
				$token = md5(time());

				// Update the user with the new reset token
				$result = app('database')->table('users')->where('email', app()->request->get('email'))->update(['resettoken' => $token]);

				// User successfully updated
				if ($result) {
					$resetUrl = 'http://localhost/password/reset/' . app()->request->get('email') . '/' . $token;

					// Send reset email with generated link
					UserMailer::sendPasswordResetEmail($user, $resetUrl);
				}
			}

			// Let the user know an email may have been sent
			$messages[] = 'Als er een account bij ons geregistreerd is met die e-mail adres ontvang je een email met instructies.';
		}

		// Render the view with the possible messages and or errors
		return $this->view('frontend/password/index.twig', ['errors' => $errors, 'messages' => $messages]);
	}

	// Renders the reset view (where you can enter a new password)
	public function reset() {
		$errors = [];

		// Get the email and reset token from the URL (which was sent in the email)
		$email = app()->getRouteInfo()['email'];
		$resetToken = app()->getRouteInfo()['reset_token'];

		// Try to fetch the user from the database
		$user = app('database')->table('users')->select()->where(['email' => $email, 'resettoken' => $resetToken])->first();

		// The user wasn't found for the email / token combination, return
		if (!$user) {
			return $this->redirect('/password');
		}

		// Render the view with possible messages and or errors
		return $this->view('frontend/password/reset.twig', ['errors' => $errors, 'messages' => [], 'email' => $email, 'reset_token' => $resetToken]);
	}

	// Called when the user resets his or her password
	public function postReset() {
		$messages = [];
		$errors = [];

		// Get the email, token and new password from the POST request
		$email = app()->request->get('email');
		$resetToken = app()->request->get('reset_token');
		$newPassword = app()->request->get('password');

		// Validate that the password meets our requirements
		if (!v::stringType()->length(5)->validate($newPassword)) {
			$errors[] = 'Je wachtwoord voldoet niet aan onze eisen (minimaal 5 karakters)';
		}
		else {

			// Hash the password
			$newPassword = password_hash($newPassword, PASSWORD_BCRYPT);

			// Update the password for the user, remove the resettoken to avoid fraud
			$update = app('database')->table('users')->where(['email' => $email, 'resettoken' => $resetToken])->update(['password' => $newPassword, 'resettoken' => NULL]);

			// User found, password updated
			if ($update) {
				$messages[] = 'Wachtwoord bijgewerkt';
			} else {

				// The user wasn't found or some other error
				$errors[] = 'Er ging iets mis bij het bijwerken van je wachtwoord';
			}
		}

		// Render the view
		return $this->view('frontend/password/reset.twig', ['errors' => $errors, 'messages' => $messages, 'email' => $email, 'reset_token' => $resetToken]);
	}
}