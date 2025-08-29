<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
if(!isset($myCtrl)){
    $myCtrl = 'sales';
}
_auth();
$ui->assign('_application_menu', 'sales');
$ui->assign('_title', 'sales - '. $config['CompanyName']);
$ui->assign('_st', 'sales');

$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);

$ui->assign('jsvar', '
_L[\'Working\'] = \''.$_L['Working'].'\';
_L[\'Submit\'] = \''.$_L['Submit'].'\';
 ');


switch ($action) {
    case 'add': //in use
        Event::trigger('sales/add/');
        $c = ORM::for_table('crm_accounts')->select('id')->select('account')->select('company')->select('email')->order_by_asc('account')->find_many();
        $agent = ORM::for_table('sys_users')->where('roleid','5')->select('id')->select('fullname')->order_by_asc('fullname')->find_many();        
        $services = ORM::for_table('sys_items')->order_by_asc('id')->find_many();
        $ui->assign('services',$services);
        $ui->assign('agent', $agent);
        $ui->assign('c', $c);
        $ui->assign('idate', date('Y-m-d'));

        $ui->assign('xheader', Asset::css(array('modal','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'sales/sales','modal')));
        $tags = Tags::get_all('Contacts');
        $ui->assign('tags',$tags);
        $ui->assign('xjq', '$("#country").select2({ theme: "bootstrap" }); ');
        $ui->assign('jsvar', '_L[\'Working\'] = \''.$_L['Working'].'\'; ');
        $currencies = Model::factory('Models_Currency')->find_array();
        $ui->assign('currencies',$currencies);
        $ui->display('sales/add-sale.tpl');
    break;

    case 'edit-sale-modal': //in use
    Event::trigger('sales/add/');
    $sid = $routes['2'];
    $crm_sales = ORM::for_table('crm_sales')->find_one($sid);    
    $c = ORM::for_table('crm_accounts')->select('id')->select('account')->select('company')->select('email')->order_by_asc('account')->find_many();
    $agent = ORM::for_table('sys_users')->where('roleid','5')->select('id')->select('fullname')->order_by_asc('fullname')->find_many();        
    $services = ORM::for_table('sys_items')->order_by_asc('id')->find_many();
    $ui->assign('services',$services);
    $ui->assign('agent', $agent);
    $ui->assign('c', $c);
    $ui->assign('sale', $crm_sales);
    $ui->assign('dur', $crm_sales['duration'].'_'.$crm_sales['duration_type']);
    $ui->assign('idate', date('Y-m-d'));
    $tags = Tags::get_all('Contacts');
    $ui->assign('tags',$tags);
    $ui->assign('xjq', '$("#country").select2({ theme: "bootstrap" }); ');
    $ui->assign('jsvar', '_L[\'Working\'] = \''.$_L['Working'].'\'; ');
    $currencies = Model::factory('Models_Currency')->find_array();
    $ui->assign('currencies',$currencies);
    $ui->display('sales/edit-sale.tpl');
    break;

    case 'generate-bill-modal': //in use
    Event::trigger('sales/generate-bill-modal/');
    $sale_id   = $routes['2'];
    $crm_sales = ORM::for_table('crm_sales')->find_one($sale_id);        
    $services  = ORM::for_table('sys_items')->order_by_asc('id')->find_many();  
    $ui->assign('services',$services);
    $ui->assign('sale', $crm_sales);
    $ui->assign('dur', $crm_sales['duration'].'_'.$crm_sales['duration_type']);
    $ui->display('sales/generate-bill.tpl');
    break; 

    case 'terminate-sale-modal': //in use
    Event::trigger('sales/terminate-sale-modal/');
    $sale_id   = $routes['2'];  
    $ui->assign('sale_id',$sale_id);
    $ui->display('sales/terminate.tpl');
    break;    
    
    case 'view-sale-modal': //in use
        Event::trigger('sales/view-sale-modal/');
        $sale_id   = $routes['2'];

        $sale = ORM::for_table('crm_sales')->find_one($sale_id);
        $sale_transaction = ORM::for_table('crm_sales_transaction')->where('sale_id', $sale_id)->order_by_desc('id')->find_many();         
        $customer = get_type_by_id_multi('crm_accounts', 'id', $sale['customer_id'], 'account,company,phone,email,id');
        $sale_logs = ORM::for_table('crm_sales_logs')->where('sale_id', $sale_id)->order_by_desc('id')->find_many();
        $agent = get_type_by_id_multi('sys_users', 'id', $sale['agent_id'], 'fullname'); 
        $service = get_type_by_id('sys_items', 'id', $sale['service_id'], 'name');   

        $ui->assign('customer',$customer);
        $ui->assign('sale_logs',$sale_logs);
        $ui->assign('service',$service);
        $ui->assign('sale', $sale);
        $ui->assign('sale_transaction', $sale_transaction);
        $ui->assign('agent', $agent);
        $ui->display('sales/view-sale.tpl');
    break;    

    case 'generate-bill':
        Event::trigger('sales/generate-bill/');
        $sale_id         = _post('sale_id');
        $service_id      = _post('service_id');
        $service_type    = get_type_by_id('sys_items', 'id', $service_id,'type2');
        $duration        = explode('_', _post('duration'));
        $duration_for    = $duration[0];
        $duration_type   = $duration[1]; 
        $qty             = _post('qty');
        $amount          = _post('amount');
        $desc            = _post('description');
        $update_date     = _post('update_date');
        $sale_trans_code = $sale_id.'-'.time();

        $msg = '';
        if(empty($service_id))
        {
            $msg .= '<p>Service field is required</p>';
        }
        if(empty(_post('duration')))
        {
            $msg .= '<p>Duration field is required</p>';
        }  
        if(empty($amount))
        {
            $msg .= '<p>Amount field is required</p>';
        } 
        if(empty($qty))
        {
            $msg .= '<p>Quantity field is required</p>';
        }        
        
        if(!empty($msg))
        {
            echo $msg;exit;
        }

        $sale = ORM::for_table('crm_sales')->find_one($sale_id);

        $cid = $sale['customer_id'];
		$company = 2;
				
        $u = ORM::for_table('crm_accounts')->find_one($cid);

        if(trim(strtolower($u['country'])) == 'india')
        {
            $default_tax_id = (in_array(trim(strtolower($u['state'])), array('mh', 'maharashtra'))) ? 1 : 6;
        }
        else
        {
            $default_tax_id = 4;
        }

        $notes = $desc;

        // find currency
        $currency_id = 1;
        $currency_find = Model::factory('Models_Currency')->find_one($currency_id);

        if($currency_find){
            $currency = $currency_id;
            $currency_symbol = $currency_find->symbol;
            $currency_rate = $currency_find->rate;
        }else{
            $currency = 0;
            $currency_symbol = $config['currency_code'];
            $currency_rate = 1.0000;
        }

        $amount = $amount;

        $idate = date('Y-m-d');
        $its = strtotime($idate);
        $duedate = 'due_on_receipt';
        $dd = $idate;
        $nd = $idate;
        $r = '0';
        $inv_prefix = 'PMI';
        $inv_no = 0;
		$pf_tax_id = $default_tax_id; //1;		
        $cn = '';

        $ttl = $amount * $qty;
        $sTotal = 0.00; $taxTotal = 0; $prod_total = 0;
        $ptaxrate = ORM::for_table('sys_tax')->find_one($pf_tax_id);
        $taxrate = $ptaxrate['rate'];
        $taxamt = $ttl *$taxrate/100;
        $taxTotal = $taxTotal + $taxamt;
        $sTotal += $taxamt + $ttl;
        $prod_total = $prod_total + $ttl;
        $fTotal = $sTotal;

        // calculate discount
        $discount_amount = '0';
        $discount_type = 'p';
        $discount_value = '0.00';
 
        if($discount_amount == '0' OR $discount_amount == ''){
            $actual_discount = '0.00';
        }else{
            if($discount_type == 'f'){
                $actual_discount = $discount_amount;
                $discount_value = $discount_amount;
            }else{
                $discount_type = 'p';
                $actual_discount = ($sTotal * $discount_amount) / 100;
                $discount_value = $discount_amount;
            }
        }
                    
        $actual_discount = number_format((float)$actual_discount, 2, '.', '');
        $fTotal = $fTotal - $actual_discount;

        $inv_no = ORM::for_table('sys_performa')->where('company_id', $company)->max('invoice_no');	
        $inv_no = $inv_no+1;
        $invoicenum = $inv_prefix.$inv_no;
        $datetime = date("Y-m-d H:i:s");
        $vtoken = _raid(10);
        $ptoken = _raid(10);
                    
        //save to sys_performa
        $d = ORM::for_table('sys_performa')->create();
        $d->userid = $cid;
        $d->account = $u['account'];
        $d->company_id = $company;
        $d->invoice_no = $inv_no;
        $d->cn = $cn;
        $d->invoicenum = $invoicenum;
        $d->date = $idate;
        $d->duedate = $dd;
        $d->datepaid = $datetime;
        $d->subtotal = Finance::amount_fix($sTotal-$actual_discount);
        $d->discount_type = $discount_type;
        $d->discount_value = $discount_value;
        $d->discount = $actual_discount;
        $d->total = Finance::amount_fix($prod_total);
        $d->vtoken = $vtoken;
        $d->ptoken = $ptoken;
        $d->status = 'Unpaid';
        $d->notes = $notes;
        $d->r = $r;
        $d->nd = $nd;
        $d->taxamt = Finance::amount_fix($taxTotal);
        $d->taxid = $pf_tax_id;
        $d->paymentmethod = '';
        $d->currency = $currency;
        $d->currency_symbol = $currency_symbol;
        $d->currency_rate = $currency_rate;
        $d->sale_id = $sale_id;
        $d->sale_trans_code = $sale_trans_code;
        $d->save(); //save
        $invoiceid = $d->id();

        if($duration_type == 'month')
        {
            $exp_date = date('Y-m-d', strtotime($update_date. "+".$duration_for." months"));
        }
        elseif($duration_type == 'year')
        {
            $exp_date = date('Y-m-d', strtotime($update_date. "+".$duration_for." years"));
        }
        
        if($service_type == 'recurring')
        {
            $content = ' ('.$update_date. ' to ' .$exp_date.')';
        }
        else{
            $content = '';
        }
        
        //save to sys_performaitems
        $prod = ORM::for_table('sys_items')->where('id', $service_id)->find_one();    
        $d = ORM::for_table('sys_performaitems')->create();
        $d->invoiceid = $invoiceid;
        $d->userid = $cid;
        $d->itemcode = $prod['id'];
        $d->description = $prod['name'].$content;
        $d->qty = Finance::amount_fix($qty);
        $d->amount = Finance::amount_fix($amount);
        $ptaxrate = ORM::for_table('sys_tax')->find_one($pf_tax_id);
        $d->taxrate = $ptaxrate['rate'];
        $d->tax_id = $pf_tax_id;
        $taxamt = $ttl*$ptaxrate['rate']/100;
        $d->taxamount = Finance::amount_fix($taxamt);
        $d->total = Finance::amount_fix($taxamt + $ttl);
        $d->type = '';
        $d->relid = '0';
        $d->duedate = $update_date;
        $d->paymentmethod = '';
        $d->notes = '';
        $d->save(); //save

        //update sale to latest data
        $d = ORM::for_table('crm_sales')->find_one($sale_id);    
        $d->service_id    = $service_id;
        $d->service_type  = $service_type;
        $d->duration      = $duration_for;
        $d->duration_type = $duration_type;
        $d->amount        = $ttl;
        $d->note          = $notes;
        $d->update_date   = $update_date;
        $d->expire_date   = $exp_date;
        $d->save(); //save
 
        //latest sale data 
        $sale_latest = ORM::for_table('crm_sales')->where('id', $sale_id)->find_one();

        $s_data = array(
            'agent_id'      => $sale_latest['agent_id'],
            'customer_id'   => $sale_latest['customer_id'],
            'service_id'    => $sale_latest['service_id'],
            'service_type'  => $sale_latest['service_type'],
            'amount'        => $sale_latest['amount'],
            'duration'      => $sale_latest['duration'],
            'duration_type' => $sale_latest['duration_type'],
            'domain'        => $sale_latest['domain'],
            'note'          => $sale_latest['note'],
            'ragister_date' => $sale_latest['ragister_date'],
            'update_date'   => $sale_latest['update_date'],
            'expire_date'   => $sale_latest['expire_date']
        );

        //save in crm_sales_transaction
        $d = ORM::for_table('crm_sales_transaction')->create(); 
        $d->sale_id         = $sale_id;
        $d->sale_trans_code = $sale_trans_code;
        $d->sale_data       = json_encode($s_data);
        $d->timestamp       = date('Y-m-d H:i:s');
        $d->save(); //save    
        
        add_sale_log($sale_id, "bill created", "");

        Event::trigger('add_invoice_posted');
        
        echo $invoiceid;

    break; 

    case 'edit-post':
			Event::trigger('sales/add-post/');
			Event::trigger('sales/add-post/_on_start');
            
            $sale_id        = _post('sale_id');
			$agent_id       = _post('agent_id');
            $customer_id    = _post('customer_id');
            $service_id     = _post('service_id');
            $duration       = explode('_', _post('duration'));
            $duration_for   = $duration[0];
            $duration_type  = $duration[1];
            $domain_name    = _post('domain_name');
            $amount         = _post('amount');
            $register_date  = _post('register_date');
            $update_date    = _post('register_date'); //_post('update_date');
            $note           = _post('note');

            if($duration_type == 'month')
            {
                $exp_date = date('Y-m-d', strtotime($register_date. "+".$duration_for." months"));
            }
            elseif($duration_type == 'year')
            {
                $exp_date = date('Y-m-d', strtotime($register_date. "+".$duration_for." years"));
            }
            //var_dump($duration);
            //var_dump($update_date);
            //exit;
			$msg = '';

            if($agent_id == '')
            {
					$msg .= 'Agent Name is required <br>';
            }
            if($customer_id == '')
            {
					$msg .= 'Customer Name is required <br>';
            }
            if($service_id == '')
            {
					$msg .= 'Service is required <br>';
            }
            if(_post('duration') == '')
            {
					$msg .= 'Duration is required <br>';
            } 
            if($amount == '')
            {
					$msg .= 'Amount is required <br>';
            } 
            if($register_date == '')
            {
					$msg .= 'Registration Date is required <br>';
            } 
            if($update_date == '')
            {
					$msg .= 'Update Date is required <br>';
            }                                                          
                        

			if($msg == ''){

				$d = ORM::for_table('crm_sales')->find_one($sale_id);

				$d->agent_id      = $agent_id;
				$d->customer_id   = $customer_id;
                $d->service_id    = $service_id;
                $d->service_type  = get_type_by_id('sys_items', 'id', $service_id, 'type2');
                $d->duration      = $duration_for;
                $d->duration_type = $duration_type;
                $d->domain 	      = $domain_name;
				$d->amount 	      = $amount;
                $d->ragister_date = $register_date;
                $d->update_date   = $update_date;
                $d->note          = $note;	
                $d->expire_date   = $exp_date;			
				//$d->timestamp     = time();
				
				$d->save();
				$lid = $d->id();
                _log('Sales Updated Successfully!');
                
                add_sale_log($lid, "sale updated", "");
				 
				echo $lid; 
			} else {
				echo $msg;
			}
			
    break;    

    case 'terminate-sale-post':
			Event::trigger('sales/terminate-sale-post/');
            Event::trigger('sales/terminate-sale-post/_on_start');           
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);           
            $sale_id        = _post('sale_id');
			$reason       = _post('reason');
			$msg = '';

            if($reason == '')
            {
					$msg .= 'Reason is required <br>';
            }                                                                              

			if($msg == ''){

				$d = ORM::for_table('crm_sales')->find_one($sale_id);
				$d->is_terminated = 'yes';		
				$d->save();
                $lid = $d->id();
                

                _log('Sales Terminated Successfully!');
                
                add_sale_log($lid, "sale terminated <b>(".$reason.")</b>", "");
				 
				echo $lid; 
			} else {
				echo $msg;
			}
			
    break;


    case 'add-post':
			Event::trigger('domain_n_hosting/add-post/');
			Event::trigger('domain_n_hosting/add-post/_on_start');

			$agent_id       = _post('agent_id');
            $customer_id    = _post('customer_id');
            $service_id     = _post('service_id');
            $duration       = explode('_', _post('duration'));
            $duration_for   = $duration[0];
            $duration_type  = $duration[1];
            $domain_name    = _post('domain_name');
            $amount         = _post('amount');
            $register_date  = _post('register_date');
            $update_date    = _post('register_date'); //_post('update_date');
            $note           = _post('note');

            if($duration_type == 'month')
            {
                $exp_date = date('Y-m-d', strtotime($register_date. "+".$duration_for." months"));
            }
            elseif($duration_type == 'year')
            {
                $exp_date = date('Y-m-d', strtotime($register_date. "+".$duration_for." years"));
            }
            //var_dump($duration);
            //var_dump($update_date);
            //exit;
			$msg = '';

            if($agent_id == '')
            {
					$msg .= 'Agent Name is required <br>';
            }
            if($customer_id == '')
            {
					$msg .= 'Customer Name is required <br>';
            }
            if($service_id == '')
            {
					$msg .= 'Service is required <br>';
            }
            if(_post('duration') == '')
            {
					$msg .= 'Duration is required <br>';
            } 
            if($amount == '')
            {
					$msg .= 'Amount is required <br>';
            } 
            if($register_date == '')
            {
					$msg .= 'Registration Date is required <br>';
            } 
            if($update_date == '')
            {
					$msg .= 'Update Date is required <br>';
            }                                                          
                        

			if($msg == ''){

				$d = ORM::for_table('crm_sales')->create();

				$d->agent_id      = $agent_id;
				$d->customer_id   = $customer_id;
                $d->service_id    = $service_id;
                $d->service_type  = get_type_by_id('sys_items', 'id', $service_id, 'type2');
                $d->duration      = $duration_for;
                $d->duration_type = $duration_type;
                $d->domain 	      = $domain_name;
				$d->amount 	      = $amount;
                $d->ragister_date = $register_date;
                $d->update_date   = $update_date;
                $d->note          = $note;	
                $d->expire_date   = $exp_date;			
				$d->timestamp     = time();
				
				$d->save();
                $lid = $d->id();
                
                add_sale_log($lid, "sale created", "");

				_log('Sales Added');
				 
				echo $lid; 
			} else {
				echo $msg;
			}
			
    break;

    case 'list':
		/* //send_mail_domain_hosting(); */
        Event::trigger('sales/list/');

        $agents = ORM::for_table('sys_users')->select('id')->select('fullname')->where('roleid', 5)->find_many();
        $custs = ORM::for_table('crm_accounts')->select('id')->select('account')->select('email')->select('company')->select('phone')->find_many();
        $services = ORM::for_table('sys_items')->select('id')->select('name')->find_many();

        $mode_css = Asset::css(array('s2/css/select2.min','footable/css/footable.core.min','modal','sn/summernote','sn/summernote-bs3','modal'));
		$mode_js = Asset::js(array('s2/js/select2.min', 'footable/js/footable.all.min','contacts/mode_search','modal'));

        $ui->assign('agents', $agents);
        $ui->assign('custs', $custs);
        $ui->assign('services', $services);
        $ui->assign('xheader', $mode_css);
        $ui->assign('xfooter', $mode_js.
            '<script type="text/javascript" src="' . $_theme . '/lib/list-contacts.js"></script>');
        $ui->display('sales/list-sales.tpl');

        break;

        case 'delete-bill':

            $sale_trans_code  = $routes['2'];
            
            $sale_transaction = ORM::for_table('crm_sales_transaction')->where('sale_trans_code', $sale_trans_code)->find_one();

            if($sale_transaction)
            {
                $sale_id = $sale_transaction['sale_id'];
                $sale_transaction->delete();
            }
             
            $invoice = ORM::for_table('sys_invoices')->where('sale_trans_code', $sale_trans_code)->find_one();
            if($invoice)
            {
                ORM::for_table('sys_invoiceitems')->where('invoiceid',$invoice['id'])->delete_many();
                $invoice->delete();
            }

            $performa = ORM::for_table('sys_performa')->where('sale_trans_code', $sale_trans_code)->find_one();
            if($performa)
            {
                ORM::for_table('sys_performaitems')->where('invoiceid',$performa['id'])->delete_many();
                $performa->delete(); 
            }   
            
            add_sale_log($sale_id, "bill deleted", "");

            r2(U.'sales/list','s','Bill Deleted Successfully');

        break;        
    
    case 'list-ajax-sales':  
        Event::trigger('sales/list-ajax-sales/');

        $response = array();
        
		## Read value
		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$rowperpage = $_POST['length'];
		$columnIndex = $_POST['order'][0]['column'];
		$columnName = $_POST['columns'][$columnIndex]['data'];
        $columnSortOrder = $_POST['order'][0]['dir'];
                
        $agent_id      = $_POST['agent_id'];
        $customer_id   = $_POST['customer_id'];
        $service_id    = $_POST['service_id'];
        $domain        = trim($_POST['domain']);
        $date_coloumn  = $_POST['date_coloumn'];
        $from          = $_POST['from'];
        $to            = $_POST['to'];
        $service_type  = $_POST['service_type'];
        $sale_id       = str_replace('MK-', '', $_POST['sale_id']);
        $is_terminated = $_POST['is_terminated'];

        $totalRecordwithFilter = ORM::for_table('crm_sales');
        $totalRecordwithFilter->select('id');

        if(!empty($sale_id))
        {
            $totalRecordwithFilter->where('id', $sale_id);
        } 
        if(!empty($is_terminated))
        {
            $totalRecordwithFilter->where('is_terminated', $is_terminated);
        }                       
        if(!empty($agent_id))
        {
            $totalRecordwithFilter->where('agent_id', $agent_id);
        }
        if(!empty($customer_id))
        {
            $totalRecordwithFilter->where('customer_id', $customer_id); 
        }
        if(!empty($service_id))
        {
            $totalRecordwithFilter->where('service_id', $service_id);
        }
        if(!empty($domain))
        {
            $totalRecordwithFilter->where('domain', $domain);
        } 
        if(!empty($service_type))
        {
            $totalRecordwithFilter->where('service_type', $service_type);
        }        
        if(!empty($date_coloumn) && !empty($from) && !empty($to))
        {
            $totalRecordwithFilter->where_gte($date_coloumn, $from);
            $totalRecordwithFilter->where_lte($date_coloumn, $to);
        }

        $totalRecordwithFilter->offset($start);
        $totalRecordwithFilter->limit($rowperpage);  

        $totalRecordwithFilter = $totalRecordwithFilter->count();


        $record = ORM::for_table('crm_sales');

        if(!empty($sale_id))
        {
            $record->where('id', $sale_id);
        } 
        if(!empty($is_terminated))
        {
            $record->where('is_terminated', $is_terminated);
        }         
        if(!empty($agent_id))
        {
            $record->where('agent_id', $agent_id);
        }
        if(!empty($customer_id))
        {
            $record->where('customer_id', $customer_id); 
        }
        if(!empty($service_id))
        {
            $record->where('service_id', $service_id);
        }
        if(!empty($domain))
        {
            $record->where('domain', $domain);
        } 
        if(!empty($service_type))
        {
            $record->where('service_type', $service_type);
        }         
        if(!empty($date_coloumn) && !empty($from) && !empty($to))
        {
            $record->where_gte($date_coloumn, $from);
            $record->where_lte($date_coloumn, $to);
        }

        $record->offset($start);
        $record->limit($rowperpage); 
        
        if($columnSortOrder == 'asc')
        {
            $record->order_by_asc($columnName);
        }
        elseif($columnSortOrder == 'desc') //var_dump($columnName);
        {
            $record->order_by_desc($columnName); //var_dump($columnName);
        }

        $records = $record->find_many();

		$data = array();
        $sr = $start + 1;
		foreach($records as $record){  
             $agent    = get_type_by_id('sys_users', 'id', $record['agent_id'], 'fullname');    
             $customer = get_type_by_id('crm_accounts', 'id', $record['customer_id'], 'account'); 
             $service  = get_type_by_id('sys_items', 'id', $record['service_id'], 'name');

            $warning_date = strtotime($record['expire_date']) - time();
            $warning_date = round($warning_date / (60 * 60 * 24));
            
            $total_bills = ORM::for_table('crm_sales_transaction')->select('id')->where('sale_id', $record['id'])->count();

            //var_dump($warning_date);
            $exp_label = '';
             if($warning_date <= 21)
             {
                $exp_label = 'style="color:#2196f3;font-weight: bold;"';
             }

             if($warning_date <= 7)
             {
                $exp_label = 'style="color:orange;font-weight: bold;"';
             }

             if($warning_date < 0)
             {
                $exp_label = 'style="color:red;font-weight: bold;"';
             }
             
             if($total_bills > 0 || $record['is_terminated'] == 'yes')
             {
                $button1 = '';
             }
             else{
                $event1 = "edit_sale_modal('".$record['id']."')";
                $button1 = '<a href="javascript:void(0);" class="btn btn-primary btn-xs" onclick="'.$event1.'"><i class="fa fa-edit"></i> edit</a>';
             }      
             
             $event3 = "view_sale_modal('".$record['id']."')";
             $button3 = '<a href="javascript:void(0);" class="btn btn-secondary btn-xs" onclick="'.$event3.'"><i class="fa fa-eye"></i>View</a>';   

             if($record['is_terminated'] == 'yes')
             {
                $button4 = '';
                $button2 = '';
             }
             else
             {
                $event2 = "generate_bill_modal('".$record['id']."')";
                $button2 = '<a href="javascript:void(0);" class="btn btn-info btn-xs" onclick="'.$event2.'"><i class="fa fa-repeat"></i> Genarate Bill</a>';

                $event4 = "terminate_sale_modal('".$record['id']."')"; 
                $button4 = '<a href="javascript:void(0);" class="btn btn-danger btn-xs" onclick="'.$event4.'"><i class="fa fa-trash"></i> Terminate</a>';
             }



			$data[] = array( 
                "sr"             => $sr,
                "id"             => '<b>'.SaleID($record['id']).'<b>',
			    "agent_id"       => $agent,
				"customer_id"    => $customer,
				"service_id"     => $service,
				"domain"         => $record['domain'] ? $record['domain'] : '-',
				"duration"       => $record['duration'].' '.$record['duration_type'],
                "amount"         => $record['amount'],
                "expire_date"    => '<span '.$exp_label.'>'.date('d M Y', strtotime($record['expire_date'])).'</span>',
                "remaining"      => ($warning_date >= 0) ? '<span '.$exp_label.'>'.$warning_date.' days remaining</span>' : '<span '.$exp_label.'>'.'Expired</span>',
                "ragister_date"  => date('d M Y', strtotime($record['ragister_date'])),
                "update_date"    => date('d M Y', strtotime($record['update_date'])),    
                "service_type"   => $record['service_type'],
                "total_bills"    => $total_bills,
                "status"         => ($record['is_terminated'] == 'yes') ? 'Terminated' : '',
                "action"         => $button1.' '.$button2.' '.$button3.' '.$button4,
			); 
		$sr++;	
		}
  
		## Response
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecordwithFilter,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $data
		);
  
		echo json_encode($response);


    break;

    default:
        echo 'action not defined';
}