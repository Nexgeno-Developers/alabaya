<?php
_auth();
$ui->assign('_application_menu', 'accounts');
$ui->assign('_title', $_L['Accounts'].'- '. $config['CompanyName']);
$ui->assign('_st', $_L['Accounts']);
$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);

Event::trigger('accounts');

switch ($action) {
    case 'balances':

//Find all accounts
        $d = ORM::for_table('sys_accounts')->find_many();
        $tbal = ORM::for_table('sys_accounts')->sum('balance');
        $tbal = ib_money_format($tbal,$config);
        $ui->assign('d',$d);
        $ui->assign('tbal',$tbal);
        $ui->display('account-balances.tpl');

        break;

    case 'add':
        $ui->assign('xfooter', Asset::js(array('numeric')));
        $ui->assign('xjq', '
 $(\'.amount\').autoNumeric(\'init\',{
 
 vMin: \'-9999999999999.99\'
 
 });
 ');
        $ui->display('account-add.tpl');
        break;

    case 'add-post':
        $account = _post('account');
        $description = _post('description');
        $balance = _post('balance');
        $balance = Finance::amount_fix($balance);
				$email = _post('email');
				$pan = _post('pan');
				$bank_name = _post('bank_name');
				$account_number = _post('account_number');
				$account_type = _post('account_type');
				$ifsc_code = _post('ifsc_code');
        $msg = '';
        
				if(!empty($email)){ 
					if(!Validator::Email($email)){
							$msg .= $_L['Invalid Email']. '<br>';
					}
				}
        if(Validator::Length($account,100,2) == false){
            $msg .= $_L['account_title_length_error']. '<br>';
        }

        $d = ORM::for_table('sys_accounts')->where('account',$account)->find_one();
        if($d){
            $msg .= $_L['account_already_exist']. '<br>';
        }
        if (is_numeric($balance) == false) {
            $balance = '0.00';
        }
			
        if($msg == ''){
					
					$file_name = '';
					//logo upload
					if(isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"]) )	{	
						$validextentions = array("jpeg", "jpg", "png");
						$temporary = explode(".", $_FILES["file"]["name"]);
						$file_extension = end($temporary);
						
						if(($_FILES["file"]["type"] == "image/png")){
								$file_name = 'logo.png';
						}
						elseif(($_FILES["file"]["type"] == "image/jpg")){
								$file_name = 'logo.jpg';
						}
						elseif(($_FILES["file"]["type"] == "image/jpeg")){
								$file_name = 'logo.jpeg';
						}
						elseif(($_FILES["file"]["type"] == "image/gif")){
								$file_name = 'logo.gif';
						}
						else{

						}
						if ((($_FILES["file"]["type"] == "image/png")
									|| ($_FILES["file"]["type"] == "image/jpg")
									|| ($_FILES["file"]["type"] == "image/jpeg"))
									&& ($_FILES["file"]["size"] < 1000000)//approx. 100kb files can be uploaded
									&& in_array($file_extension, $validextentions))
						{
								$file_name = strtotime('now').'_'.$file_name;
								move_uploaded_file($_FILES["file"]["tmp_name"], 'application/storage/system/'. $file_name);
						}
					}					if(isset($_FILES["stamp"]["name"]) && !empty($_FILES["stamp"]["name"]) )	{	
						$validextentions = array("jpeg", "jpg", "png");
						$temporary = explode(".", $_FILES["stamp"]["name"]);
						$file_extension = end($temporary);
						
						if(($_FILES["stamp"]["type"] == "image/png")){
								$file_name1 = 'stamp.png';
						}
						elseif(($_FILES["stamp"]["type"] == "image/jpg")){
								$file_name1 = 'stamp.jpg';
						}
						elseif(($_FILES["stamp"]["type"] == "image/jpeg")){
								$file_name1 = 'stamp.jpeg';
						}
						elseif(($_FILES["stamp"]["type"] == "image/gif")){
								$file_name1 = 'stamp.gif';
						}
						else{

						}
						if ((($_FILES["stamp"]["type"] == "image/png")
									|| ($_FILES["stamp"]["type"] == "image/jpg")
									|| ($_FILES["stamp"]["type"] == "image/jpeg"))
									&& ($_FILES["stamp"]["size"] < 1000000)//approx. 100kb files can be uploaded
									&& in_array($file_extension, $validextentions))
						{
								$file_name1 = strtotime('now').'_'.$file_name1;
								move_uploaded_file($_FILES["stamp"]["tmp_name"], 'application/storage/system/'. $file_name1);
						}
					}
						// Add Account 
            $d = ORM::for_table('sys_accounts')->create();
            $d->account = $account;
            $d->description = $description;
            $d->balance = $balance;
            $d->bank_name = '';
            $d->account_number = _post('account_number');
            $d->currency = '';
            $d->branch = '';
            $d->address = _post('address');
            $d->contact_person = _post('contact_person');
            $d->contact_phone = _post('contact_phone');
            $d->email = $email;
            $d->company_logo = $file_name;						    
						$d->company_stamp = $file_name1;
            $d->website = '';
						$d->gstin = _post('gst');
						$d->pan = $pan;
						$d->bank_name = $bank_name;
						$d->account_number = $account_number;
						$d->ifsc = $ifsc_code;
						$d->account_type = $account_type;
            $d->ib_url = '';
            $d->created = date('Y-m-d H:i:s');
            $d->notes = '';
            $d->sorder = 1;
            $d->e = '';
            $d->token = '';
            $d->status = '';

            $d->save();
						$aid = $d->id();
            //Add a Transaction
            if($balance != '0.00'){
                $d = ORM::for_table('sys_transactions')->create();
                $d->account = $account;
                $d->type = 'Income';
                $d->payer = $_L['system'];
                $d->amount = $balance;
                $d->date = date('Y-m-d');
                $d->dr = '0.00';
                $d->cr = $balance;
                $d->bal = $balance;
                $d->description = $_L['initial_balance'];
                $d->category = '';
                $d->payer = '';
                $d->payee = '';
                $d->payeeid = '0';
                $d->payerid = '0';
                $d->status = 'Cleared';
                $d->tax = '0.00';
                $d->iid = 0;
                $d->aid = $aid;
                $d->method = '';
                $d->ref = '';
                $d->tags = '';

                $d->save();
            }
            
            r2(U . 'accounts/list', 's', $_L['account_created_successfully'].$ex_msg);
        }
        else{
            r2(U . 'accounts/add', 'e', $msg);
        }
        break;

    case 'list':

        $d = ORM::for_table('sys_accounts')->find_many();
        $ui->assign('d',$d);
        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/accounts.js"></script>
');
        $ui->display('accounts-manage.tpl');
        break;

    case 'edit':
        $id  = $routes['2'];
        $d = ORM::for_table('sys_accounts')->find_one($id);
        if($d){

            $ui->assign('d',$d);
            $ui->display('account-edit.tpl');

        }
        else{
            r2(U . 'accounts/list', 'e', $_L['Account_Not_Found']);
        }

        break;
    case 'edit-post':
        $account = _post('account');
        $description = _post('description');
        $id = _post('id');
        $email = _post('email');
        $pan = _post('pan');
				$bank_name = _post('bank_name');
				$account_number = _post('account_number');
				$account_type = _post('account_type');
				$ifsc_code = _post('ifsc_code');
        $msg = '';
        if(Validator::Length($account,100,2) == false){
            $msg .= $_L['account_title_length_error']. '<br>';
        }
				if(!empty($email)){ 
					if(!Validator::Email($email)){
							$msg .= $_L['Invalid Email']. '<br>';
					}
				}

        if($msg == ''){
						//logo upload
					$clogo = ORM::for_table('sys_accounts')->select('company_logo')->find_one($id);	
					$file_name = $clogo['company_logo'];	
					if(isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"]) )	{
						$validextentions = array("jpeg", "jpg", "png");
						$temporary = explode(".", $_FILES["file"]["name"]);
						$file_extension = end($temporary);
						
						if(($_FILES["file"]["type"] == "image/png")){
								$file_name = 'logo.png';
						}
						elseif(($_FILES["file"]["type"] == "image/jpg")){
								$file_name = 'logo.jpg';
						}
						elseif(($_FILES["file"]["type"] == "image/jpeg")){
								$file_name = 'logo.jpeg';
						}
						elseif(($_FILES["file"]["type"] == "image/gif")){
								$file_name = 'logo.gif';
						}
						else{

						}
						if ((($_FILES["file"]["type"] == "image/png")
									|| ($_FILES["file"]["type"] == "image/jpg")
									|| ($_FILES["file"]["type"] == "image/jpeg"))
									&& ($_FILES["file"]["size"] < 1000000)//approx. 100kb files can be uploaded
									&& in_array($file_extension, $validextentions))
						{
								$file_name = strtotime('now').'_'.$file_name;
								move_uploaded_file($_FILES["file"]["tmp_name"], 'application/storage/system/'. $file_name);
						}
					}
            $d = ORM::for_table('sys_accounts')->find_one($id);
            if($d){
                $oaccount = $d['account'];
                $d->account = $account;
                $d->description = $description;
                $d->bank_name = '';
                $d->account_number = _post('account_number');
                $d->currency = '';
                $d->branch = '';
                $d->address = _post('address');
                $d->gstin = _post('gst');
                $d->pan = $pan;
								$d->bank_name = $bank_name;
								$d->account_number = $account_number;
								$d->ifsc = $ifsc_code;
								$d->account_type = $account_type;
                $d->contact_person = _post('contact_person');
                $d->contact_phone = _post('contact_phone');
                $d->email = $email;
								$d->company_logo = $file_name;
                $d->website = '';
                $d->created = date('Y-m-d');
                $d->notes = '';
                $d->sorder = 1;
                $d->e = '';
                $d->token = '';
                $d->status = '';

                $d->save();

                //now update all transactions with the new name

                $b = ORM::for_table('sys_transactions')->where('account',$oaccount)->find_result_set()
                    ->set('account', $account)
                    ->save();

                r2(U . 'accounts/list', 's', $_L['account_updated_successfully'].$ex_msg);

            }
            else{
                r2(U . 'accounts/list', 'e', $_L['Account_Not_Found']);
            }
        }
        else{
            r2(U . 'accounts/edit/'. $id, 'e', $msg);
        }

        break;
    case 'delete':
        $id = $routes['2'];
        $id = str_replace('did','',$id);
        if($_app_stage == 'Demo'){
            r2(U . 'accounts/list', 'e', 'Sorry! Deleting Account is disabled in the demo mode.');
        }
        $d = ORM::for_table('sys_accounts')->find_one($id);
        if($d){
            $d->delete();
            r2(U . 'accounts/list', 's', $_L['account_delete_successful']);
        }

        break;
    case 'post':

        break;

    default:
        echo 'action not defined';
}