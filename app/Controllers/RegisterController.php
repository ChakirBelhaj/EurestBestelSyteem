<?php
/**
 * Created by PhpStorm.
 * User: Chakir Belhaj
 * Date: 9/26/2017
 * Time: 11:43 AM
 */

namespace App\Controllers;

use Respect\Validation\Validator as v;

class RegisterController extends Controller
{

    //show register form
    public function showRegisterForm()
    {
        return $this->view('frontend/register/index.twig');
    }
    //show error
    public function showError($array){
        return $this->view('frontend/register/index.twig', [
            'errors' => $array
        ]);
    }
    //show success
    public function showSuccess($array){
        return $this->view('frontend/register/index.twig', [
            'success' => $array
        ]);
    }
    //posts register form
    public function postRegister() {
        //declare error and success array
        $errors = [];
        $success = [];
        //gets email from post
        $email = app()->request->get('email');

        //checks if email is already in use
        $userNotAvailable =  app('database')->table('users')->select(['id', 'email'])->where('email', app()->request->get('email'))->first();
        //checks if userAvailable = true
        if ($userNotAvailable) {
            $errors[] = 'email is in gebruik';
            return $this->showError($errors);
            //if passwords dont match return error.
        }else if(app()->request->get('password') != app()->request->get('pass2')){
            $errors[] = "passwords do not match";
            return $this->showError($errors);
        } else {

            //puts all the post variables in $data
            $data = request([
                'firstname', 'middlename', 'lastname','email','streetname','housenumber','placeofresidence','zipcode','mobile','password'
            ]);

            //encryts password variable
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);


            //checks if $email contains '@student.windesheim.nl' or @docent.windesheim.nl
            if (strpos($email, '@student.windesheim.nl') !== false) {
                $data['role_id'] = 1;
                $success[] = "account is aangemaakt";
                app('database')->table('users')->insert($data);
                return $this->showSuccess($success);
            }else if (strpos($email, '@docent.windesheim.nl') !== false){
                $data['role_id'] = 2;
                $success[] = "account is aangemaakt";
                //insert $data with all the user variables into the database
                app('database')->table('users')->insert($data);
                return $this->showSuccess($success);
            }else{
                //runes error if $email does not contain '@student.windesheim.nl' or @docent.windesheim.nl
                $errors[] = "email adres moet van windesheim zijn";
                return $this->showError($errors);
            }
        }
    }
}