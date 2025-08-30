<?php
Class User{
    public static function _info(){

        if(!isset($_SESSION['uid'])){
            echo 'You have logged out. <a href="'.U.'login/">Click Here to Login.</a>';
            exit;
        }

        $id = $_SESSION['uid'];

        // $d = ORM::for_table('sys_users')->find_one($id);
        // Join sys_users with sys_accounts to get branch name
        $d = ORM::for_table('sys_users')
            ->table_alias('u')
            ->select('u.*')
            ->select('a.account', 'branch_name') // assuming 'account' column in sys_accounts stores branch name
            ->left_outer_join('sys_accounts', array('u.branch_id', '=', 'a.id'), 'a')
            ->find_one($id);
        return $d;
    }
}