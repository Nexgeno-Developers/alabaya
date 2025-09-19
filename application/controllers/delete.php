<?php
// *************************************************************************
// *                                                                       *
// * iBilling -  Accounting, Billing Software                              *
// * Copyright (c) Sadia Sharmin. All Rights Reserved                      *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: sadiasharmin3139@gmail.com                                                *
// * Website: http://www.sadiasharmin.com                                  *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.                              *
// * If you Purchased from Codecanyon, Please read the full License from   *
// * here- http://codecanyon.net/licenses/standard                         *
// *                                                                       *
// *************************************************************************
_auth();
$ui->assign('_application_menu', 'accounts');
$ui->assign('_title', $_L['Delete'].'- '. $config['CompanyName']);
$action = $routes['1'];
$user = User::_info();
switch ($action) {
/*
    case 'crm-user':
    $id = $routes['2'];
    $id = str_replace('uid','',$id);
    $d = ORM::for_table('crm_accounts')->find_one($id);
    if($d){
        $username = $d->account;
        //delete all activity
        $x = ORM::for_table('sys_activity')->where('cid',$id)->delete_many();
        $x = ORM::for_table('sys_invoices')->where('userid',$id)->delete_many();
        #todo update payer and payee
        $d->delete();
        _log('Contact Deleted: '.$username,'Admin',$user['id']);

        $gid = route(3);

        if(!$gid){
            r2(U.'contacts/list/','s',$_L['Contact Deleted Successfully']);
        }
        else{
            r2(U.'contacts/find_by_group/'.$gid.'/','s',$_L['Contact Deleted Successfully']);
        }
    }
    else{
        echo 'contact not found';
    }
    break;  	
*/
    case 'crm-user':
        $id = $routes['2'];
        $id = str_replace('uid','',$id);
        $d = ORM::for_table('crm_accounts')->find_one($id);

        if(!$d){
            echo json_encode(['success'=>false, 'message'=>'Contact not found']);
            exit;
        }

        // Collect counts
        $counts = [
            'Invoices'       => ORM::for_table('sys_invoices')->where('userid', $id)->count(),
            'Invoice Items'  => ORM::for_table('sys_invoiceitems')->where('userid', $id)->count(),
            'Transactions'   => ORM::for_table('sys_transactions')
                                ->where_any_is([
                                    ['vendor_id' => $id],
                                    ['payerid'  => $id],
                                    ['payeeid'  => $id]
                                ])->count(),
            'Invoice Allocation'    => ORM::for_table('invoice_alocation')->where('employee_id', $id)->count(),
            'Timesheets'     => ORM::for_table('crm_timesheet')->where('employee_id', $id)->count()
        ];

        // Build list of blocking records
        $blocking = [];
        foreach($counts as $type => $count){
            if($count > 0){
                $blocking[] = $type;
            }
        }

        if(!empty($blocking)){
            $message = 'Cannot delete this contact. Linked record(s) exist: ' . implode(', ', $blocking) . '.';
            echo json_encode(['success'=>false, 'message'=>$message]);
            exit;
        }


        // Delete all activity
        ORM::for_table('sys_activity')->where('cid',$id)->delete_many();

        $username = $d->account;
        $d->delete();

        _log('Contact Deleted: '.$username,'Admin',$user['id']);

        echo json_encode([
            'success'=>true,
            'message'=>'Contact Deleted Successfully'
        ]);

    break;

    case 'domain-n-hosting':
    $id = $routes['2'];
    $id = str_replace('uid','',$id);
    $d = ORM::for_table('crm_domain_n_hosting')->find_one($id);
    if($d){
        $username = $d->domain_name;
//delete all activity
        $x = ORM::for_table('crm_domain_n_hosting')->where('id',$id)->delete_many();
        #todo update payer and payee
        $d->delete();
        _log('Domain and Hosting Deleted: '.$username,'Admin',$user['id']);

        $gid = route(3);

        if(!$gid){
            r2(U.'domain_n_hosting/list/','s',$_L['Domain and Hosting Deleted Successfully']);
        }
        else{
            r2(U.'contacts/find_by_group/'.$gid.'/','s',$_L['Contact Deleted Successfully']);
        }



    }
    else{
        echo 'contact not found';
    }
    break;

    case 'ps':
    $id = $routes['2'];
    $data_filter = $routes['3'];
    $id = str_replace('pid','',$id);
    $d = ORM::for_table('sys_items')->find_one($id);
    if($d){
        $type = $d['type'];
        $r = 'ps/s-list/&product_type='.$data_filter;
        if($type == 'Product'){
            $r = 'ps/p-list/&product_type='.$data_filter;
        }
        _log($type.' Deleted: '.$d['name'].' [ID: '.$d['id'].']','Admin',$user['id']);

        unlink($d->product_image);
        
        $d->delete();

        $y = ORM::for_table('sys_items_stock')->where('item_id', $id)->delete_many();
        $z = ORM::for_table('sys_items_stock')->where('parent_item_id', $id)->delete_many();        

        r2(U.$r,'s', $type. ' ' .$_L['Deleted Successfully']);

    }
    else{
        echo 'not found';
    }
    break;

    case 'design':
        $id = $routes['2'];
        $id = str_replace('pid','',$id);
        $d = ORM::for_table('sys_designs')->find_one($id);
        if($d)
        {
            foreach(json_decode($d->image, true) as $img)
            {
                unlink($img);
            }
               
            $d->delete();
            r2(U.'manage/list-design','s', $type. ' ' .$_L['Deleted Successfully']);
        }
        else
        {
            echo 'not found';
        }
    break;


    case 'invoice':
        $id = $routes['2'];
        $id = str_replace('iid','',$id);
        $d = ORM::for_table('sys_invoices')->find_one($id);
        if($d){
//delete all invoice items
            $x = ORM::for_table('sys_invoiceitems')->where('invoiceid',$id)->delete_many();

            $y = ORM::for_table('sys_items_stock')->where('invoice_id', $id)->delete_many();

            // Delete related invoice allocations
            ORM::for_table('invoice_alocation')->where('invoice_id', $id)->delete_many();
            
            $d->delete();
            r2(U.'invoices/list','s','Invoice Deleted Successfully');

        }
        else{
            echo 'Invoice not found';
        }
        break;
case 'proforma':
        $id = $routes['2'];
        $id = str_replace('iid','',$id);
        $d = ORM::for_table('sys_performa')->find_one($id);
        if($d){
//delete all invoice items
            $x = ORM::for_table('sys_performaitems')->where('invoiceid',$id)->delete_many();

            $d->delete();
            r2(U.'invoices/list-proforma','s','Proforma Deleted Successfully');

        }
        else{
            echo 'Proforma not found';
        }
        break;

    case 'quote':
        $id = $routes['2'];
        $id = str_replace('iid','',$id);
        $d = ORM::for_table('sys_quotes')->find_one($id);
        if($d){
//delete all invoice items
            $x = ORM::for_table('sys_quoteitems')->where('qid',$id)->delete_many();

            $d->delete();
            r2(U.'quotes/list/','s',$_L['Quote Deleted Successfully']);

        }
        else{
            echo 'Invoice not found';
        }
        break;

    case 'tags':
        $id = $routes['2'];
        $id = str_replace('iid','',$id);
        $d = ORM::for_table('sys_tags')->find_one($id);
        if($d){
//delete all invoice items


            $d->delete();
            r2(U.'settings/tags','s',$_L['Tag Deleted Successfully']);

        }
        else{
            echo 'Invoice not found';
        }
        break;

    case 'tax':
        $id = $routes['2'];
        $id = str_replace('t','',$id);
        $d = ORM::for_table('sys_tax')->find_one($id);
        if($d){

            $d->delete();
            r2(U.'tax/list/','s',$_L['TAX Deleted Successfully']);

        }
        else{
            echo 'TAX not found';
        }
        break;


    case 'customfield':

        $id = $routes[2];
        $id = str_replace('d','',$id);

        $d = ORM::for_table('crm_customfields')->find_one($id);
        if($d){

            $d->delete();
            r2(U.'settings/customfields/','s',$_L['Custom Field Deleted Successfully']);

        }
        else{
            echo 'Custom Field Not found';
        }

        break;

    case 'crm-group':

        //

        $id = $routes[2];
        $id = str_replace('g','',$id);
        $d = ORM::for_table('crm_groups')->find_one($id);
        if($d){

            // find all contacts with this group

            $gname = $d->gname;

           ORM::execute("update crm_accounts set gid=0, gname='' where gid=$id");


            $d->delete();

            _log('Group Deleted: '.$gname,'Admin',$user['id']);
            r2(U.'contacts/groups/','s',$_L['Group Deleted Successfully']);

        }
        else{
            echo 'contact not found';
        }

        break;





    case 'currency':

        $id = route(2);
        $id = str_replace('c','',$id);

        $currency = Model::factory('Models_Currency');

        $c = $currency->find_one($id);

        if($c){

            if($c->cname == $config['home_currency']){

                r2(U.'settings/currencies/','e','You Can\'t Delete Home Currency');

            }


            // check currency is using


            $invoice = Model::factory('Models_Invoice');

            $check = $invoice->where('currency',$id)->find_one();

            if($check){

                r2(U.'settings/currencies/','e','This Currency is in use, You Can\'t Delete.');

            }

            $c->delete();


            r2(U.'settings/currencies/','s','Currency Deleted Successfully.');

        }


        break;


    case 'company':

        $id = route(2);
        $id = str_replace('c','',$id);

        $company = Model::factory('Models_Company');

        $c = $company->find_one($id);

        if($c){

            $c->delete();

            r2(U.'contacts/companies/','s',$_L['Deleted Successfully']);

        }


        break;


    case 'event':

        $id = route(2);

        $calendar = Model::factory('Models_Calendar')->find_one($id);

        if($calendar){

            $calendar->delete();

            r2(U.'calendar/events/','s',$_L['Deleted Successfully']);

        }




        break;


    case 'role':

        $id = route(2);

        $role = Model::factory('Models_Role')->find_one($id);

        if($role){

            // check this role is using

            $users = Model::factory('Models_User')->where('roleid',$id)->find_one();

            if($users){

                r2(U.'settings/roles/','e','This Role is in Use. You will have to assign User to another Role before deleting.');

            }
            else{

                // delete all permissions

                $p = ORM::for_table('sys_staffpermissions')->where('rid',$id)->delete_many();

                $role->delete();



                r2(U.'settings/roles/','s',$_L['Deleted Successfully']);

            }

        }




        break;





    default:
        echo 'action not defined';
}