<?php

namespace App\Controllers;

class AdminController extends Controller
{
    //show error
    public function showError($array){
        return $this->view('frontend/admin/adminpanel.twig', [
            'errors' => $array
        ]);
    }

    public function showErrorEmployee($array){
        return $this->view('frontend/admin/addemployee.twig', [
            'errors' => $array
        ]);
    }

    //show success
    public function showSuccess($array){
        return $this->view('frontend/admin/adminpanel.twig', [
            'success' => $array
        ]);
    }

    public function showSuccessEmployee($array){
        return $this->view('frontend/admin/addemployee.twig', [
            'success' => $array
        ]);
    }
    //shows admin panel
    public function showAdminPanel(){
        return $this->view('frontend/admin/adminpanel.twig');
    }
    //shows add admin form
    public function showAddAdminPanel(){
        return $this->view('frontend/admin/addadmin.twig');
    }
    //shows add employee form
    public function showAddEmployeePanel(){
        return $this->view('frontend/admin/addemployee.twig');
    }

    //posts register
    public function postRegisterAdmin(){
    //declare error and success array
    $errors  = [];
    $success = [];

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
        $data['role_id'] = 4;

        //encryts password variable
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $success[] = "employee is aangemaakt";

        //insert $data with all the user variables into the database
        app('database')->table('users')->insert($data);
        return $this->showSuccess($success);
    }
}
    //posts register employee
    public function postRegisterEmployee(){
        //declare error and success array
        $errors  = [];
        $success = [];

        //checks if email is already in use
        $userNotAvailable =  app('database')->table('users')->select(['id', 'email'])->where('email', app()->request->get('email'))->first();

        //checks if userAvailable = true
        if ($userNotAvailable) {
            $errors[] = 'email is in gebruik';
            return $this->showErrorEmployee($errors);
            //if passwords dont match return error.
        }else if(app()->request->get('password') != app()->request->get('pass2')){
            $errors[] = "passwords do not match";
            return $this->showErrorEmployee($errors);
        } else {
            //puts all the post variables in $data
            $data = request([
                'firstname', 'middlename', 'lastname','email','streetname','housenumber','placeofresidence','zipcode','mobile','password'
            ]);
            $data['role_id'] = 3;

            //encryts password variable
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $success[] = "admin is aangemaakt";

            //insert $data with all the user variables into the database
            app('database')->table('users')->insert($data);
            return $this->showSuccessEmployee($success);
        }
    }

}