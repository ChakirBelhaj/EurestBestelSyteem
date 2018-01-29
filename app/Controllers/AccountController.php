<?php

namespace App\Controllers;

use App\Services\Login\LoginService;
use App\Services\Redirect;

class AccountController extends Controller
{
    private $userID;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Get userID
        $this->userId = LoginService::getCurrentUser()->id;
    }

    /**
     * Load the change password form
     */
    public function passwordChange()
    {
        return $this->view('frontend/account/changepassword.twig');
    }

    /**
     * Call the change password function and redirects back
     */
    public function postPasswordChange()
    {
        $this->changepassword();
        Redirect::back();
    }

    /**
     * Loads user profile data and shows profile page
     */
    public function showProfile()
    {
        //
        $data = app('database')->table('users')->select()->where('id', $this->userId)->first();

        //
        return $this->view('frontend/account/index.twig', [
            'data' => $data
        ]);
    }

    /**
     * Shows form to change the user data
     */
    public function showEditProfile()
    {
        //
        $old_data = app('database')->table('users')->select()->where('id', $this->userId)->first();
        
        //
        return $this->view('frontend/account/changeprofile.twig', [
            'data' => $old_data
        ]);
    }

    /**
     * Changes the user profile details
     */
    public function postEditProfile()
    {
        // Gets user information form database
        $old_data = app('database')->table('users')->select()->where('id', $this->userId)->first();

        // Checks if email is already in use
        $userAvailable =  app('database')->table('users')->select(['id', 'email'])->where('email', app()->request->get('email'))->first();

        // Check if email is not already being used
        if(app()->request->get('email') != $old_data->email and $userAvailable == true)
        {
            // Show error message
            app()->resolve('messenger')->createError('That email is already being used!');

            return $this->view('frontend/account/changeprofile.twig', [
                'data' => $old_data
            ]);
        }
        else
        {
            // Puts all the post variables in $postData
            $postData = request([
                'id','firstname', 'middlename', 'lastname', 'email', 'streetname', 'housenumber', 'placeofresidence', 'zipcode', 'mobile', 'role_idrole'
            ]);

            // Insert $data with all the user variables into the database
            app('database')->table('users')->where('id',$this->userId)->update([
                'firstname'         => $postData['firstname'],
                'middlename'        => $postData['middlename'],
                'lastname'          => $postData['lastname'],
                'email'             => $postData['email'],
                'streetname'        => $postData['streetname'],
                'housenumber'       => $postData['housenumber'],
                'placeofresidence'  => $postData['placeofresidence'],
                'zipcode'           => $postData['zipcode'],
                'mobile'            => $postData['mobile'],
                'role_id'           => $postData['role_idrole']
            ]);

            // Gets user information form database
            $newData = app('database')->table('users')->select()->where('id', $this->userId)->first();

            // Show success message
            app()->resolve('messenger')->createMessage("gegevens zijn gewijzigd");
        }
        
        Redirect::back();
    }

    /**
     * Changes the password of the user
     * 
     * @return boolean      returns true if the password has been changed, otherwise false
     */
    public function changePassword()
    {
        // Request data from POST
        $data = request([
            'nieuwPassword',
            'confirmNieuwPassword',
            'oldPassword'
        ]);
        
        // Gets oldpassword from database
        $oldPassword = app('database')->table('users')->select('id','password')->where('id', $this->userId)->first();
        
        // Checks if old posted password is not matching the password in the database.
        if(! password_verify($data['oldPassword'], $oldPassword->password))
        {
            // Show error message
            app()->resolve('messenger')->createError('Oude wachtwoord is niet correct!');
            return false;
        }
        else if($data['nieuwPassword']  != $data['confirmNieuwPassword'])
        {
            // Show error message
            app()->resolve('messenger')->createError('Nieuw Password Does Not Match The Confirmed Password');
            return false;
        }
        else
        {
            // Hashes the new password
            $hashedNieuwPassword = password_hash($data['nieuwPassword'], PASSWORD_BCRYPT);

            // Update the password in the database
            app('database')->table('users')->where('id',$this->userId)->update(['password' => $hashedNieuwPassword]);

            app()->resolve('messenger')->createMessage('wachtwoord is gewijzigd');
            return true;
        }
    }

    /*
        Allows the user to view and edit preferences
     */
    public function preferences() {
        return $this->view('frontend/account/preferences/index.twig');
    }

    /*
        Handles preference changes and updates it in the database
     */
    public function postPreferences() {
        $values = [
            'preference_invoice_emails' => intval(!empty(app()->request->get('preference_invoice_emails'))),
            'preference_order_ready_emails' => intval(!empty(app()->request->get('preference_order_ready_emails')))
        ];
        
        if (app('database')->table('users')->update($values)) {
            message('Je voorkeuren zijn opgeslagen');
        }

        return Redirect::back();
    }
}