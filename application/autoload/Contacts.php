<?php

Class Contacts
{
    public static function options($selected = '')
    {

        $c = ORM::for_table('crm_accounts')->select('id')->select('account')->find_many();
        $options = '';
        if($c){

            foreach($c as $cs){
                $s = '';
                if($cs['id'] == $selected){
                    $s = 'selected';
                }
                $options .= '<option value="'.$cs['id'].'" '.$s.'>'.$cs['account'].'</option>';
            }
        }

        return $options;

    }


    public static function add($data=array()){


        if(isset($data['account'])){

            $account = trim($data['account']);

            if($account == ''){
                return 'Account Name is Required';
            }

            $email = '';
            $phone = '';
            $address = '';
            $city = '';
            $zip = '';
            $gst = '';
            $state = '';
            $country = '';
            $tags = '';
            $company = '';
            $password = '';
            $img = '';
						$balance = '0.00';

            $d = ORM::for_table('crm_accounts')->create();

            $d->account = $data['account'];

            if(isset($data['email']) && trim($data['email']) != ''){

                if(Validator::Email($data['email']) == false){
                    return 'Invalid Email';
                }
                $f = ORM::for_table('crm_accounts')->where('email',$data['email'])->find_one();

                if($f){
                    return 'Email already exist';
                }

                $email = $data['email'];

            }

            if(isset($data['phone'])){
                $phone = $data['phone'];
            }

            if(isset($data['address'])){
                $address = $data['address'];
            }

            if(isset($data['city'])){
                $city = $data['city'];
            }

            if(isset($data['zip'])){
                $zip = $data['zip'];
            }
						
						if(isset($data['gst_no'])){
                $gst = $data['gst_no'];
            }

            if(isset($data['state'])){
                $state = $data['state'];
            }

            if(isset($data['country'])){
                $country = $data['country'];
            }

            if(isset($data['company'])){
                $company = $data['company'];
            }
						
						if(isset($data['balance'])){
                $balance = $data['balance'];
            }


            if(isset($data['password'])){
                $password = $data['password'];
                $password = Password::_crypt($password);
            }

            if(isset($data['tags'])){
                $tags = $data['tags'];
            }

            if(isset($data['img'])){
                $img = $data['img'];
            }



            $d->email = $email;
            $d->phone = $phone;
            $d->address = $address;
            $d->city = $city;
            $d->zip = $zip;
            $d->gst_no = $gst;
            $d->state = $state;
            $d->country = $country;
            $d->tags = $tags;

            //others
            $d->fname = '';
            $d->lname = '';
            $d->company = $company;
            $d->jobtitle = '';
            $d->cid = '0';
            $d->o = '0';
            $d->balance = $balance;
            $d->status = 'Active';
            $d->notes = '';
            $d->password = $password;
            $d->token = '';
            $d->ts = '';
            $d->img = $img;
            $d->web = '';
            $d->facebook = '';
            $d->google = '';
            $d->linkedin = '';

            //
            $d->save();
            $cid = $d->id();

            return $cid;

        }

        else{
            return 'Invalid Data Posted or Data is Null';
        }


    }


    public static function login($email,$password){
        $d = ORM::for_table('crm_accounts')->where('email',$email)->find_one();
        if($d){

            $db_password = $d['password'];

            if(Password::_verify($password,$db_password) == true){

            
                // Seamless one-time migration to bcrypt if needed
                if (Password::needsRehash($db_password)) {
                    try {
                        $d->password = Password::_crypt($password); // rehash SAME password
                        $d->save();
                    } catch (Exception $e) {
                        _log('Password rehash failed for user '.$email.' : '.$e->getMessage(), 'Admin', $d->id);
                        // continue login even if rehash save fails
                    }
                }
                
               $auth_key = Ib_Str::random_string(20).md5(time());

                $d->token = $auth_key;

                $d->save();

                return $auth_key;

            }
            else{
                return false;
            }



        }
        else{
            return false;
        }
    }

    public static function logout_using_token($token){
        $d = ORM::for_table('crm_accounts')->where('token',$token)->find_one();
        if($d){

            $d->token = '';

            $d->save();

            return true;



        }
        else{
            return false;
        }
    }


    public static function details(){

        $d = false;

        if(isset($_COOKIE['ib_ct'])) {

            $ib_ct = $_COOKIE['ib_ct'];

            $d = ORM::for_table('crm_accounts')->where('token',$ib_ct)->find_one();

            if(!$d){
                ib_die('You have logged out. <a href="'.U.'client/login/">Click Here to Login.</a>');
            }


        } else {

            ib_die('You have logged out. <a href="'.U.'client/login/">Click Here to Login.</a>');

        }

        return $d;

    }


    public static function isLogged(){


        if(isset($_COOKIE['ib_ct'])) {

            $ib_ct = $_COOKIE['ib_ct'];

            $d = ORM::for_table('crm_accounts')->where('token',$ib_ct)->find_one();

            if($d){

                r2(U.'client/dashboard/');
            }


        }



    }

    public static function all(){

        $d = ORM::for_table('crm_accounts')->order_by_desc('id')->find_array();

        return $d;

    }


}