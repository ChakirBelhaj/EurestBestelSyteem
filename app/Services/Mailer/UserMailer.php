<?php

namespace App\Services\Mailer;

use \App\Models\User;
use \App\Models\Order;

use SendGrid\Email;
use SendGrid\Content;
use SendGrid\Mail;
use SendGrid\Attachment;

/*
	Responsible for sending the user emails
 */
class UserMailer {

	/*
		Send's an account activation email
	 */
	public static function sendActivationEmail($uid) {
        
        // Get the user for the given user ID from the database
        $user = app('database')->table('users')->select(['email', 'firstname', 'middlename', 'lastname'])->where('id', $uid)->first();

		// Check if the user actually exists
		if (! $user) {
			return false;
		}

        $user = new \App\Models\User($user);

		// Render the contents of the email using Twig
		$content = app('renderer')->render('email/activation/index.twig', ['user' => $user, 'activate_url' => 'https://google.com']);

		// Get an instance of the Mailer service
		$mailer = app('mailer');

		// Create the email
		$mail = self::createNewEmail($user, 'Activeer je Eurest account', $content);

		// Send the email with Sendgrid
		$result = $mailer->client->mail()->send()->post($mail);
		
		// Return true if response == 202 (Accepted)
		return $result->statusCode() == 202;
	}

	/*
		Sends a password reset email
	 */
	public static function sendPasswordResetEmail(User $user, $resetUrl) {
		// Render the contents of the email using Twig
		$content = app('renderer')->render('email/password/index.twig', ['user' => $user, 'reset_url' => $resetUrl]);

		// Get an instance of the Mailer service
		$mailer = app('mailer');

		$mail = self::createNewEmail($user, 'Wijzig je Eurest account wachtwoord', $content);

		// Send the email with Sendgrid
		$result = $mailer->client->mail()->send()->post($mail);
		
		// Return true if response == 202 (Accepted)
		return $result->statusCode() == 202;
	}

	/*
		Sends an email when the order is ready
	 */
	public static function sendOrderReadyEmail(User $user, Order $order) {

		// If the user doesn't want emails, return
		if (!$user->preference_order_ready_emails) {
			return true;
		}

		// Render the contents of the email using Twig
		$content = app('renderer')->render('email/order/ready.twig', ['user' => $user, 'order' => $order]);

		// Get an instance of the Mailer service
		$mailer = app('mailer');

		$mail = self::createNewEmail($user, 'Je bestelling is klaar om opgehaald te worden', $content);

		// Send the email with Sendgrid
		$result = $mailer->client->mail()->send()->post($mail);
		
		// Return true if response == 202 (Accepted)
		return $result->statusCode() == 202;
	}

	/*
		Sends an email when the order is ready
	 */
	public static function sendOrderCanceledEmail(User $user, Order $order) {

		// If the user doesn't want emails, return
		if (!$user->preference_order_ready_emails) {
			return true;
		}

		// Render the contents of the email using Twig
		$content = app('renderer')->render('email/order/canceled.twig', ['user' => $user, 'order' => $order]);

		// Get an instance of the Mailer service
		$mailer = app('mailer');

		$mail = self::createNewEmail($user, 'Je bestelling is geannuleerd', $content);

		// Send the email with Sendgrid
		$result = $mailer->client->mail()->send()->post($mail);
		
		// Return true if response == 202 (Accepted)
		return $result->statusCode() == 202;
	}

	/*
		Sends an order confirmation email when the users' order has been placed
	 */
	public static function sendOrderConfirmationEmail(User $user, $order, $invoice) {

		// If the user doesn't want emails, return
		if (!$user->preference_invoice_emails) {
			return true;
		}

		// Render the contents of the email using Twig
		$content = app('renderer')->render('email/order/confirmation.twig', ['user' => $user, 'order' => $order]);

		// Get an instance of the Mailer service
		$mailer = app('mailer');

		$mail = self::createNewEmail($user, 'Bevestiging van uw bestelling bij Eurest', $content);

		// Add the invoice as attachment
		$attachment = new Attachment();
		$attachment->setContent(base64_encode($invoice));
		$attachment->setType("application/pdf");
		$attachment->setDisposition("attachment");
		$attachment->setFilename('Eurest factuur #' . $order->id);
		$mail->addAttachment($attachment);

		// Send the email with Sendgrid
		$result = $mailer->client->mail()->send()->post($mail);
		
		// Return true if response == 202 (Accepted)
		return $result->statusCode() == 202;
	}

	/*
		Helper method that creates an email skeleton
	 */
	private static function createNewEmail(User $user, $subject, $content) {

		// Create the email
		$from = new Email('Eurest', 'info@eurest.nl');
		$to = new Email($user->fullName(), $user->email);
		$content = new Content('text/html', $content);

		return new Mail($from, $subject, $to, $content);
	}
}