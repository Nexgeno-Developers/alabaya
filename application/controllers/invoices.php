<?php 

_auth();
$ui->assign('_application_menu', 'dsfds');
$ui->assign('_st', $_L['Invoices']);
$ui->assign('_title', $_L['Sales'].'- ' . $config['CompanyName']);
$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);

Event::trigger('invoices');

switch ($action) {
    case 'add':
//find all clients.

        // Permission check (keep)
        if(!has_access($user->roleid, 'sales')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }

        // if($user['roleid'] != 0)
        // {
        // exit; 
        // }

        Event::trigger('invoices/add/');

        $extra_fields = '';
        $extra_jq = '';

        Event::trigger('add_invoice');

        $cloths = ORM::for_table('sys_cloths')->find_many();
        $ui->assign('cloths', $cloths); 
        
        $designs = ORM::for_table('sys_designs')->find_many();
        $ui->assign('designs', $designs);        

        $ui->assign('extra_fields', $extra_fields);

        if (isset($routes['2']) AND ($routes['2'] == 'recurring')) {
            $recurring = true;
        } else {
            $recurring = false;
        }
				
				$taxes = ORM::for_table('sys_tax')->order_by_asc('rate')->find_many();
        $tax_opts = "<optgroup label=GST>";
        foreach ($taxes as $tax) {
					if($tax['taxtype']=='GST'){
						$tax_opts .= '<option value="' . $tax['id'] . '">' . $tax['name'] ." ". $tax['rate'] ." %" .'</option>';
					}
				} 
				$tax_opts .= '</optgroup><optgroup label=IGST>';
        foreach ($taxes as $tax) {
					if($tax['taxtype']=='IGST'){
						$tax_opts .= '<option value="' . $tax['id'] . '">' . $tax['name'] ." ". $tax['rate'] ." %" .'</option>';
					}
				} 
				$tax_opts .= '</optgroup>';
				$ui->assign('tax_opts', $tax_opts);
				
				$items = ORM::for_table('sys_items')->find_many();
        $ui->assign('items', $items);
				
        $currencies = Model::factory('Models_Currency')->find_array();


        $ui->assign('recurring', $recurring);
        $ui->assign('currencies', $currencies);



        if (isset($routes['3']) AND ($routes['3'] != '')) {
            $p_cid = $routes['3'];
            $p_d = ORM::for_table('crm_accounts')->find_one($p_cid);
            if ($p_d) {
                $ui->assign('p_cid', $p_cid);
            }
        } else {
            $ui->assign('p_cid', '');
        }

        $ui->assign('_st', $_L['Add Invoice']);
        // $c = ORM::for_table('crm_accounts')->select('id')->select('account')->select('phone')->select('email')->where('gid', 1)->order_by_desc('id')->find_many();
        // $ui->assign('c', $c);

        $comp = ORM::for_table('sys_accounts')->select('id')->select('account')->order_by_asc('id')->find_many();
        $ui->assign('comp', $comp);
        $ui->assign('idate', date('Y-m-d'));


				if($config['i_driver'] == 'default'){
						$js_file = 'invoice';
						$tpl_file = 'add-invoice.tpl';
				}
        elseif($config['i_driver'] == 'v2'){
            $js_file = 'invoice_add_v2';
            $tpl_file = 'add_invoice_v2.tpl';
        }
        else{
            $js_file = 'invoice';
            $tpl_file = 'add-invoice.tpl';
        }

        $css_arr = array('s2/css/select2.min','modal','dp/dist/datepicker.min','redactor/redactor');
        $js_arr = array('validator/parsley','redactor/redactor.min','s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','modal',$js_file);

        Event::trigger('add_invoice_rendering_form');

        $ui->assign('xheader', Asset::css($css_arr));
        $ui->assign('xfooter', Asset::js($js_arr));


        $ui->assign('xjq', '

 $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });


 '.
            $extra_jq);

        $ui->display($tpl_file);

        break;

    case 'edit':
 
        // Permission check (keep)
        if(!has_access($user->roleid, 'sales')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
        // if($user['roleid'] != 0)
        // {
        // exit; 
        // }   

        Event::trigger('invoices/edit/');
        $id = $routes['2'];
        $d = ORM::for_table('sys_invoices')->find_one($id);

        if ($d) {

            $cloths = ORM::for_table('sys_cloths')->find_many();
            $ui->assign('cloths', $cloths); 
            
            $designs = ORM::for_table('sys_designs')->find_many();
            $ui->assign('designs', $designs);            

            $currencies = Model::factory('Models_Currency')->find_array();
            $ui->assign('currencies', $currencies);
						$ui->assign('idate', date('Y-m-d'));
            $ui->assign('i', $d);
						$comp = ORM::for_table('sys_accounts')->select('id')->select('account')->order_by_asc('id')->find_many();
						$ui->assign('comp', $comp);
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid', $id)->order_by_asc('id')->find_many();
						$ui->assign('items', $items);

            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $ui->assign('a', $a);
            $ui->assign('d', $d);
            $ui->assign('_st', $_L['Add Invoice']);
            // $c = ORM::for_table('crm_accounts')->select('id')->select('account')->select('company')->select('phone')->select('email')->where('gid', 1)->order_by_desc('id')->find_many();
            // $ui->assign('c', $c);
            $current_c = ORM::for_table('crm_accounts')
                ->table_alias('c')
                ->select('c.id')
                ->select('c.account')
                ->select('c.phone')
                ->select('c.email')
                ->select('b.alias', 'branch_alias')
                ->join('sys_accounts', ['c.branch_id', '=', 'b.id'], 'b')
                ->where('c.id', $d['userid'])
                ->find_one();

            $ui->assign('current_c', $current_c);

            $products = ORM::for_table('sys_items')->find_many();
            $ui->assign('products', $products);
    
            $taxes = ORM::for_table('sys_tax')->order_by_asc('rate')->find_many();
            $ui->assign('taxes', $taxes); 

            $ui->assign('idate', date('Y-m-d'));

            if($config['i_driver'] == 'default'){
                $js_file = 'edit-invoice-v2';
                $tpl_file = 'edit-invoice.tpl';
            }
            elseif($config['i_driver'] == 'v2'){
                $js_file = 'edit_invoice_v2n';
                $tpl_file = 'edit_invoice_v2.tpl';
            }
            else{
                $js_file = 'edit-invoice-v2';
                $tpl_file = 'edit-invoice.tpl';
            }

            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','modal','dp/dist/datepicker.min','redactor/redactor')));
            $ui->assign('xfooter', Asset::js(array('redactor/redactor.min','s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','modal',$js_file)));

            $ui->assign('xjq', '

							$(\'.amount\').autoNumeric(\'init\', {

								aSign: \''.$config['currency_code'].' \',
								dGroup: '.$config['thousand_separator_placement'].',
								aPad: '.$config['currency_decimal_digits'].',
								pSign: \''.$config['currency_symbol_position'].'\',
								aDec: \''.$config['dec_point'].'\',
								aSep: \''.$config['thousands_sep'].'\'
								});
							');

            $ui->display($tpl_file);

        } else {
            echo 'Invoice Not Found';
        }
//find all clients.


        break;

    case 'proforma-edit':

        Event::trigger('invoices/proforma-edit/');
        $id = $routes['2'];
        $d = ORM::for_table('sys_performa')->find_one($id);

        if ($d) {

            $currencies = Model::factory('Models_Currency')->find_array();
            $ui->assign('currencies', $currencies);
						$ui->assign('idate', date('Y-m-d'));
            $ui->assign('i', $d);
						$comp = ORM::for_table('sys_accounts')->select('id')->select('account')->order_by_asc('id')->find_many();
						$ui->assign('comp', $comp);
            $items = ORM::for_table('sys_performaitems')->where('invoiceid', $id)->order_by_asc('id')->find_many();
						$ui->assign('items', $items);

            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $ui->assign('a', $a);
            $ui->assign('d', $d);
            $ui->assign('_st', 'Edit Proforma');
            $c = ORM::for_table('crm_accounts')->select('id')->select('account')->select('company')->find_many();
            $ui->assign('c', $c);
						$products = ORM::for_table('sys_items')->find_many();
						$ui->assign('products', $products);
				
						$taxes = ORM::for_table('sys_tax')->order_by_asc('rate')->find_many();
						$ui->assign('taxes', $taxes); 

            $ui->assign('idate', date('Y-m-d'));

            if($config['i_driver'] == 'default'){
                $js_file = 'edit-proforma-v2';
                $tpl_file = 'edit-proforma.tpl';
            }
            elseif($config['i_driver'] == 'v2'){
                $js_file = 'edit_proforma_v2n';
                $tpl_file = 'edit_proforma_v2.tpl';
            }
            else{
                $js_file = 'edit-proforma-v2';
                $tpl_file = 'edit-proforma.tpl';
            }

            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','modal','dp/dist/datepicker.min','redactor/redactor')));
            $ui->assign('xfooter', Asset::js(array('redactor/redactor.min','s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','modal',$js_file)));

            $ui->assign('xjq', '

							$(\'.amount\').autoNumeric(\'init\', {

								aSign: \''.$config['currency_code'].' \',
								dGroup: '.$config['thousand_separator_placement'].',
								aPad: '.$config['currency_decimal_digits'].',
								pSign: \''.$config['currency_symbol_position'].'\',
								aDec: \''.$config['dec_point'].'\',
								aSep: \''.$config['thousands_sep'].'\'
								});
							');

            $ui->display($tpl_file);

        } else {
            echo 'Invoice Not Found';
        }
//find all clients.


        break;


    case 'view':
        Event::trigger('invoices/view/');

        $id = $routes['2'];
        $d = ORM::for_table('sys_invoices')->find_one($id);
        $s = ORM::for_table('sys_invoices_status')->where('invoice_id', $id)->find_many();
			$app_config = ORM::for_table('sys_appconfig')->find_one('85'); 
				
				$ui->assign('app_config', $app_config);				
        if ($d) {

            //find all activity for this user
	
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid', $id)->order_by_asc('id')->find_many();

            $ui->assign('items', $items);
            $ui->assign('delivery_status', $s);
			//find company 
            $comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);
            $ui->assign('comp', $comp);
            //find related transactions
            $trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();
            $trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
            $emls_c = ORM::for_table('sys_email_logs')->where('iid', $id)->count();
            $emls = ORM::for_table('sys_email_logs')->where('iid', $id)->order_by_desc('id')->find_many();
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
           
			$ui->assign('emls', $emls);
            $ui->assign('emls_c', $emls_c);
			$ui->assign('trs', $trs);
            $ui->assign('trs_c', $trs_c);
            $ui->assign('a', $a);
            $ui->assign('d', $d);

            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];
            $i_discount = $d['discount'];
            if($d['credit'] != '0.00'){
                $i_due = $i_total - $i_credit;
            }
            else{
                $i_due =  $d['subtotal']-$d['discount'];
            }

            $i_due = number_format($i_due,2,$config['dec_point'],$config['thousands_sep']);
            $ui->assign('i_due', $i_due);

            //find all custom fields
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();
            $ui->assign('cf',$cf);
            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min','sn/summernote','sn/summernote-bs3','modal','sn/summernote-application')));
            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','modal','sn/summernote.min','jslib/invoice-view')));
            $x_html = '';

            Event::trigger('view_invoice');
            $ui->assign('x_html',$x_html);
            $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {
							dGroup: '.$config['thousand_separator_placement'].',
							aPad: '.$config['currency_decimal_digits'].',
							pSign: \''.$config['currency_symbol_position'].'\',
							aDec: \''.$config['dec_point'].'\',
							aSep: \''.$config['thousands_sep'].'\'
							});');
            $ui->display('invoice-view.tpl');

        } else {
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;
		
		case 'performa-view':
        Event::trigger('invoices/performa-view/');
			
        $id = $routes['2'];
        $d = ORM::for_table('sys_performa')->find_one($id);
			/*var_dump($d['currency_symbol']);exit; */
			$app_config = ORM::for_table('sys_appconfig')->find_one('85'); 
				
				$ui->assign('app_config', $app_config);				
        if ($d) {

            //find all activity for this user
	
            $items = ORM::for_table('sys_performaitems')->where('invoiceid', $id)->order_by_asc('id')->find_many();

            $ui->assign('items', $items);
			//find company 
            $comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);
            $ui->assign('comp', $comp);
            //find related transactions 
			$invoicenum = str_replace("PMI","MI",$d['invoicenum']); 
					
			$iid = get_type_by_id('sys_invoices', 'invoicenum', $invoicenum, 'id');
            $trs_c = ORM::for_table('sys_proforma_transactions')->where('iid', $id)->count();
            $trs = ORM::for_table('sys_proforma_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
            $emls_c = ORM::for_table('sys_proforma_email_logs')->where('iid', $iid)->count();
            $emls = ORM::for_table('sys_proforma_email_logs')->where('iid', $iid)->order_by_desc('id')->find_many();
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
           
			$ui->assign('emls', $emls);
            $ui->assign('emls_c', $emls_c);
			$ui->assign('trs', $trs);
            $ui->assign('trs_c', $trs_c);
            $ui->assign('a', $a);
            $ui->assign('d', $d);

            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];
            $i_discount = $d['discount'];
            if($d['credit'] != '0.00'){
                $i_due = $i_total - $i_credit;
            }
            else{
                $i_due =  $d['subtotal']-$d['discount'];
            }

            $i_due = number_format($i_due,2,$config['dec_point'],$config['thousands_sep']);
            $ui->assign('i_due', $i_due);

            //find all custom fields
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();
            $ui->assign('cf',$cf);
            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min','sn/summernote','sn/summernote-bs3','modal','sn/summernote-application')));
            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','modal','sn/summernote.min','jslib/invoice-view')));
            $x_html = '';

            Event::trigger('view_invoice');

            $ui->assign('x_html',$x_html);

            $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

							dGroup: '.$config['thousand_separator_placement'].',
							aPad: '.$config['currency_decimal_digits'].',
							pSign: \''.$config['currency_symbol_position'].'\',
							aDec: \''.$config['dec_point'].'\',
							aSep: \''.$config['thousands_sep'].'\'
							});');

            $ui->display('performa-view.tpl');

        } else {
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;
    case 'add-post':
        Event::trigger('invoices/add-post/');
/* 				var_dump($_POST);exit; */
        $cid = _post('cid');
				$company = _post('company');
				
        $u = ORM::for_table('crm_accounts')->find_one($cid);

        $msg = '';
        if ($cid == '') {
            $msg .= $_L['select_a_contact'].' <br> ';
        }
				if ($company == '') {
            $msg .= $_L['select_a_company'].' <br> ';
        }
        $notes = _post('notes');

        // find currency
        $currency_id = _post('currency');
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

        if (isset($_POST['amount'])) {
            $amount = $_POST['amount'];
        } else {
            $msg .= $_L['at_least_one_item_required'].' <br> ';
        }

        $idate = _post('idate');
        $its = strtotime($idate);
        $duedate = _post('duedate');
        $dd = '';
        if ($duedate == 'due_on_receipt') {
            $dd = $idate;
        } elseif ($duedate == 'days3') {
            $dd = date('Y-m-d', strtotime('+3 days', $its));

        } elseif ($duedate == 'days5') {
            $dd = date('Y-m-d', strtotime('+5 days', $its));
        } elseif ($duedate == 'days7') {
            $dd = date('Y-m-d', strtotime('+7 days', $its));
        } elseif ($duedate == 'days10') {
            $dd = date('Y-m-d', strtotime('+10 days', $its));
        } elseif ($duedate == 'days15') {
            $dd = date('Y-m-d', strtotime('+15 days', $its));
        } elseif ($duedate == 'days30') {
            $dd = date('Y-m-d', strtotime('+30 days', $its));
        } elseif ($duedate == 'days45') {
            $dd = date('Y-m-d', strtotime('+45 days', $its));
        } elseif ($duedate == 'days60') {
            $dd = date('Y-m-d', strtotime('+60 days', $its));
        } else {
            $msg .= 'Invalid Date <br> ';
        }
        if (!$dd) {
            $msg .= 'Date Parsing Error <br> ';
        }

        $repeat = _post('repeat');
        $nd = $idate;
        if ($repeat == '0') {
            $r = '0';
        } elseif ($repeat == 'week1') {
            $r = '+1 week';
            $nd = date('Y-m-d', strtotime('+1 week', $its));
        } elseif ($repeat == 'weeks2') {
            $r = '+2 weeks';
            $nd = date('Y-m-d', strtotime('+2 weeks', $its));
        } elseif ($repeat == 'month1') {
            $r = '+1 month';
            $nd = date('Y-m-d', strtotime('+1 month', $its));
        } elseif ($repeat == 'months2') {
            $r = '+2 months';
            $nd = date('Y-m-d', strtotime('+2 months', $its));
        } elseif ($repeat == 'months3') {
            $r = '+3 months';
            $nd = date('Y-m-d', strtotime('+3 months', $its));
        } elseif ($repeat == 'months6') {
            $r = '+6 months';
            $nd = date('Y-m-d', strtotime('+6 months', $its));
        } elseif ($repeat == 'year1') {
            $r = '+1 year';
            $nd = date('Y-m-d', strtotime('+1 year', $its));
        } elseif ($repeat == 'years2') {
            $r = '+2 years';
            $nd = date('Y-m-d', strtotime('+2 years', $its));
        } elseif ($repeat == 'years3') {
            $r = '+3 years';
            $nd = date('Y-m-d', strtotime('+3 years', $its));
        } else {
            $msg .= 'Date Parsing Error <br> ';
        }
				$inv_prefix = 'MI';
				$inv_no = 0;
        if ($msg == '') {
						
						$cn = _post('cn');
						$sTotal = 0.00; $taxTotal = 0; $prod_total = 0;
						for($i=0;$i<count($_POST['desc']);$i++ ){
								$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
								$taxrate = $ptaxrate['rate'];
								$taxamt = $_POST['total'][$i]*$taxrate/100;
								$taxTotal = $taxTotal + $taxamt;
								$sTotal += $taxamt + $_POST['total'][$i];
								$prod_total = $prod_total + $_POST['total'][$i];
						}
						
						//exit;
					 $fTotal = $sTotal;
						// calculate discount
            $discount_amount = _post('discount_amount');
            $discount_type = _post('discount_type');
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
            /*
            $inv_no = ORM::for_table('sys_invoices')->where('company_id', $company)->max('invoice_no');
			$inv_no = $inv_no+1;
			if($company == 1){
				$inv_prefix = 'ME';
			}
			$invoicenum = $inv_prefix.$inv_no;
			*/
							
            // Start transaction Lock the table to prevent race conditions
            ORM::get_db()->beginTransaction();
            ORM::get_db()->exec("LOCK TABLES sys_invoices WRITE");
            $inv_no = ORM::for_table('sys_invoices')->max('invoice_no');
			$inv_no = $inv_no+1;
		    if($company == 1){
				$inv_prefix = 'ME';
			}
			$invoicenum = $inv_prefix.$inv_no;
            ORM::get_db()->exec("UNLOCK TABLES");
            ORM::get_db()->commit();
            
            $datetime = date("Y-m-d H:i:s");
            $vtoken = _raid(10);
            $ptoken = _raid(10);
						
			//save to sys_invoices
            $d = ORM::for_table('sys_invoices')->create();
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
			$d->taxid = $_POST['taxed'];
            //others
            $d->paymentmethod = '';
            $d->currency = $currency;
            $d->currency_symbol = $currency_symbol;
            $d->currency_rate = $currency_rate;

            $d->save(); //save
            $invoiceid = $d->id();
 
						for($i=0;$i<count($_POST['desc']);$i++ ){
								$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
								
								$d = ORM::for_table('sys_invoiceitems')->create();
								$d->invoiceid = $invoiceid;
								$d->userid = $cid;
								$d->itemcode = $_POST['s_id'][$i];
								$d->description = $_POST['desc'][$i];
								$d->qty = Finance::amount_fix($_POST['qty'][$i]);
								$d->amount = Finance::amount_fix($_POST['amount'][$i]);
								//get tax rate by id
								$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
								//var_dump($ptaxrate['rate']);
								$d->taxrate = $ptaxrate['rate'];
								$d->tax_id = $_POST['taxed'];
								$taxamt = $_POST['total'][$i]*$ptaxrate['rate']/100;
								$d->taxamount = Finance::amount_fix($taxamt);
								$d->total = Finance::amount_fix($taxamt + $_POST['total'][$i]);
								//others
								$d->type = '';
								$d->relid = '0';
								$d->duedate = date('Y-m-d');
								$d->paymentmethod = '';
								$d->notes = '';

								$d->save(); //save
						}

            Event::trigger('add_invoice_posted');

            echo $invoiceid;
				}else {
            echo $msg;
        }

        break;  
		case 'generate-invoice':
				
			Event::trigger('invoices/generate-invoice/');
			$prod = ORM::for_table('sys_performa')->where('id',$_POST['iid'])->find_one();
            /*
			$inv_no = ORM::for_table('sys_invoices')->max('invoice_no');
			$inv_no = $inv_no+1;
			*/
							
            // Start transaction Lock the table to prevent race conditions
            ORM::get_db()->beginTransaction();
            ORM::get_db()->exec("LOCK TABLES sys_invoices WRITE");
            $inv_no = ORM::for_table('sys_invoices')->max('invoice_no');
			$inv_no = $inv_no+1;
            ORM::get_db()->exec("UNLOCK TABLES");
            ORM::get_db()->commit();
            
			$prod->invoice_status = 1;
			$prod->save(); 
				 
			$invoicenum = 'MI'.$inv_no; 	
			$datetime = date("Y-m-d H:i:s");
					
			//save to sys_invoices
            $d = ORM::for_table('sys_invoices')->create();
            $d->userid = $prod['userid'];
            $d->sale_id = $prod['sale_id'];
            $d->sale_trans_code = $prod['sale_trans_code'];             
            $d->account = $prod['account'];
			$d->company_id = $prod['company_id'];
			$d->invoice_no = $inv_no;
			$d->cn = $prod['cn'];
			$d->invoicenum = $invoicenum;
            $d->date = date("Y-m-d");
            $d->duedate = date("Y-m-d");
            $d->datepaid = $datetime;
            $d->subtotal = $prod['subtotal'];
			$d->discount_type = $prod['discount_type'];
            $d->discount_value = $prod['discount_value'];
            $d->discount = $prod['discount'];
            $d->total = $prod['total'];
            $d->vtoken = $prod['vtoken'];
            $d->ptoken = $prod['ptoken'];
            $d->status = $prod['status'];
            $d->notes = $prod['notes'];
            $d->r = $prod['r'];
            $d->nd = $prod['nd'];
			$d->taxamt = $prod['taxamt'];
			$d->taxid = $prod['taxid'];
			$d->credit = $prod['credit'];
            /* //others */
            $d->paymentmethod = '';
            $d->currency = $prod['currency'];
            $d->currency_symbol = $prod['currency_symbol'];
            $d->currency_rate = $prod['currency_rate'];

            $d->save(); //save
            $invoiceid = $d->id();
    		$inv_invoicenum = $d->invoicenum;
    		/* get_type_by_id('sys_invoices','','',''); */
    		$prod->set('inv_id',$invoiceid);
    		$prod->save();
						
			$performa_items = ORM::for_table('sys_performaitems')->where('invoiceid',$_POST['iid'])->find_many();
			foreach($performa_items as $performa){
				$d = ORM::for_table('sys_invoiceitems')->create();
				$d->invoiceid = $invoiceid;
				$d->userid = $performa['userid'];
				$d->itemcode = $performa['itemcode'];
				$d->description = $performa['description'];
				$d->qty = $performa['qty'];
				$d->amount = $performa['amount'];
				$d->taxrate = $performa['taxrate'];
				$d->tax_id = $performa['tax_id'];
				$d->taxamount = $performa['taxamount'];
				$d->total = $performa['total'];
				//others
				$d->type = '';
				$d->relid = '0';
				$d->duedate = $performa['duedate'];
				$d->paymentmethod = '';
				$d->notes = '';
				$d->save(); //save
			}
			
			$performa_tanscs = ORM::for_table('sys_proforma_transactions')->where('iid',$_POST['iid'])->find_many();
			foreach($performa_tanscs as $performa_tansc){
				$tran_inv = ORM::for_table('sys_transactions')->create();
				$tran_inv->account = $performa_tansc['account'];
				$tran_inv->type = $performa_tansc['type'];
				$tran_inv->payerid = $performa_tansc['payerid'];
				$tran_inv->amount = $performa_tansc['amount'];
				$tran_inv->category = $performa_tansc['category'];
				$tran_inv->method = $performa_tansc['method'];
				$tran_inv->ref = $performa_tansc['ref'];
				$tran_inv->tags = $performa_tansc['tags'];
				$tran_inv->description = str_replace('Proforma','Invoice',str_replace($prod->invoicenum,$inv_invoicenum,$performa_tansc['description']));
				$tran_inv->date = $performa_tansc['date'];
				$tran_inv->dr = $performa_tansc['dr'];
				$tran_inv->cr =$performa_tansc['cr'];
				$tran_inv->bal = $performa_tansc['bal'];
				$tran_inv->iid = $invoiceid;
				$tran_inv->payer = $performa_tansc['payer'];
				$tran_inv->payee = $performa_tansc['payee'];
				$tran_inv->payeeid = $performa_tansc['payeeid'];
				$tran_inv->status = $performa_tansc['status'];
				$tran_inv->tax = $performa_tansc['tax'];
				//save
				$tran_inv->save();
			}

            Event::trigger('add_invoice_posted');

            echo $invoiceid;

        break;
				
		case 'add-performa-post':
        Event::trigger('invoices/add-performa-post/');
        $cid = _post('cid');
        $company = _post('company');
        $company_branch_id = _post('company_branch_id');
				
        
        $msg = '';
        if ($company == '')
        {
            $msg .= $_L['select_a_company'].' <br> ';
        }

        if(strlen($_POST['cust_phone']) != 10 && strlen($_POST['cust_phone']) != 12)
        {
            $msg .= 'Enter a 10 or 12 digit contact number.<br>';
        }

        // if(strlen($_POST['cust_phone']) != 10)
        // {
        //     $msg .= 'Enter 10 digit contact number.<br> ';
        // }

        if (isset($_POST['amount']))
        {
            $amount = $_POST['amount'];
        }
        else
        {
            $msg .= $_L['at_least_one_item_required'].' <br> ';
        }

        $cust_name  = $_POST['cust_name'] ? $_POST['cust_name'] : 0;
        $cust_phone = $_POST['cust_phone'] ? $_POST['cust_phone'] : 0;
        
        // Auto-add +91 prefix if phone number is 10 digits
        if (strlen($cust_phone) == 10 && ctype_digit($cust_phone)) {
            $cust_phone = '91' . $cust_phone;
        }

        $cust_location = $_POST['cust_location'] ? $_POST['cust_location'] : 0;
        $cust_length = $_POST['cust_length'] ? $_POST['cust_length'] : 0;
        $cust_shoulder = $_POST['cust_shoulder'] ? $_POST['cust_shoulder'] : 0;
        $cust_sleeves = $_POST['cust_sleeves'] ? $_POST['cust_sleeves'] : 0;
        $cust_armole = $_POST['cust_armole'] ? $_POST['cust_armole'] : 0;
        $cust_cuff = $_POST['cust_cuff'] ? $_POST['cust_cuff'] : 0;
        $cust_chest = $_POST['cust_chest'] ? $_POST['cust_chest'] : 0;
        $cust_waist = $_POST['cust_waist'] ? $_POST['cust_waist'] : 0;
        $cust_hipps = $_POST['cust_hipps'] ? $_POST['cust_hipps'] : 0;         
        $d_measure  =  $_POST['d_measure']; 
        if ($cid == '') 
        {  
            $measurements = array('length' => $cust_length, 'shoulder' => $cust_shoulder, 'sleeves' => $cust_sleeves, 'armole' => $cust_armole, 'cuff' => $cust_cuff, 'chest' => $cust_chest, 'waist' => $cust_waist, 'hipps' => $cust_hipps);             
            $crm_accounts = ORM::for_table('crm_accounts')->create();
            $crm_accounts->account = $cust_name;
            $crm_accounts->phone = $cust_phone;
            $crm_accounts->address = $cust_location;
            $crm_accounts->status = 'Active';
            $crm_accounts->gname = 'Customer';
            $crm_accounts->gid = 1;
            $crm_accounts->measurements = json_encode($measurements);
            $crm_accounts->branch_id = $company_branch_id;
            $crm_accounts->save();
            $cid = $crm_accounts->id();
        }
        else
        {
            $measurements = array('length' => $cust_length, 'shoulder' => $cust_shoulder, 'sleeves' => $cust_sleeves, 'armole' => $cust_armole, 'cuff' => $cust_cuff, 'chest' => $cust_chest, 'waist' => $cust_waist, 'hipps' => $cust_hipps);                
            $crm_accounts = ORM::for_table('crm_accounts')->find_one($cid);
            $crm_accounts->account = $cust_name;
            $crm_accounts->phone = $cust_phone;
            $crm_accounts->address = $cust_location;
            $crm_accounts->measurements = json_encode($measurements);
            $crm_accounts->save();
            $cid = $crm_accounts->id();
        }

        $u = ORM::for_table('crm_accounts')->find_one($cid);

		/*if ($company == '') {
            $msg .= $_L['select_a_company'].' <br> ';
        }*/
        $notes = _post('notes');

        // find currency
        $currency_id = _post('currency');
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

        /*if (isset($_POST['amount'])) {
            $amount = $_POST['amount'];
        } else {
            $msg .= $_L['at_least_one_item_required'].' <br> ';
        }*/

        $idate = _post('idate');
        $its = strtotime($idate);
        $duedate = _post('duedate');
        $dd =  _post('duedate');
        /*$dd = '';
        if ($duedate == 'due_on_receipt') {
            $dd = $idate;
        } elseif ($duedate == 'days3') {
            $dd = date('Y-m-d', strtotime('+3 days', $its));

        } elseif ($duedate == 'days5') {
            $dd = date('Y-m-d', strtotime('+5 days', $its));
        } elseif ($duedate == 'days7') {
            $dd = date('Y-m-d', strtotime('+7 days', $its));
        } elseif ($duedate == 'days10') {
            $dd = date('Y-m-d', strtotime('+10 days', $its));
        } elseif ($duedate == 'days15') {
            $dd = date('Y-m-d', strtotime('+15 days', $its));
        } elseif ($duedate == 'days30') {
            $dd = date('Y-m-d', strtotime('+30 days', $its));
        } elseif ($duedate == 'days45') {
            $dd = date('Y-m-d', strtotime('+45 days', $its));
        } elseif ($duedate == 'days60') {
            $dd = date('Y-m-d', strtotime('+60 days', $its));
        } else {
            $msg .= 'Invalid Date <br> ';
        }
        if (!$dd) {
            $msg .= 'Date Parsing Error <br> ';
        }*/

        $repeat = _post('repeat');
        $nd = $idate;
        if ($repeat == '0') {
            $r = '0';
        } elseif ($repeat == 'week1') {
            $r = '+1 week';
            $nd = date('Y-m-d', strtotime('+1 week', $its));
        } elseif ($repeat == 'weeks2') {
            $r = '+2 weeks';
            $nd = date('Y-m-d', strtotime('+2 weeks', $its));
        } elseif ($repeat == 'month1') {
            $r = '+1 month';
            $nd = date('Y-m-d', strtotime('+1 month', $its));
        } elseif ($repeat == 'months2') {
            $r = '+2 months';
            $nd = date('Y-m-d', strtotime('+2 months', $its));
        } elseif ($repeat == 'months3') {
            $r = '+3 months';
            $nd = date('Y-m-d', strtotime('+3 months', $its));
        } elseif ($repeat == 'months6') {
            $r = '+6 months';
            $nd = date('Y-m-d', strtotime('+6 months', $its));
        } elseif ($repeat == 'year1') {
            $r = '+1 year';
            $nd = date('Y-m-d', strtotime('+1 year', $its));
        } elseif ($repeat == 'years2') {
            $r = '+2 years';
            $nd = date('Y-m-d', strtotime('+2 years', $its));
        } elseif ($repeat == 'years3') {
            $r = '+3 years';
            $nd = date('Y-m-d', strtotime('+3 years', $its));
        } else {
            $msg .= 'Date Parsing Error <br> ';
        }
				$inv_prefix = 'AB';
				$inv_no = 0;
        if ($msg == '') {
						
						$cn = _post('cn');
						$sTotal = 0.00; $taxTotal = 0; $prod_total = 0;
						for($i=0;$i<count($_POST['desc']);$i++ ){
								$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
								$taxrate = $ptaxrate['rate'];
								$taxamt = $_POST['total'][$i]*$taxrate/100;
								$taxTotal = $taxTotal + $taxamt;
								$sTotal += $taxamt + $_POST['total'][$i];
								$prod_total = $prod_total + $_POST['total'][$i];
						}
						
						//exit;
					 $fTotal = $sTotal;
			// calculate discount
            $discount_amount = _post('discount_amount');
            $discount_type = _post('discount_type');
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

            /*
            $inv_no = ORM::for_table('sys_invoices')->where('company_id', $company)->max('invoice_no');
			
    		$inv_no = $inv_no+1;
    				
    		if($company == 1){
    			$inv_prefix = 'PME';
    		}
    		$invoicenum = $inv_prefix.$inv_no;
    		*/
    		
            // Start transaction Lock the table to prevent race conditions
            ORM::get_db()->beginTransaction();
            ORM::get_db()->exec("LOCK TABLES sys_invoices WRITE");
            $inv_no = ORM::for_table('sys_invoices')->max('invoice_no');
			$inv_no = $inv_no+1;
			if($company == 1){
				$inv_prefix = 'PME';
			}
			$invoicenum = $inv_prefix.$inv_no;
            ORM::get_db()->exec("UNLOCK TABLES");
            ORM::get_db()->commit();
            
            $datetime = date("Y-m-d H:i:s");
            $vtoken = _raid(10);
            $ptoken = _raid(10);

            //save sample image
            $img_array = array();
            for($i=0; $i<count($_FILES['additional_imgs']); $i++ )
            {
                if($_FILES['additional_imgs']["name"][$i])
                {
                    $filename = 'ui/lib/imgs/additional-img/'.time().$i.'.jpg';
                    $img_array[] = $filename;
                    move_uploaded_file($_FILES['additional_imgs']["tmp_name"][$i], $filename);
                }                
            } 
            		
			//save to sys_invoices
            $d = ORM::for_table('sys_invoices')->create();
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
						$d->taxid = $_POST['taxed'];
            //others
            $d->paymentmethod = '';
            $d->currency = $currency;
            $d->currency_symbol = $currency_symbol;
            $d->currency_rate = $currency_rate;
            $d->created_by = $user['id'];
            $d->d_measure  = $d_measure;
            $d->additional_imgs = json_encode($img_array);
            $d->updated_at = time();
            $d->created_at = time();
            $d->save(); //save
            $invoiceid = $d->id();
 
            $branch_id = _post('company') ? _post('company') : 2;

						for($i=0;$i<count($_POST['desc']);$i++ ){
								$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
								
							    $d = ORM::for_table('sys_invoiceitems')->create();
								$d->invoiceid = $invoiceid;
								$d->userid = $cid;
								$d->itemcode = $_POST['s_id'][$i];
								$d->description = $_POST['desc'][$i];
								$d->qty = Finance::amount_fix($_POST['qty'][$i]);
								$d->amount = Finance::amount_fix($_POST['amount'][$i]);
								//get tax rate by id
								$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
								//var_dump($ptaxrate['rate']);
								$d->taxrate = $ptaxrate['rate'];
								$d->tax_id = $_POST['taxed'];
								$taxamt = $_POST['total'][$i]*$ptaxrate['rate']/100;
								$d->taxamount = Finance::amount_fix($taxamt);
								$d->total = Finance::amount_fix($taxamt + $_POST['total'][$i]);
								//others
								$d->type = '';
								$d->relid = '0';
								$d->duedate = date('Y-m-d');
								$d->paymentmethod = '';						
                                $d->notes = '';
                                
                                $d->item_type = $_POST['item_type'][$i];   
                                
                                $d->product_id = ($d->item_type == 'product') ? $_POST['p_id'][$i] : null;
                                $d->design_id = ($d->item_type == 'design') ? $_POST['p_id'][$i] : null; 
                                
                                stock_record($d->product_id, $d->qty, 'debit', $d->invoiceid, '', '', '', $branch_id, ''); //newly added

                                if(!empty($_POST['pimg'][$i]))
                                {
                                    $get_img = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$_POST['pimg'][$i]);
                                    $set_name = time().$i.'.jpg';
                                    $inv_img_path = $_SERVER['DOCUMENT_ROOT'].'/ui/lib/imgs/invoice-contents/'.$set_name;
                                    file_put_contents($inv_img_path, $get_img);

                                    compressImage($inv_img_path, $inv_img_path, 30);
                                    
                                    $d->item_img = $set_name; //$inv_img_path;
                                }

								$d->save(); //save
                        } 
                        
                        $other_l   = array();
                        $other_l[] = ($_POST['is_pocket'] == 'yes') ? 'pocket' : null;
                        $other_l[] = ($_POST['is_zip'] == 'yes') ? 'zip' : null;
                        $other_l[] = ($_POST['is_beading'] == 'yes') ? 'beading' : null;
                        $other_l[] = ($_POST['is_folding'] == 'yes') ? 'folding' : null;
                        $other_l[] = ($_POST['is_pico'] == 'yes') ? 'pico' : null;
                        $other_l   = array_filter($other_l);
                        
                        foreach($other_l as $other)
                        {
                            $d = ORM::for_table('sys_invoiceitems')->create();
                            $d->invoiceid = $invoiceid;
                            $d->userid = $cid;
                            $d->itemcode = 0;
                            $d->description = $other;
                            $d->qty = 1;
                            $d->amount = 0;
                            $ptaxrate = 0;
                            $d->taxrate = 0;
                            $d->tax_id = 0;
                            $taxamt = 0;
                            $d->taxamount = 0;
                            $d->total = 0;
                            $d->relid = '0';
                            $d->duedate = date('Y-m-d');
                            
                            $d->item_type = $other;    
                            $d->product_id = null;
                            $d->design_id = null;                                                              
                            $d->save(); //save
                        }

                        /*if($_POST['is_pocket'] == 'yes')
                        {
								//$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
								
							    $d = ORM::for_table('sys_invoiceitems')->create();
								$d->invoiceid = $invoiceid;
								$d->userid = $cid;
								$d->itemcode = 0;
								$d->description = 'Pocket';
								$d->qty = 1;
								$d->amount = 0;
								$ptaxrate = 0;
								$d->taxrate = 0;
								$d->tax_id = 0;
								$taxamt = 0;
								$d->taxamount = 0;
								$d->total = 0;
								$d->relid = '0';
                                $d->duedate = date('Y-m-d');
                                
                                $d->item_type = 'pocket';    
                                $d->product_id = null;
                                $d->design_id = null;                                                              
								$d->save(); //save
                        }

                        if($_POST['is_zip'] == 'yes')
                        {
								//$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
								
							    $d = ORM::for_table('sys_invoiceitems')->create();
								$d->invoiceid = $invoiceid;
								$d->userid = $cid;
								$d->itemcode = 0;
								$d->description = 'Zip';
								$d->qty = 1;
								$d->amount = 0;
								$ptaxrate = 0;
								$d->taxrate = 0;
								$d->tax_id = 0;
								$taxamt = 0;
								$d->taxamount = 0;
								$d->total = 0;
								$d->relid = '0';
                                $d->duedate = date('Y-m-d');
                                
                                $d->item_type = 'zip';  
                                $d->product_id = null;
                                $d->design_id = null;                                  
                                

								$d->save(); //save
                        }*/ 
                        
                        if($_POST['is_umbrella'] == 'yes')
                        {
								//$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
								
							    $d = ORM::for_table('sys_invoiceitems')->create();
								$d->invoiceid = $invoiceid;
								$d->userid = $cid;
								$d->itemcode = 0;
								$d->description = 'Umbrella (Size : '.$_POST['umbrella_size'].')';
								$d->qty = 1;
								$d->amount = 0;
								$ptaxrate = 0;
								$d->taxrate = 0;
								$d->tax_id = 0;
								$taxamt = 0;
								$d->taxamount = 0;
								$d->total = 0;
								$d->relid = '0';
                                $d->duedate = date('Y-m-d');
                                
                                $d->item_type = 'umbrella';  
                                $d->product_id = null;
                                $d->design_id = null;                                                                 

								$d->save(); //save
                        } 
                        
                        if($_POST['is_dupatta'] == 'yes')
                        {
								//$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
								
							    $d = ORM::for_table('sys_invoiceitems')->create();
								$d->invoiceid = $invoiceid;
								$d->userid = $cid;
								$d->itemcode = 0;
								$d->description = 'Dupatta (Size : '.$_POST['dupatta_size'].')';
								$d->qty = 1;
								$d->amount = 0;
								$ptaxrate = 0;
								$d->taxrate = 0;
								$d->tax_id = 0;
								$taxamt = 0;
								$d->taxamount = 0;
								$d->total = 0;
								$d->relid = '0';
                                $d->duedate = date('Y-m-d');
                                
                                $d->item_type = 'dupatta';  
                                $d->product_id = null;
                                $d->design_id = null;   
                                
                                $d->save(); //save
                        }
            $branch_id = _post('company_branch_id') ? _post('company_branch_id') : 2;
            //advance amount            
            if( _post('advance_amount') ){
                $d = ORM::for_table('sys_invoices')->find_one($invoiceid);
    			
                //set data
                $sys_acc = ORM::for_table('sys_accounts')->find_one($branch_id);
                // $sys_acc = ORM::for_table('sys_accounts')->find_one(2);
                $account     = $sys_acc['account'];
                $date        = date('Y-m-d'); 
                $payerid     = $d['userid'];
                $pmethod     = _post('advance_method') ? _post('advance_method') : 'Advance Payment';
                $ref         = "";
                $amount      = _post('advance_amount'); //$amount      = $d['subtotal'] - $d['credit'];
                $cat         = "Advance Payment";
                $iid         = $d['id']; 
                $invoicenum  = $d['invoicenum']; 
                $description = "Invoice $invoicenum Payment";
                
                //find the current balance for this account
                $a = ORM::for_table('sys_accounts')->where('account', $account)->find_one();
                $cbal = $a['balance'];
                $nbal = $cbal + $amount;
                $a->balance = $nbal;
                $a->save();
                
                //save in transactions
                $t = ORM::for_table('sys_transactions')->create();
                $t->branch_id = $branch_id;
                $t->account = $account;
                $t->type = 'Income';
                $t->payerid = $payerid;
                $t->amount = $amount;
                $t->category = $cat;
                $t->method = $pmethod;
                $t->ref = $ref;
                $t->tags = '';
                $t->description = $description;
                $t->date = $date;
                $t->dr = '0.00';
                $t->cr = $amount;
                $t->bal = $nbal;
                $t->iid = $iid;
                $t->payer = '';
                $t->payee = '';
                $t->payeeid = '0';
                $t->status = 'Cleared';
                $t->tax = '0.00';
                $t->save();   
                
                //invoice update
                $id = $d['id'];
    			$vtoken = $d['vtoken'];                
                $pc = $d['credit'];
                $it = $d['subtotal'];
                $dp = $it - $pc;
                if (($dp == $amount) OR (($dp < $amount))) {
                    $d->status = 'Paid';
                }else{
                    $d->status = 'Partially Paid';
                }            
                
                $d->credit = $d->credit + $amount;            
                $d->save();                 
            }                        
                        

            Event::trigger('add_invoice_posted');

            echo $invoiceid;
				}else {
            echo $msg;
        }

        break;
        
        case 'redirect_to_customer_invoice_pdf':        
            $invoiceId = $routes['2'];
          $i = ORM::for_table('sys_invoices')->find_one($invoiceId);
          $_token = $i->vtoken;
          r2(U . "client/ipdf/$invoiceId/token_$_token/view/", 's', '');
        break;         
        
    /*case 'list':

        Event::trigger('invoices/list/');

        $paginator = array();

        $mode_css = '';
        $mode_js = '';
        $view_type = 'default';

        if(route(2) == 'filter'){
            $view_type = 'filter';
            $mode_css = Asset::css('footable/css/footable.core.min');
            $mode_js = Asset::js(array('numeric','footable/js/footable.all.min','contacts/mode_search'));
            $total_invoice = ORM::for_table('sys_invoices')->count();
            $ui->assign('total_invoice',$total_invoice);
            $ui->assign('inv_count',$_GET['count']);
            $f = ORM::for_table('sys_invoices');
            if(route(3) != ''){
                $s_f = route(3);
                $ui->assign('s_f',$s_f);
                if($s_f == 'paid'){
                    $f->where('status','Paid');
                }
                elseif ($s_f == 'unpaid'){
                    $f->where('status','Unpaid');
                }
                elseif ($s_f == 'partially_paid'){
                    $f->where('status','Partially Paid');
                }
                elseif ($s_f == 'cancelled'){
                    $f->where('status','Cancelled');
                }
                elseif ($s_f == 'pending'){
                    $f->where('delivery_status','pending');
                } 
                elseif ($s_f == 'processing'){
                    $f->where('delivery_status','processing');
                } 
                elseif ($s_f == 'completed'){
                    $f->where('delivery_status','completed');
                } 
                elseif ($s_f == 'delivered'){
                    $f->where('delivery_status','delivered');
                }  
                elseif ($s_f == 'overdue'){
                    $f->where_lte('duedate', date('Y-m-d'));
                    $f->where_in('delivery_status', array('pending', 'processing'));
                    //$f->where('delivery_status', 'processing');      
                }                                                               
                else{
                }
            }
            $d = $f->order_by_desc('id')->find_many();
            $paginator['contents'] = '';
        }
        else{
//            $ui->assign('xfooter', Asset::js(array('numeric')));
            $mode_js = Asset::js(array('numeric'));
            $paginator = Paginator::bootstrap('sys_invoices');
            $d = ORM::for_table('sys_invoices')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('id')->find_many();
        }

        $ui->assign('_st', $_L['Invoices'].'<div class="btn-group pull-right" style="padding-right: 10px;">
  <a class="btn btn-success btn-xs" href="'.U.'invoices/add/'.'" style="box-shadow: none;"><i class="fa fa-plus"></i></a>
  <a class="btn btn-primary btn-xs" href="'.U.'invoices/add/'.'" style="box-shadow: none;"><i class="fa fa-repeat"></i></a>
  <a class="btn btn-success btn-xs" href="'.U.'invoices/export_csv/'.'" style="box-shadow: none;"><i class="fa fa-download"></i></a>
</div>');

        $ui->assign('xheader', $mode_css);
        $ui->assign('xfooter', $mode_js);
        $ui->assign('view_type', $view_type);

        $ui->assign('d', $d);
        $ui->assign('paginator', $paginator);
        $ui->assign('xjq', '
         $(\'.amount\').autoNumeric(\'init\', {

   
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });
$(".cdelete").click(function (e) {
        e.preventDefault();
        var id = this.id;
        bootbox.confirm("'.$_L['are_you_sure'].'", function(result) {
           if(result){
               var _url = $("#_url").val();
               window.location.href = _url + "delete/invoice/" + id;
           }
        });
    });



 ');


        $ui->display('list-invoices.tpl');
        break;  
*/

    // ----------------------
// invoices controller
// ----------------------

    case 'list':

        Event::trigger('invoices/list/');

        // Load branches for branch filter dropdown
        $branches = ORM::for_table('sys_accounts')->select('id')->select('account')->select('alias')->order_by_asc('account')->find_array();

        $ui->assign('branches', $branches);

        $mode_js = Asset::js(array('datatables.min', 'dataTables.buttons.min', 'buttons.print.min', 'numeric','footable/js/footable.all.min','contacts/mode_search'));

        // load DataTables css/js assets
        $ui->assign('xheader', Asset::css(['datatables.min', 'buttons.dataTables.min']));
        $ui->assign('xfooter', $mode_js);

        $ui->assign('xjq', '
            $(\'.amount\').autoNumeric(\'init\', {        
                dGroup: '.$config['thousand_separator_placement'].',
                aPad: '.$config['currency_decimal_digits'].',
                pSign: \''.$config['currency_symbol_position'].'\',
                aDec: \''.$config['dec_point'].'\',
                aSep: \''.$config['thousands_sep'].'\'
            });
        ');

        // render new server-side template
        $ui->display('list-invoices2.tpl');

        break;


    case 'list-datatable':

        // DataTables server-side endpoint
        $request = $_REQUEST;

        // map datatable column index -> db expression
        // displayed columns: 0 Invoice No, 1 Customer, 2 Phone, 3 Amount, 4 Invoice Date, 5 Delivery Date, 6 Reminder Date,
        // 7 Payment Status, 8 Invoice Status, 9 Created By, 10 Updated At, 11 Created At, 12 Manage
        $columns = [
            0 => 't.id',         // invoice no (we will search invoice number fields separately)
            1 => 'c.account',    // customer
            2 => 'c.phone',
            3 => 't.subtotal',
            4 => 't.date',
            5 => 't.duedate',
            6 => 't.reminder_date',
            7 => 't.status',
            8 => 't.delivery_status', // use this as Invoice Status
            9 => 'u.fullname',
            10 => 't.updated_at',
            11 => 't.created_at'
        ];

        // total records (no filters)
        $totalData = (int) ORM::for_table('sys_invoices')->count();

        // pagination params
        $length = isset($request['length']) ? (int)$request['length'] : 25;
        $start  = isset($request['start']) ? max(0, (int)$request['start']) : 0;

        // ordering
        $order_index = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 12; // default created_at
        $order_col = isset($columns[$order_index]) ? $columns[$order_index] : 't.created_at';
        $order_dir = (isset($request['order'][0]['dir']) && strtolower($request['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

        // Build base query (join customer and user and branch)
        $base_q = ORM::for_table('sys_invoices')->table_alias('t')
            ->select('t.*')
            ->select('c.account', 'customer_account')
            ->select('c.phone', 'customer_phone')
            ->select('c.company', 'customer_company')
            ->select('b.alias', 'branch_alias')
            ->select('u.fullname', 'created_by_name')
            ->join('crm_accounts', ['t.userid', '=', 'c.id'], 'c')
            ->left_outer_join('sys_accounts', ['t.company_id', '=', 'b.id'], 'b')
            ->left_outer_join('sys_users', ['t.created_by', '=', 'u.id'], 'u');

        // --- global search (DataTables search)
        if (!empty($request['search']['value'])) {
            $s = '%' . $request['search']['value'] . '%';
            // search across invoice number fields, customer account, phone, company
            $base_q->where_raw('(
                t.invoicenum LIKE ? OR
                t.cn LIKE ? OR
                c.account LIKE ? OR
                c.phone LIKE ? OR
                c.company LIKE ? OR
                b.alias LIKE ?
            )', [$s, $s, $s, $s, $s, $s]);
        }

        // --- filters from the form
        // if (!empty($request['branch_id'])) {
        //     $base_q->where('t.company_id', $request['branch_id']);
        // }

        // Assume $user is current logged-in user object
        $branch_id = $request['branch_id'];

        // Admin: allow any branch (or 'all')
        if ($user->roleid == 0) {
            if (!empty($branch_id) && $branch_id != 'all') {
                $base_q->where('t.company_id', $branch_id);
            }
        } else {
            // Non-admin: override branch_id to user's branch, ignore request value
            $branch_id = $user->branch_id;
            $base_q->where('t.company_id', $branch_id);
        }

        // invoice number exact or partial
        if (!empty($request['invoice_no'])) {
            $in = '%' . $request['invoice_no'] . '%';
            $base_q->where_raw('(t.invoicenum LIKE ? OR t.cn LIKE ?)', [$in, $in]);
        }

        // customer search (server side): search customer name or phone or company
        if (!empty($request['customer'])) {
            $csearch = '%' . $request['customer'] . '%';
            $base_q->where_raw('(c.account LIKE ? OR c.phone LIKE ? OR c.company LIKE ?)', [$csearch, $csearch, $csearch]);
        }

        // Type: which date field to filter by (invoice_date or delivery_date)
        if (!empty($request['type']) && !empty($request['date_from']) && !empty($request['date_to'])) {
            $df = $request['date_from'];
            $dt = $request['date_to'];
            if ($request['type'] === 'invoice_date') {
                $base_q->where_raw('t.date BETWEEN ? AND ?', [$df, $dt]);
            } else {
                // delivery_date
                $base_q->where_raw('t.duedate BETWEEN ? AND ?', [$df, $dt]);
            }
        } else {
            // fallback single-date filters if provided
            if (!empty($request['date_from']) && !empty($request['date_to'])) {
                $base_q->where_raw('t.date BETWEEN ? AND ?', [$request['date_from'], $request['date_to']]);
            }
        }

        // payment status (Paid, Unpaid, Partially Paid, Cancelled)
        if (!empty($request['payment_status'])) {
            $base_q->where('t.status', $request['payment_status']);
        }

        if (!empty($request['delivery_status'])) {
            if ($request['delivery_status'] === 'overdue') {
                $today = date('Y-m-d');
                $base_q->where_raw('(
                    t.duedate < ? 
                    AND (t.delivery_status = "pending" OR t.delivery_status = "processing")
                )', [$today]);
            } else {
                $base_q->where('t.delivery_status', $request['delivery_status']);
            }
        }


        // --- get filtered count (clone query to avoid mutation)
        $count_q = clone $base_q;
        $totalFiltered = (int) $count_q->count();

        // --- data query (clone, apply ordering and pagination)
        $data_q = clone $base_q;
        $data_q->order_by_expr("$order_col $order_dir");

        if ($length != -1) {
            $data_q->offset($start)->limit($length);
        }

        $rows = $data_q->find_array();

        // Build response rows
        $data = [];
        foreach ($rows as $r) {

            $invoice_no_display = ($r['cn'] !== '') ? $r['cn'] : $r['invoicenum'];
            $invoice_link = '<a href="'. U .'invoices/view/'. $r['id'] .'/">'.$invoice_no_display.'</a>';

            // amount formatting
            $amount_formatted = number_format((float)$r['subtotal'], 2, '.', '');

            // created by fallback
            $created_by = $r['created_by_name'] ?: get_type_by_id('sys_users', 'id', $r['created_by'], 'fullname');

            $manage_html = '';
            // you can conditionally show edit/delete based on $r['status'] or permissions
            $manage_html .= '<a href="'. U .'invoices/view/'. $r['id'] .'/" class="btn btn-primary btn-xs"><i class="fa fa-check"></i> '.$_L['View'].'</a> ';
            if ($r['status'] === 'Unpaid') {
                $manage_html .= '<a href="'. U .'invoices/edit/'. $r['id'] .'/" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> '.$_L['Edit'].'</a> ';
            }
            if (empty($r['sale_trans_code'])) {
                $manage_html .= '<a href="#" class="btn btn-danger btn-xs cdelete" id="iid'.$r['id'].'"><i class="fa fa-trash"></i> '.$_L['Delete'].'</a>';
            }

            $data[] = [
                $invoice_link,
                htmlspecialchars($r['customer_account'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($r['customer_phone'], ENT_QUOTES, 'UTF-8'),
                '<span class="amount">'. $amount_formatted .'</span>',
                date($_c['df'], strtotime($r['date'])),
                date($_c['df'], strtotime($r['duedate'])),
                !empty($r['reminder_date']) ? $r['reminder_date'] : '',
                htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($r['delivery_status'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($created_by, ENT_QUOTES, 'UTF-8'),
                !empty($r['updated_at']) ? date('Y-m-d H:i A', $r['updated_at']) : '',
                !empty($r['created_at']) ? date('Y-m-d H:i A', $r['created_at']) : '',
                $manage_html
            ];
        }

        // JSON response
        $response = [
            'draw' => intval($request['draw'] ?? 0),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);

        break;



    case 'list-proforma':

        Event::trigger('invoices/list-proforma/');
        $paginator = array();

        $mode_css = '';
        $mode_js = '';
        $view_type = 'default';

        if(route(2) == 'filter'){
            $view_type = 'filter';
            $mode_css = Asset::css('footable/css/footable.core.min');
            $mode_js = Asset::js(array('numeric','footable/js/footable.all.min','contacts/mode_search'));
            $total_invoice = ORM::for_table('sys_performa')->count();
            $ui->assign('total_invoice',$total_invoice);
            $f = ORM::for_table('sys_performa');
            if(route(3) != ''){
                $s_f = route(3);
                if($s_f == 'paid'){
                    $f->where('status','Paid');
                }
                elseif ($s_f == 'unpaid'){
                    $f->where('status','Unpaid');
                }
                elseif ($s_f == 'partially_paid'){
                    $f->where('status','Partially Paid');
                }
                elseif ($s_f == 'cancelled'){
                    $f->where('status','Cancelled');
                }
                else{
                }
            }
            $d = $f->order_by_desc('id')->find_many();
            $paginator['contents'] = '';
        }
        else{
//            $ui->assign('xfooter', Asset::js(array('numeric')));
            $mode_js = Asset::js(array('numeric'));
            $paginator = Paginator::bootstrap('sys_performa');
            $d = ORM::for_table('sys_performa')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('id')->find_many();
        }

        $ui->assign('_st', 'Proformas'.'<div class="btn-group pull-right" style="padding-right: 10px;">
  <a class="btn btn-success btn-xs" href="'.U.'invoices/add/'.'" style="box-shadow: none;"><i class="fa fa-plus"></i></a>
  <a class="btn btn-primary btn-xs" href="'.U.'invoices/add/'.'" style="box-shadow: none;"><i class="fa fa-repeat"></i></a>
  <a class="btn btn-success btn-xs" href="'.U.'invoices/export_csv/'.'" style="box-shadow: none;"><i class="fa fa-download"></i></a>
</div>');

        $ui->assign('xheader', $mode_css);
        $ui->assign('xfooter', $mode_js);
        $ui->assign('view_type', $view_type);

        $ui->assign('d', $d);
        $ui->assign('paginator', $paginator);
        $ui->assign('xjq', '
         $(\'.amount\').autoNumeric(\'init\', {

   
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });

$(".cdelete_proforma").click(function (e) {
        e.preventDefault();
        var id = this.id;
        bootbox.confirm("'.$_L['are_you_sure'].'", function(result) {
           if(result){
               var _url = $("#_url").val();
               window.location.href = _url + "delete/proforma/" + id;
           }
        });
    });



 ');


        $ui->display('list-proforma.tpl');
        break;  

		case 'followup':

        Event::trigger('invoices/followup/');

        $paginator = array();

        $mode_css = '';
        $mode_js = '';
        $view_type = 'default';

        if(route(2) == 'filter'){
            $view_type = 'filter';
            $mode_css = Asset::css('footable/css/footable.core.min');
            $mode_js = Asset::js(array('numeric','footable/js/footable.all.min','contacts/mode_search'));
            $total_invoice = ORM::for_table('sys_invoices')->count();
            $ui->assign('total_invoice',$total_invoice);
            $f = ORM::for_table('sys_invoices');
            if(route(3) != ''){
                $s_f = route(3);
                if($s_f == 'paid'){
                    $f->where('status','Paid');
                }
                elseif ($s_f == 'unpaid'){
                    $f->where('status','Unpaid');
                }
                elseif ($s_f == 'partially_paid'){
                    $f->where('status','Partially Paid');
                }
                elseif ($s_f == 'cancelled'){
                    $f->where('status','Cancelled');
                }
                else{
                }
            }
            $d = $f->order_by_desc('id')->find_many();
            $paginator['contents'] = '';
        }
        else{
//            $ui->assign('xfooter', Asset::js(array('numeric')));
            $mode_js = Asset::js(array('numeric'));
            $paginator = Paginator::bootstrap('sys_invoices');
            $d = ORM::for_table('sys_invoices')->where_not_equal('status','Paid')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('duedate')->find_many();
						foreach($d as $row){	
							$row['phone']=ORM::for_table('crm_accounts')->where('id', $row['userid'])->find_one()->phone; 		
				}			
        }

        $ui->assign('_st', $_L['Invoices'].'<div class="btn-group pull-right" style="padding-right: 10px;">
  <a class="btn btn-success btn-xs" href="'.U.'invoices/add/'.'" style="box-shadow: none;"><i class="fa fa-plus"></i></a>
  <a class="btn btn-primary btn-xs" href="'.U.'invoices/add/'.'" style="box-shadow: none;"><i class="fa fa-repeat"></i></a>
  <a class="btn btn-success btn-xs" href="'.U.'invoices/export_csv/'.'" style="box-shadow: none;"><i class="fa fa-download"></i></a>
</div>');

        $ui->assign('xheader', $mode_css);
        $ui->assign('xfooter', $mode_js);
        $ui->assign('view_type', $view_type);

        $ui->assign('d', $d);
        $ui->assign('paginator', $paginator);
        $ui->assign('xjq', '
         $(\'.amount\').autoNumeric(\'init\', {

   
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });
$(".cdelete").click(function (e) {
        e.preventDefault();
        var id = this.id;
        bootbox.confirm("'.$_L['are_you_sure'].'", function(result) {
           if(result){
               var _url = $("#_url").val();
               window.location.href = _url + "delete/invoice/" + id;
           }
        });
    });



 ');


        $ui->display('follow-invoices.tpl');
        break;

    case 'list-recurring':

        $d = ORM::for_table('sys_invoices')->where_not_equal('r', '0')->order_by_desc('id')->find_many();
        $ui->assign('d', $d);
        $ui->assign('xjq', '
$(".cdelete").click(function (e) {
        e.preventDefault();
        var id = this.id;
        bootbox.confirm("'.$_L['are_you_sure'].'", function(result) {
           if(result){
               var _url = $("#_url").val();
               window.location.href = _url + "delete/invoice/" + id;
           }
        });
    });

     $(".cstop").click(function (e) {
        e.preventDefault();
        var id = this.id;
        bootbox.confirm("Are you sure? This will prevent future invoice generation from this invoice.", function(result) {
           if(result){
               var _url = $("#_url").val();
               window.location.href = _url + "invoices/stop_recurring/" + id;
           }
        });
    });

 ');
        $ui->display('list-recurring-invoices.tpl');
        break;

    case 'edit-post':

        Event::trigger('invoices/edit-post/');
				$cid = _post('cid');
				$iid = _post('iid');
				
        $idate = _post('idate');
        $its = strtotime($idate);
        $duedate = _post('duedate');
        $company = _post('company');
        

        $msg = '';
        if ($company == '')
        {
            $msg .= $_L['select_a_company'].' <br> ';
        }
        if (isset($_POST['amount']))
        {
            $amount = $_POST['amount'];
        }
        else
        {
            $msg .= $_L['at_least_one_item_required'].' <br> ';
        }

        if($_POST['cust_name'] == '')
        {
            $msg .= 'Customer Name is required <br> ';
        }

        /*if($_POST['cust_phone'] == '')
        {
            $msg .= 'Customer Phone is required <br> ';
        }*/
        
        if(strlen($_POST['cust_phone']) != 10 && strlen($_POST['cust_phone']) != 12)
        {
            $msg .= 'Enter a 10 or 12 digit contact number.<br>';
        }

        // if(strlen($_POST['cust_phone']) != 10)
        // {
        //     $msg .= 'Enter 10 digit contact number.<br> ';
        // }        
        
        if($_POST['cust_location'] == '')
        {
            $msg .= 'Location is required <br> ';
        }  
        
        if($msg != '')
        {
           echo $msg;exit;
        }

        $cust_name  = $_POST['cust_name'] ? $_POST['cust_name'] : 0;
        $cust_phone = $_POST['cust_phone'] ? $_POST['cust_phone'] : 0;
        
        // Auto-add +91 prefix if phone number is 10 digits
        if (strlen($cust_phone) == 10 && ctype_digit($cust_phone)) {
            $cust_phone = '91' . $cust_phone;
        }

        $cust_location = $_POST['cust_location'] ? $_POST['cust_location'] : 0;
        $cust_length = $_POST['cust_length'] ? $_POST['cust_length'] : 0;
        $cust_shoulder = $_POST['cust_shoulder'] ? $_POST['cust_shoulder'] : 0;
        $cust_sleeves = $_POST['cust_sleeves'] ? $_POST['cust_sleeves'] : 0;
        $cust_armole = $_POST['cust_armole'] ? $_POST['cust_armole'] : 0;
        $cust_cuff = $_POST['cust_cuff'] ? $_POST['cust_cuff'] : 0;
        $cust_chest = $_POST['cust_chest'] ? $_POST['cust_chest'] : 0;
        $cust_waist = $_POST['cust_waist'] ? $_POST['cust_waist'] : 0;
        $cust_hipps = $_POST['cust_hipps'] ? $_POST['cust_hipps'] : 0; 
        $d_measure  = $_POST['d_measure'];      
        
        if ($cid == '') 
        {  
            $measurements = array('length' => $cust_length, 'shoulder' => $cust_shoulder, 'sleeves' => $cust_sleeves, 'armole' => $cust_armole, 'cuff' => $cust_cuff, 'chest' => $cust_chest, 'waist' => $cust_waist, 'hipps' => $cust_hipps);             
            $crm_accounts = ORM::for_table('crm_accounts')->create();
            $crm_accounts->account = $cust_name;
            $crm_accounts->phone = $cust_phone;
            $crm_accounts->address = $cust_location;
            $crm_accounts->status = 'Active';
            $crm_accounts->gname = 'Customer';
            $crm_accounts->gid = 1;
            $crm_accounts->measurements = json_encode($measurements);
            $crm_accounts->save();
            $cid = $crm_accounts->id();
        }
        else
        {
            $measurements = array('length' => $cust_length, 'shoulder' => $cust_shoulder, 'sleeves' => $cust_sleeves, 'armole' => $cust_armole, 'cuff' => $cust_cuff, 'chest' => $cust_chest, 'waist' => $cust_waist, 'hipps' => $cust_hipps);                
            $crm_accounts = ORM::for_table('crm_accounts')->find_one($cid);
            $crm_accounts->account = $cust_name;
            $crm_accounts->phone = $cust_phone;
            $crm_accounts->address = $cust_location;
            $crm_accounts->measurements = json_encode($measurements);
            $crm_accounts->save();
            $cid = $crm_accounts->id();
        }        


        $u = ORM::for_table('crm_accounts')->find_one($cid);
				
        $notes = _post('notes');

        // find currency
        $currency_id = _post('currency');
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

        $repeat = _post('repeat');
        $nd = $idate;
        if ($repeat == '0') {
            $r = '0';
        } elseif ($repeat == 'week1') {
            $r = '+1 week';
            $nd = date('Y-m-d', strtotime('+1 week', $its));
        } elseif ($repeat == 'weeks2') {
            $r = '+2 weeks';
            $nd = date('Y-m-d', strtotime('+2 weeks', $its));
        } elseif ($repeat == 'month1') {
            $r = '+1 month';
            $nd = date('Y-m-d', strtotime('+1 month', $its));
        } elseif ($repeat == 'months2') {
            $r = '+2 months';
            $nd = date('Y-m-d', strtotime('+2 months', $its));
        } elseif ($repeat == 'months3') {
            $r = '+3 months';
            $nd = date('Y-m-d', strtotime('+3 months', $its));
        } elseif ($repeat == 'months6') {
            $r = '+6 months';
            $nd = date('Y-m-d', strtotime('+6 months', $its));
        } elseif ($repeat == 'year1') {
            $r = '+1 year';
            $nd = date('Y-m-d', strtotime('+1 year', $its));
        } elseif ($repeat == 'years2') {
            $r = '+2 years';
            $nd = date('Y-m-d', strtotime('+2 years', $its));
        } elseif ($repeat == 'years3') {
            $r = '+3 years';
            $nd = date('Y-m-d', strtotime('+3 years', $its));
        } else {
            $msg .= 'Date Parsing Error <br> ';
        }
$inv_prefix = '';
        if ($msg == '') {

						$cn = _post('cn');
						$sTotal = 0; $taxTotal = 0; $prod_total = 0;
						for($i=0;$i<count($_POST['desc']);$i++ ){
								$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
								$taxrate = $ptaxrate['rate'];//$_POST['taxed'][$i];
								$taxamt = $_POST['total'][$i]*$taxrate/100;
								$taxTotal = $taxTotal + $taxamt;
								$sTotal += $taxamt + $_POST['total'][$i];
								$prod_total = $prod_total + $_POST['total'][$i];
						}
						//exit;
					 $fTotal = $sTotal;
						// calculate discount
            $discount_amount = _post('discount_amount');
            $discount_type = _post('discount_type');
            $discount_value = '0.00';
 
            if($discount_amount == '0' OR $discount_amount == ''){
                $actual_discount = '0.00';
            }
            else{
                if($discount_type == 'f'){
                    $actual_discount = $discount_amount;
                    $discount_value = $discount_amount;
                }
                else{
                    $discount_type = 'p';
                    $actual_discount = ($sTotal * $discount_amount) / 100;
                    $discount_value = $discount_amount;
                }
            }
						//var_dump($discount_value);exit;
            $actual_discount = number_format((float)$actual_discount, 2, '.', '');

            $fTotal = $fTotal - $actual_discount;

           /*  $actual_taxed_amount = $taxed_amount - $actual_discount;
				
            if($actual_taxed_amount > 0){
                $taxval = ($actual_taxed_amount * $taxrate) / 100;
            }
 
            if (($taxed_type != 'individual') AND ($tax != '')) {
                $taxval = ($fTotal * $taxrate) / 100;
            }

           // $inv_no = ORM::for_table('sys_invoices')->where('company_id', $company)->max('invoice_no');
		
						if($inv_no == null){
							$inv_no = 1;
						}else{
							$inv_no = $inv_no;
											
						}
						
						if($company == 1){
							$inv_prefix = 'ME';
						}elseif($company == 2){
							$inv_prefix = 'MI';
						}else{
							
						} */
						//$invoicenum = $inv_prefix.date('ymd').$inv_no;
						//$invoicenum = $ciid;
            $datetime = date("Y-m-d H:i:s");

            $vtoken = _raid(10);
            $ptoken = _raid(10);

            //save sample image
            $img_array = array();

            for($i=0; $i<count($_POST['existing_additional_imgs']); $i++ )
            {
                $img_array[] = $_POST['existing_additional_imgs'][$i];              
            }

            for($i=0; $i<count($_FILES['additional_imgs']); $i++ )
            {
                if($_FILES['additional_imgs']["name"][$i])
                {
                    $filename = 'ui/lib/imgs/additional-img/'.time().$i.'.jpg';
                    $img_array[] = $filename;
                    move_uploaded_file($_FILES['additional_imgs']["tmp_name"][$i], $filename);
                }                
            }            
						
						//update to sys_invoices
            $d = ORM::for_table('sys_invoices')->find_one($iid);
						$status = "";
						if($d['credit']!=0.00){
							$status = "Partially Paid";
						}else{
							$status = "Unpaid";
						}
						if ($d) {
							$d->set(array(
								'userid'		 			=> $cid,
								'account'  				=> $u['account'],
								 'company_id'  		=> $company,
								'cn'							=> $cn,
								'date' 						=> $idate,
								'duedate' 				=> $duedate,
								'datepaid' 				=> $datetime,
								'total' 					=> Finance::amount_fix($prod_total),
								'subtotal' 				=> Finance::amount_fix($sTotal-$actual_discount),
								'discount_type'   => $discount_type,
								'discount_value'  => $discount_value,
								'discount'        => $actual_discount,
								'vtoken' 					=> $vtoken,
								'ptoken' 					=> $ptoken,
								'status' 					=> $status,
								'notes' 					=> $notes,
								'r' 							=> $r,
								'nd' 							=> $nd,
								'taxamt' 					=> Finance::amount_fix($taxTotal),
								//others
								'paymentmethod'		=> '',
								'currency' 				=> $currency,
								'currency_symbol'	=> $currency_symbol,
                                'currency_rate' 	=> $currency_rate,
                                'd_measure'         => $d_measure,
                                'additional_imgs' => json_encode($img_array),
                                'updated_at' => time()
							));
							$d->save(); //save
							$branch_id = _post('company_branch_id') ? _post('company_branch_id') : 2;
                            $x = ORM::for_table('sys_invoiceitems')->where('invoiceid', $iid)->delete_many();
                            $y = ORM::for_table('sys_items_stock')->where('invoice_id', $iid)->delete_many(); //newly added
							for($i=0;$i<count($_POST['desc']);$i++ ){
									$prod = ORM::for_table('sys_items')->find_one($_POST['desc'][$i]);
									$d = ORM::for_table('sys_invoiceitems')->create();
									$d->invoiceid = $iid;
									$d->userid = $cid;
									$d->itemcode = $_POST['s_id'][$i];
									$d->description = $_POST['desc'][$i];
									$d->qty = Finance::amount_fix($_POST['qty'][$i]);
									$d->amount = Finance::amount_fix($_POST['amount'][$i]);
									//get tax rate by id
									$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
									$d->taxrate = Finance::amount_fix($ptaxrate['rate']);
									$d->tax_id = $_POST['taxed'];
									$taxamt = $_POST['total'][$i]*$ptaxrate['rate']/100;
									$d->taxamount = Finance::amount_fix($taxamt);
									$d->total = Finance::amount_fix($taxamt + $_POST['total'][$i]);
									//others
									$d->type = '';
									$d->relid = '0';
									$d->duedate = date('Y-m-d');
									$d->paymentmethod = '';
                                    $d->notes = '';

                                    
                                    $d->item_type = $_POST['item_type'][$i];   
                                
                                    $d->product_id = ($d->item_type == 'product') ? $_POST['p_id'][$i] : null;
                                    $d->design_id = ($d->item_type == 'design') ? $_POST['p_id'][$i] : null;
                                    
                                    stock_record($d->product_id, $d->qty, 'debit', $d->invoiceid , '', '', '', $branch_id, ''); //newly added

                                    if(!empty($_POST['pimg'][$i]))
                                    {
                                        $get_img = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$_POST['pimg'][$i]);
                                        $set_name = time().$i.'.jpg';
                                        $inv_img_path = $_SERVER['DOCUMENT_ROOT'].'/ui/lib/imgs/invoice-contents/'.$set_name;
                                        file_put_contents($inv_img_path, $get_img);
                                        
                                        $d->item_img = $set_name; //$inv_img_path;
                                    }


									$d->save(); //save
									_msglog('s', 'Invoice Edited Successfully'); 
                            }

                        $other_l   = array();
                        $other_l[] = ($_POST['is_pocket'] == 'yes') ? 'pocket' : null;
                        $other_l[] = ($_POST['is_zip'] == 'yes') ? 'zip' : null;
                        $other_l[] = ($_POST['is_beading'] == 'yes') ? 'beading' : null;
                        $other_l[] = ($_POST['is_folding'] == 'yes') ? 'folding' : null;
                        $other_l[] = ($_POST['is_pico'] == 'yes') ? 'pico' : null;
                        $other_l   = array_filter($other_l);
                        
                        foreach($other_l as $other)
                        {
                            $d = ORM::for_table('sys_invoiceitems')->create();
                            $d->invoiceid = $iid;
                            $d->userid = $cid;
                            $d->itemcode = 0;
                            $d->description = $other;
                            $d->qty = 1;
                            $d->amount = 0;
                            $ptaxrate = 0;
                            $d->taxrate = 0;
                            $d->tax_id = 0;
                            $taxamt = 0;
                            $d->taxamount = 0;
                            $d->total = 0;
                            $d->relid = '0';
                            $d->duedate = date('Y-m-d');
                            
                            $d->item_type = $other;    
                            $d->product_id = null;
                            $d->design_id = null;                                                              
                            $d->save(); //save
                        }                            

                            /*if($_POST['is_pocket'] == 'yes')
                            {
                                    //$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
                                    
                                    $d = ORM::for_table('sys_invoiceitems')->create();
                                    $d->invoiceid = $iid;
                                    $d->userid = $cid;
                                    $d->itemcode = 0;
                                    $d->description = 'Pocket';
                                    $d->qty = 1;
                                    $d->amount = 0;
                                    $ptaxrate = 0;
                                    $d->taxrate = 0;
                                    $d->tax_id = 0;
                                    $taxamt = 0;
                                    $d->taxamount = 0;
                                    $d->total = 0;
                                    $d->relid = '0';
                                    $d->duedate = date('Y-m-d');
                                    
                                    $d->item_type = 'pocket';    
                                    $d->product_id = null;
                                    $d->design_id = null;                                                              
    
                                    $d->save(); //save
                            }
    
                            if($_POST['is_zip'] == 'yes')
                            {
                                    //$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
                                    
                                    $d = ORM::for_table('sys_invoiceitems')->create();
                                    $d->invoiceid = $iid;
                                    $d->userid = $cid;
                                    $d->itemcode = 0;
                                    $d->description = 'Zip';
                                    $d->qty = 1;
                                    $d->amount = 0;
                                    $ptaxrate = 0;
                                    $d->taxrate = 0;
                                    $d->tax_id = 0;
                                    $taxamt = 0;
                                    $d->taxamount = 0;
                                    $d->total = 0;
                                    $d->relid = '0';
                                    $d->duedate = date('Y-m-d');
                                    
                                    $d->item_type = 'zip';  
                                    $d->product_id = null;
                                    $d->design_id = null;                                  
                                    
    
                                    $d->save(); //save
                            }*/  
                            
                            if($_POST['is_umbrella'] == 'yes')
                            {
                                    //$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
                                    
                                    $d = ORM::for_table('sys_invoiceitems')->create();
                                    $d->invoiceid = $iid;
                                    $d->userid = $cid;
                                    $d->itemcode = 0;
                                    $d->description = 'Umbrella (Size : '.$_POST['umbrella_size'].')';
                                    $d->qty = 1;
                                    $d->amount = 0;
                                    $ptaxrate = 0;
                                    $d->taxrate = 0;
                                    $d->tax_id = 0;
                                    $taxamt = 0;
                                    $d->taxamount = 0;
                                    $d->total = 0;
                                    $d->relid = '0';
                                    $d->duedate = date('Y-m-d');
                                    
                                    $d->item_type = 'umbrella';  
                                    $d->product_id = null;
                                    $d->design_id = null;                                                                 
    
                                    $d->save(); //save
                            }

                            if($_POST['is_dupatta'] == 'yes')
                            {
                                    //$prod = ORM::for_table('sys_items')->where('item_number',$_POST['s_id'][$i] )->find_one();
                                    
                                    $d = ORM::for_table('sys_invoiceitems')->create();
                                    $d->invoiceid = $iid;
                                    $d->userid = $cid;
                                    $d->itemcode = 0;
                                    $d->description = 'Dupatta (Size : '.$_POST['dupatta_size'].')';
                                    $d->qty = 1;
                                    $d->amount = 0;
                                    $ptaxrate = 0;
                                    $d->taxrate = 0;
                                    $d->tax_id = 0;
                                    $taxamt = 0;
                                    $d->taxamount = 0;
                                    $d->total = 0;
                                    $d->relid = '0';
                                    $d->duedate = date('Y-m-d');
                                    
                                    $d->item_type = 'dupatta';  
                                    $d->product_id = null;
                                    $d->design_id = null;   
                                    
                                    $d->save(); //save
                            }                            



						}else{
							_msglog('e', 'Invoice Not Saved.');
						}
            Event::trigger('add_invoice_posted');
            echo $iid;
				}else {
            echo $msg;
        }
        break;

    case 'edit-proforma-post':

        Event::trigger('invoices/edit-proforma-post/');
				$cid = _post('cid');
				$iid = _post('iid');
        $idate = _post('idate');
        $its = strtotime($idate);
        $duedate = _post('duedate');
				$company = _post('company');
        //find user with cid
        $u = ORM::for_table('crm_accounts')->find_one($cid);

        $msg = '';
        if ($cid == '') {
            $msg .= $_L['select_a_contact'].' <br> ';
        }
				if ($duedate == '') {
            $msg .= 'Select a Due Date '.' <br> ';
        }
				
        $notes = _post('notes');

        // find currency
        $currency_id = _post('currency');
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

        $repeat = _post('repeat');
        $nd = $idate;
        if ($repeat == '0') {
            $r = '0';
        } elseif ($repeat == 'week1') {
            $r = '+1 week';
            $nd = date('Y-m-d', strtotime('+1 week', $its));
        } elseif ($repeat == 'weeks2') {
            $r = '+2 weeks';
            $nd = date('Y-m-d', strtotime('+2 weeks', $its));
        } elseif ($repeat == 'month1') {
            $r = '+1 month';
            $nd = date('Y-m-d', strtotime('+1 month', $its));
        } elseif ($repeat == 'months2') {
            $r = '+2 months';
            $nd = date('Y-m-d', strtotime('+2 months', $its));
        } elseif ($repeat == 'months3') {
            $r = '+3 months';
            $nd = date('Y-m-d', strtotime('+3 months', $its));
        } elseif ($repeat == 'months6') {
            $r = '+6 months';
            $nd = date('Y-m-d', strtotime('+6 months', $its));
        } elseif ($repeat == 'year1') {
            $r = '+1 year';
            $nd = date('Y-m-d', strtotime('+1 year', $its));
        } elseif ($repeat == 'years2') {
            $r = '+2 years';
            $nd = date('Y-m-d', strtotime('+2 years', $its));
        } elseif ($repeat == 'years3') {
            $r = '+3 years';
            $nd = date('Y-m-d', strtotime('+3 years', $its));
        } else {
            $msg .= 'Date Parsing Error <br> ';
        }
$inv_prefix = '';
        if ($msg == '') {

						$cn = _post('cn');
						$sTotal = 0; $taxTotal = 0; $prod_total = 0;
						for($i=0;$i<count($_POST['desc']);$i++ ){
								$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
								$taxrate = $ptaxrate['rate'];//$_POST['taxed'][$i];
								$taxamt = $_POST['total'][$i]*$taxrate/100;
								$taxTotal = $taxTotal + $taxamt;
								$sTotal += $taxamt + $_POST['total'][$i];
								$prod_total = $prod_total + $_POST['total'][$i];
						}
						//exit;
					 $fTotal = $sTotal;
						// calculate discount
            $discount_amount = _post('discount_amount');
            $discount_type = _post('discount_type');
            $discount_value = '0.00';
 
            if($discount_amount == '0' OR $discount_amount == ''){
                $actual_discount = '0.00';
            }
            else{
                if($discount_type == 'f'){
                    $actual_discount = $discount_amount;
                    $discount_value = $discount_amount;
                }
                else{
                    $discount_type = 'p';
                    $actual_discount = ($sTotal * $discount_amount) / 100;
                    $discount_value = $discount_amount;
                }
            }
						//var_dump($discount_value);exit;
            $actual_discount = number_format((float)$actual_discount, 2, '.', '');

            $fTotal = $fTotal - $actual_discount;

           /*  $actual_taxed_amount = $taxed_amount - $actual_discount;
				
            if($actual_taxed_amount > 0){
                $taxval = ($actual_taxed_amount * $taxrate) / 100;
            }
 
            if (($taxed_type != 'individual') AND ($tax != '')) {
                $taxval = ($fTotal * $taxrate) / 100;
            }
 */
            $inv_no = ORM::for_table('sys_performa')->where('company_id', $company)->max('invoice_no');
		
						if($inv_no == null){
							$inv_no = 1;
						}else{
							$inv_no = $inv_no;
											
						}
						
						if($company == 1){
							$inv_prefix = 'PME';
						}elseif($company == 2){
							$inv_prefix = 'PMI';
						}else{
							
						}
						$invoicenum = $inv_prefix.$inv_no;
            $datetime = date("Y-m-d H:i:s");

            $vtoken = _raid(10);
            $ptoken = _raid(10);
						
						//update to sys_invoices
            $d = ORM::for_table('sys_performa')->find_one($iid);
						$status = "";
						if($d['credit']!=0.00){
							$status = "Partially Paid";
						}else{
							$status = "Unpaid";
						}
						if ($d) {
							$d->set(array(
								'userid'		 			=> $cid,
								'account'  				=> $u['account'],
								 'company_id'  		=> $company,
								'cn'							=> $cn,
								//'invoicenum'			=> $invoicenum,
								'date' 						=> $idate,
								'duedate' 				=> $duedate,
								'datepaid' 				=> $datetime,
								'total' 					=> Finance::amount_fix($prod_total),
								'subtotal' 				=> Finance::amount_fix($sTotal-$actual_discount),
								'discount_type'   => $discount_type,
								'discount_value'  => $discount_value,
								'discount'        => $actual_discount,
								'vtoken' 					=> $vtoken,
								'ptoken' 					=> $ptoken,
								'status' 					=> $status,
								'notes' 					=> $notes,
								'r' 							=> $r,
								'nd' 							=> $nd,
								'taxamt' 					=> Finance::amount_fix($taxTotal),
								//others
								'paymentmethod'		=> '',
								'currency' 				=> $currency,
								'currency_symbol'	=> $currency_symbol,
								'currency_rate' 	=> $currency_rate
							));
							$d->save(); //save
							
							$x = ORM::for_table('sys_performaitems')->where('invoiceid', $iid)->delete_many();
							for($i=0;$i<count($_POST['desc']);$i++ ){
									$prod = ORM::for_table('sys_items')->find_one($_POST['desc'][$i]);
									$d = ORM::for_table('sys_performaitems')->create();
									$d->invoiceid = $iid;
									$d->userid = $cid;
									$d->itemcode = $_POST['s_id'][$i];
									$d->description = $_POST['desc'][$i];
									$d->qty = Finance::amount_fix($_POST['qty'][$i]);
									$d->amount = Finance::amount_fix($_POST['amount'][$i]);
									//get tax rate by id
									$ptaxrate = ORM::for_table('sys_tax')->find_one($_POST['taxed']);
									$d->taxrate = Finance::amount_fix($ptaxrate['rate']);
									$d->tax_id = $_POST['taxed'];
									$taxamt = $_POST['total'][$i]*$ptaxrate['rate']/100;
									$d->taxamount = Finance::amount_fix($taxamt);
									$d->total = Finance::amount_fix($taxamt + $_POST['total'][$i]);
									//others
									$d->type = '';
									$d->relid = '0';
									$d->duedate = date('Y-m-d');
									$d->paymentmethod = '';
									$d->notes = '';
									$d->save(); //save
									_msglog('s', 'Proforma Edited Successfully'); 
							}
						}else{
							_msglog('e', 'Proforma Not Saved.');
						}
            Event::trigger('add_invoice_posted');
            echo $iid;
				}else {
            echo $msg;
        }
        break;

    case 'delete':

        Event::trigger('invoices/delete/');

        $id = $routes['2'];
        if ($_app_stage == 'Demo') {
            r2(U . 'accounts/list', 'e', 'Sorry! Deleting Account is disabled in the demo mode.');
        }
        $d = ORM::for_table('crm_accounts')->find_one($id);
        if ($d) {
            $d->delete();
            r2(U . 'accounts/list', 's', $_L['account_delete_successful']);
        }

        break;

        case 'ajax-delete':
            Event::trigger('invoices/ajax-delete/');
            // Get ID from route
            $id = $routes['2'];
            // Demo mode protection
            if ($_app_stage == 'Demo') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Deleting is disabled in demo mode.'
                ]);
                exit;
            }
            // Find the invoice
            $invoice = ORM::for_table('sys_invoices')->find_one($id);
            if ($invoice) {
                try {
                    $invoice->delete();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Invoice deleted successfully.'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error deleting invoice: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invoice not found.'
                ]);
            }
        break;


    case 'print':

        Event::trigger('invoices/print/');

        $id = $routes['2'];
        $d = ORM::for_table('sys_invoices')->find_one($id);
        if ($d) {

            //find all activity for this user
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid', $id)->order_by_asc('id')->find_many();

//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);

            require 'application/lib/invoices/render.php';

        } else {
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;

    case 'pdf':

        Event::trigger('invoices/pdf/');


        $id = $routes['2'];


        $d = ORM::for_table('sys_invoices')->find_one($id);
        if ($d) {

            //find all activity for this user
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid', $id)->order_by_asc('id')->find_many();

            $trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();

            $trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();

//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['total'];
            if($d['credit'] != '0.00'){
                $i_due = $i_total - $i_credit;
            }
            else{
                $i_due =  $d['total'];
            }

            $i_due = number_format($i_due,2,$config['dec_point'],$config['thousands_sep']);
            $cf = ORM::for_table('crm_customfields')->where('showinvoice', 'Yes')->order_by_asc('id')->find_many();
//            ob_start();
//            require 'application/lib/invoices/pdf-default.php';
//            $html = ob_get_contents();
//            ob_end_clean();
//            echo $html;
//            exit;
//            require('application/lib/tcpdf/config/lang/eng.php');
//            require('application/lib/tcpdf/tcpdf.php');
//            // create new PDF document
//            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//
//// set document information
//            $pdf->SetCreator('application');
//            $pdf->SetAuthor('application.com');
//            $pdf->SetTitle('invoice titla');
//            $pdf->SetSubject('invoice subject');
//
//            $pdf->SetPrintHeader(false);
//// set default header data
//            //   $pdf->SetHeaderData('', '', $title, "Generated on ".date('d/m/Y')." \nby ".$aadmin);
//
//// set header and footer fonts
//            //   $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//            //   $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
////$pdf->SetFont('freesans', '', 10);
//// set default monospaced font
//            //   $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//
////set margins
////            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
////        //    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
////         //   $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
////
//////set auto page breaks
////            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
////
//////set image scale factor
////            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//
////set some language-dependent strings
//            //  $pdf->setLanguageArray();
//
//// ---------------------------------------------------------
//
//// set font
//            $pdf->AddPage();
//            require 'application/lib/invoices/pdf-x1.php';
//
//            // $pdf->writeHTML($html, true, false, true, false, '');
//
//// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//
//// reset pointer to the last page
//            //   $pdf->lastPage();
//
//// ---------------------------------------------------------
//
////Close and output PDF document
//            if (isset($routes['3']) AND ($routes['3'] == 'dl')) {
//                $pdf->Output(date('Y-m-d') . _raid(4) . '.pdf', 'D'); # D
//            } else {
//                $pdf->Output(date('Y-m-d') . _raid(4) . '.pdf', 'I'); # D
//            }
//
//        } else {
//            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
//        }


            if($d['cn'] != ''){
                $dispid = $d['cn'];
            }else{
                $dispid = $d['id'];
            }

            $in = $d['invoicenum'].$dispid;
            define('_MPDF_PATH','application/lib/mpdf/');

            require('application/lib/mpdf/mpdf.php');
            $pdf_c = '';
            $ib_w_font = 'dejavusanscondensed';
            if($config['pdf_font'] == 'default'){
                $pdf_c = 'c';
                $ib_w_font = 'Helvetica';
            }

            $mpdf=new mPDF($pdf_c,'A4','','',20,15,15,25,10,10);
            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($config['CompanyName'].' Invoice');
            $mpdf->SetAuthor($config['CompanyName']);
            $mpdf->SetWatermarkText(ib_lan_get_line($d['status']));
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = $ib_w_font;
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            if($config['pdf_font'] == 'AdobeCJK'){
                $mpdf->useAdobeCJK = true;
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;
            }

            Event::trigger('invoices/before_pdf_render/');

            ob_start();
            require 'application/lib/invoices/pdf-x2.php';
            $html = ob_get_contents();
            ob_end_clean();

            $mpdf->WriteHTML($html);
            $pdf_return = 'inline';

            if (isset($routes[3])) {
                $r_type = $routes[3];
            }else{
                $r_type = 'inline';
            }

            if ($r_type == 'dl') {
                $mpdf->Output(date('Y-m-d') . _raid(4) . '.pdf', 'D'); # D
            }elseif ($r_type == 'inline') {
                $mpdf->Output(date('Y-m-d') . _raid(4) . '.pdf', 'I'); # D
            }elseif ($r_type == 'store') {
                $mpdf->Output('application/storage/temp/Invoice_'.$in.'.pdf', 'F'); # D
            }else {
                $mpdf->Output(date('Y-m-d') . _raid(4) . '.pdf', 'I'); # D
            }
        }
        break;

    case 'markpaid':

        $iid = _post('iid');
        $d = ORM::for_table('sys_invoices')->find_one($iid);
        //$pro_d = ORM::for_table('sys_performa')->where('inv_id',$iid)->find_one();
        if ($d) {
					$d->status = 'Paid';
					$d->save();
					
					//$pro_d->status = 'Paid';
					//$pro_d->save();
					
					Event::trigger('invoices/markpaid/',$invoice=$d);
					_msglog('s', 'Invoice marked as Paid');
					$crm_account = ORM::for_table('crm_accounts')->where('id', $d['userid'])->find_one();
                    $phone = ($crm_account) ? $crm_account->phone : ''; // Assuming 'phone' is the column name
					$_url = U;
                    $ordertrack_url = $_url . "client/iview/{$d['id']}/token_{$d['vtoken']}";
                    $name = ucwords($d->account);
                    $invoicenumber = $d->invoicenum;
                    $paymentstatus = ucwords($d->status); // Use the status directly from $d
                    $orderstatus = ucwords($d->delivery_status);
					
					wati_notifiation($name, $invoicenumber, $paymentstatus, $orderstatus, $ordertrack_url, $phone);
        } else {
					_msglog('e', 'Invoice not found');
        }
        
    break;

        case 'd_status':

        $iid = $_POST['invoice_id'];
        $d_status = $_POST['d_delivery_status'];
        $d_date = $_POST['d_date'];
        $d_notes = $_POST['d_notes'];
        $phone = $_POST['phone'];
        //var_dump($_GET);exit;
        $d = ORM::for_table('sys_invoices')->find_one($iid);
        if ($d) 
        {
            $d->delivery_status = $d_status;
            
            // Check if the status is completed
            if ($d_status === 'completed') {
                // Calculate the reminder date (current date + 2 days)
                $reminder_date = date('Y-m-d', strtotime('+2 days'));
                $d->reminder_date = $reminder_date;
            }else{
                $d->reminder_date = null;
            }
            
            $d->save();

            $s = ORM::for_table('sys_invoices_status')->create();
            $s->invoice_id = $iid;
            $s->delivery_status = $d_status;
            $s->timestamp = $d_date.date(' H:i A');
            $s->notes = $d_notes;
            $s->save();            

            /*$sms = rawurlencode('For new registration mobile number verification your OTP is '.$d_status.', Don’t share with anyone, Kaak Economic');
            SMS($phone, $sms);*/

            //_msglog('s', 'Invoice marked as '.$d_status.'');
            //r2(U . 'invoices/view/'.$iid, 's', 'Delivery status marked as '.$d_status.'');
            
            $_url = U;
            $ordertrack_url = $_url . "client/iview/{$d['id']}/token_{$d['vtoken']}";
            $name = ucwords($d->account);
            $invoicenumber = $d->invoicenum;
            $paymentstatus = ucwords($d->status); // Use the status directly from $d
            $orderstatus = ucwords($d->delivery_status);
			
			wati_notifiation($name, $invoicenumber, $paymentstatus, $orderstatus, $ordertrack_url, $phone);
            
            echo $s->id();
        } 
        else 
        {
			_msglog('e', 'Invoice not found');
        }
        break;       

    case 'markunpaid':

        Event::trigger('invoices/markunpaid/');

        $iid = _post('iid');
        $d = ORM::for_table('sys_invoices')->find_one($iid);
				//$pro_d = ORM::for_table('sys_performa')->where('inv_id',$iid)->find_one();
        if ($d) {
            $d->status = 'Unpaid';
            $d->save();
            //$pro_d->status = 'Unpaid';
            //$pro_d->save();
            _msglog('s', 'Invoice marked as Un Paid');
            
            $crm_account = ORM::for_table('crm_accounts')->where('id', $d['userid'])->find_one();
            $phone = ($crm_account) ? $crm_account->phone : ''; // Assuming 'phone' is the column name
            $_url = U;
            $ordertrack_url = $_url . "client/iview/{$d['id']}/token_{$d['vtoken']}";
            $name = ucwords($d->account);
            $invoicenumber = $d->invoicenum;
            $paymentstatus = ucwords($d->status); // Use the status directly from $d
            $orderstatus = ucwords($d->delivery_status);
			
			wati_notifiation($name, $invoicenumber, $paymentstatus, $orderstatus, $ordertrack_url, $phone);
        } else {
            _msglog('e', 'Invoice not found');
        }
        break;

    case 'markcancelled':

        Event::trigger('invoices/markcancelled/');


        $iid = _post('iid');
        $d = ORM::for_table('sys_invoices')->find_one($iid);
				//$pro_d = ORM::for_table('sys_performa')->where('inv_id',$iid)->find_one();
        if ($d) {
            $d->status = 'Cancelled';
            $d->save();
            //$pro_d->status = 'Cancelled';
            //$pro_d->save();
            _msglog('s', 'Invoice marked as Cancelled');
            $crm_account = ORM::for_table('crm_accounts')->where('id', $d['userid'])->find_one();
            $phone = ($crm_account) ? $crm_account->phone : ''; // Assuming 'phone' is the column name
            $_url = U;
            $ordertrack_url = $_url . "client/iview/{$d['id']}/token_{$d['vtoken']}";
            $name = ucwords($d->account);
            $invoicenumber = $d->invoicenum;
            $paymentstatus = ucwords($d->status); // Use the status directly from $d
            $orderstatus = ucwords($d->delivery_status);
			
			wati_notifiation($name, $invoicenumber, $paymentstatus, $orderstatus, $ordertrack_url, $phone);
        } else {
            _msglog('e', 'Invoice not found');
        }
        break;

    case 'markpartiallypaid':

        Event::trigger('invoices/markpartiallypaid/');


        $iid = _post('iid');
        $d = ORM::for_table('sys_invoices')->find_one($iid);
				//$pro_d = ORM::for_table('sys_performa')->where('inv_id',$iid)->find_one();
        if ($d) {
            $d->status = 'Partially Paid';
            $d->save();
            //$pro_d->status = 'Partially Paid';
            //$pro_d->save();
            _msglog('s', 'Invoice marked as Partially Paid');
            
            $crm_account = ORM::for_table('crm_accounts')->where('id', $d['userid'])->find_one();
            $phone = ($crm_account) ? $crm_account->phone : ''; // Assuming 'phone' is the column name
            $_url = U;
            $ordertrack_url = $_url . "client/iview/{$d['id']}/token_{$d['vtoken']}";
            $name = ucwords($d->account);
            $invoicenumber = $d->invoicenum;
            $paymentstatus = ucwords($d->status); // Use the status directly from $d
            $orderstatus = ucwords($d->delivery_status);
			
			wati_notifiation($name, $invoicenumber, $paymentstatus, $orderstatus, $ordertrack_url, $phone);
        } else {
            _msglog('e', 'Invoice not found');
        }
        break;


    case 'add-payment':

        Event::trigger('invoices/add-payment/');

        $sid = $routes['2'];
        $d = ORM::for_table('sys_invoices')->find_one($sid);

        if ($d) {
            $itotal = $d['subtotal'];
            $ic = $d['credit'];
						$discount = $d['discount'];
            $np = $itotal - $ic;
            $a_opt = '';
            // <option value="{$ds['account']}">{$ds['account']}</option>
            $a = ORM::for_table('sys_accounts')->find_many();
            // foreach ($a as $acs) {
            //     $a_opt .= '<option value="' . $acs['account'] . '" selected>' . $acs['account'] . '</option>';
            // }

            // check user role
            if ($user['roleid'] == 0) {
                // Super admin - show all
                foreach ($a as $acs) {
                    $sel = ($acs['id'] == $user->branch_id) ? 'selected' : '';
                    $a_opt .= '<option value="' . $acs['account'] . '" data-branch="' . $acs['id'] . '" ' . $sel . '>' . $acs['account'] . '</option>';
                }
            } else {
                // Normal user - show only their branch
                foreach ($a as $acs) {
                    if ($acs['id'] == $user->branch_id) {
                        $a_opt .= '<option value="' . $acs['account'] . '" data-branch="' . $acs['id'] . '" selected>' . $acs['account'] . '</option>';
                    }
                }
            }
            
            $pms_opt = '';
            // <option value="{$pm['name']}">{$pm['name']}</option>
            $pms = ORM::for_table('sys_pmethods')->order_by_asc('sorder')->find_many();
            foreach ($pms as $pm) {
                $pms_opt .= '<option value="' . $pm['name'] . '">' . $pm['name'] . '</option>';
            }

            $cats_opt = '';

            $cats = ORM::for_table('sys_cats')->where('type', 'Income')->order_by_asc('sorder')->find_many();

            foreach ($cats as $cat) {
                $cats_opt .= '<option value="' . $cat['name'] . '">' . $cat['name'] . '</option>';
            }

// <option value="">'.$_L['Choose an Account'].'</option>
            echo '
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>'.$_L['Invoice'].' #' . $d['id'] . '</h3>
</div>
<div class="modal-body">

<form class="form-horizontal" role="form" id="form_add_payment" method="post">
<div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Account'].'</label>
    <div class="col-sm-10">
       <select id="account" name="account" class="form-control">' . $a_opt . '</select>
       <input type="hidden" id="branch_id" name="branch_id" value="">
    </div>
</div>

<div class="form-group">
    <label for="date" class="col-sm-2 control-label">'.$_L['Date'].'</label>
    <div class="col-sm-10">
      <input type="text" class="form-control datepicker"  value="' . date('Y-m-d') . '" name="date" id="date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
    </div>
  </div>

<div class="form-group">
    <label for="description" class="col-sm-2 control-label">'.$_L['Description'].'</label>
    <div class="col-sm-10">
      <input type="text" id="description" name="description" class="form-control" value="'.$_L['Invoice'].' ' . $d['invoicenum'] . ' '.$_L['Payment'].'">
    </div>
  </div>
<div class="form-group">
    <label for="amount" class="col-sm-2 control-label">'.$_L['Amount'].'</label>
    <div class="col-sm-10">
      <input type="text" id="amount" name="amount" class="form-control amount"   data-a-sign="' . $config['currency_code'] . ' " data-a-dec="' . $config['dec_point'] . '" data-a-sep="' . $config['thousands_sep'] . '"
data-d-group="2" value="' . $np . '">
    </div>
  </div>
<div class="form-group">
    <label for="cats" class="col-sm-2 control-label">'.$_L['Category'].'</label>
    <div class="col-sm-10">
       <select id="cats" name="cats">
                             <option value="Uncategorized">'.$_L['Uncategorized'].'</option>

' . $cats_opt . '

                        </select>
    </div>
  </div>
  <div class="form-group">
    <label for="payer_name" class="col-sm-2 control-label">'.$_L['Payer'].'</label>
    <div class="col-sm-10">
      <input type="text" id="payer_name" name="payer_name" class="form-control" value="' . $d['account'] . '" disabled>
    </div>
  </div>

   <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Method'].'</label>
    <div class="col-sm-10">
      <select id="pmethod" name="pmethod" onchange="showDiv(this.value)">
                                <option value="">'.$_L['Select Payment Method'].'</option>


                                ' . $pms_opt . '


                            </select>
    </div>
  </div>
 <div class="form-group" id="sabir" style="display:none;">
 <label for="subject" class="col-sm-2 control-label">Reference No:</label>
     <div class="col-sm-10">
      <input type="text" id="reference_no" name="reference_no" class="form-control" value="">
    </div>
  </div>

</form>

</div>
<div class="modal-footer">
<input type="hidden" id="payer" name="payer" value="' . $d['userid'] . '">
	<button id="save_payment" class="btn btn-primary">'.$_L['Save'].'</button>

		<button type="button" data-dismiss="modal" class="btn">'.$_L['Close'].'</button>
</div>
<script>
function showDiv(elem){
	console.log(elem);
		if(elem == "Cheque" || elem == "Electronic Transfer"){   
				document.getElementById("sabir").style.display = "block";
		}
		else{
			document.getElementById("sabir").style.display = "none";
		}
}

</script>';
        } else {
            exit('Invoice Not Found');
        }
        break;


        case 'add-delivey-status':

        Event::trigger('invoices/add-payment/');

        $sid = $routes['2'];
        $d = ORM::for_table('sys_invoices')->find_one($sid);


            echo '
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Invoice Status</h3>
</div>
<div class="modal-body">

<form class="form-horizontal" role="form" id="form_update_delivery" method="post">
<div class="form-group">
    <label for="subject" class="col-sm-2 control-label">Status</label>
    <div class="col-sm-10">
       <select id="d_delivery_status" name="d_delivery_status" class="form-control">
            <option value="pending">Pending</option> 
            <option value="processing">Processing</option> 
            <option value="completed">Completed</option> 
            <option value="delivered">Delivered</option>
        </select>
    </div>
  </div>

<div class="form-group">
    <label for="date" class="col-sm-2 control-label">'.$_L['Date'].'</label>
    <div class="col-sm-10">
      <input type="text" class="form-control datepicker"  value="' . date('Y-m-d') . '" name="d_date" id="d_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
    </div>
  </div>

  <div class="form-group">
  <label for="date" class="col-sm-2 control-label">Notes</label>
  <div class="col-sm-10">
    <textarea id="d_notes" name="d_notes" class="form-control"></textarea>
  </div>
</div>  

</form>

</div>
<div class="modal-footer">
	<button id="update_delivery_status" class="btn btn-primary">'.$_L['Save'].'</button>

		<button type="button" data-dismiss="modal" class="btn">'.$_L['Close'].'</button>
</div>
<script>
function showDiv(elem){
	console.log(elem);
		if(elem == "Cheque" || elem == "Electronic Transfer"){   
				document.getElementById("sabir").style.display = "block";
		}
		else{
			document.getElementById("sabir").style.display = "none";
		}
}

</script>';
        break;        
        
        
   case 'add-delivey-status2':

    Event::trigger('invoices/add-delivey-status2/');
    $sid = $routes['2'];
    $invoiceId = $sid;

    // 1) Invoice
    $invoice = ORM::for_table('sys_invoices')
        ->select('id')
        ->select('delivery_status')
        ->where('id', $invoiceId)
        ->find_one();

    $deliveryStatus = $invoice ? $invoice->delivery_status : null;

    // 2) Allocation with employee name
    $invoice_alocation = ORM::for_table('invoice_alocation')
        ->select('invoice_alocation.*')
        ->select('crm_accounts.account', 'employee_name')
        ->join('crm_accounts', array('invoice_alocation.employee_id', '=', 'crm_accounts.id'))
        ->where('invoice_id', $invoiceId)
        ->find_array();

    // 3) Get design_id from invoice items
    $designItem = ORM::for_table('sys_invoiceitems')
        ->select('design_id')
        ->select('description')
        ->where('invoiceid', $invoiceId)
        ->where('item_type', 'design')
        ->order_by_asc('id')
        ->find_one();

    $designId = $designItem ? $designItem->design_id : null;

    // Fallback: older rows may have design_id as 0/null; try to recover from description "(cloth / design) - ..."
    if (empty($designId) && $designItem && !empty($designItem->description)) {
        if (preg_match('/\\(.*?\\s\\/\\s(.*?)\\)\\s-/', $designItem->description, $m)) {
            $designName = trim($m[1]);
            $designByName = ORM::for_table('sys_designs')->where('name', $designName)->find_one();
            if ($designByName) {
                $designId = $designByName->id;
            }
        }
    }

    // 👇 IMPORTANT: always init
    $categoryPricing = [];

    if ($designId) {
        $sysDesigns = ORM::for_table('sys_designs')
            ->select('category_pricing')
            ->where('id', $designId)
            ->find_one();

        $categoryPricing = !empty($sysDesigns->category_pricing)
            ? json_decode($sysDesigns->category_pricing, true)
            : [];
    }

    // 4) Base categories
    $categoryEmployees = ORM::for_table('category_employee')->find_many();

    $categoryData = [];
    foreach ($categoryEmployees as $category) {
        // base price
        $price = $category->price ?? 0;

        // override from design JSON if present
        if (!empty($categoryPricing)) {
            foreach ($categoryPricing as $pricing) {
                if ((string)$pricing['category_id'] == (string)$category->id) {
                    $price = $pricing['price'];
                    break;
                }
            }
        }

        $categoryData[] = [
            'id'    => $category->id,
            'name'  => $category->name,
            'price' => $price,
        ];
    }

    $ui->assign('deliveryStatus', $deliveryStatus);
    $ui->assign('invoiceId', $invoiceId);
    $ui->assign('categoryData', $categoryData);
    $ui->assign('invoice_alocation', $invoice_alocation);
    $ui->display('select-employee-form.tpl');
    break;

    
    case 'add-delivey-status22333':
    Event::trigger('invoices/add-delivey-status2/');
    $sid = $routes['2'];
    $invoiceId = $sid;
          
    // Check if the invoice exists
    $invoice_alocation = ORM::for_table('invoice_alocation')->where('invoice_id', $invoiceId)->find_many();
    // var_dump($invoiceExists);

    // Fetch category data
    $categories = ORM::for_table('category_employee')->find_many();
    $categoryData = [];
    foreach ($categories as $category) {
        $categoryData[] = [
            'id' => $category->id,
            'name' => $category->name,
            'price' => $category->price,
        ];
    }

    // Fetch employee names from crm_accounts and merge with invoice_alocation
    $invoice_alocation = ORM::for_table('invoice_alocation')
        ->select('invoice_alocation.*')
        ->select('crm_accounts.account', 'employee_name')
        ->join('crm_accounts', array('invoice_alocation.employee_id', '=', 'crm_accounts.id'))
        ->find_array();

    // Now $mergedData contains the merged data with employee names included
    // Assign variables to the template
    $ui->assign('$data', $data);
    $ui->assign('invoiceId', $invoiceId);
    $ui->assign('categoryData', $categoryData);
    
    if ($invoice_alocation) {    
        $ui->assign('invoice_alocation', $invoice_alocation);
    } else {
        // If there are no records, assign an empty array to $invoice_alocation
        $ui->assign('invoice_alocation', []);
    }
    
    $ui->display('select-employee-form.tpl');
    break;

    
    case 'add-delivey-status123':
    Event::trigger('invoices/add-delivey-status2/');
    $sid = $routes['2'];
    $invoiceId = $sid;
          
    // Check if the invoice exists
    $invoiceExists = ORM::for_table('invoice_alocation')->where('invoice_id', $invoiceId)->count();
    // var_dump($invoiceExists);

    // Initialize an empty array for invoice allocations
    $invoiceAllocations = [];
    
    // Fetch invoice allocations only if the invoice exists
    if ($invoiceExists) {
        $invoiceAllocations = ORM::for_table('invoice_alocation')->where('invoice_id', $invoiceId)->find_many();
        var_dump($invoiceAllocations);
    }
    
    // Fetch category data
    $categories = ORM::for_table('category_employee')->find_many();
    $categoryData = [];
    foreach ($categories as $category) {
        $categoryData[] = [
            'id' => $category->id,
            'name' => $category->name,
            'price' => $category->price,
            'employee_id' => $category->employee_id
        ];
    }

    // Assign variables to the template
    $ui->assign('invoiceId', $invoiceId);
    $ui->assign('categoryData', $categoryData);
    $ui->assign('invoiceExists', $invoiceExists);
    $ui->assign('invoiceAllocations', $invoiceAllocations);
    $ui->display('select-employee-form.tpl');
    break;

    
    case 'fetch-employee-invoice':
    Event::trigger('invoices/fetch-employee-invoice/');
    
    $branchId = $user->branch_id; // current logged-in user's branch

    // Fetch data from the crm_accounts table
    $accounts = ORM::for_table('crm_accounts')->where('employee_category_id', $_GET['categoryId'])->where('branch_id', $branchId)->find_many();
    
    // var_dump($accounts);
    
    $option = '<option value="">Select Employee</option>';
    foreach ($accounts as $account) {
        
    $option .= '<option value="'.$account->id.'"> '. $account->account .' </option> ';
        
        // $accountData[] = [
        //     'id' => $account->id,
        //     'account' => $account->account,
        //     'employee_category_id' => $account->employee_category_id
        // ];
    }

    // echo '<pre>';
    // print_r($option);
    // echo '</pre>';
    // var_dump($option);
    // Return data as JSON
    
    echo $option;
    
    break;
    
       
   case 'employee-invoice-form':
    
    Event::trigger('invoices/employee-invoice-form/');
    
    $invoice_alocation = ORM::for_table('invoice_alocation');
    $invoiceId = $_POST['invoiceId'];
    $queryupdate = $_POST['queryupdate']; // Check if it's set to 1 for update
    $rowCount = count($_POST['categoryId']);
    
    if(isset($_POST['id'])){
        $submittedIds = array_filter($_POST['id']); // Filter out empty ids
    }
    
    // Get the existing ids from the database
    $existingIds = ORM::for_table('invoice_alocation')->select('id')->where('invoice_id', $invoiceId)->find_array();
    $existingIds = array_column($existingIds, 'id');
    
    if(!empty($existingIds && $submittedIds)){
        // Identify deleted records by comparing submitted ids with existing ids
        $deletedIds = array_diff($existingIds, $submittedIds);
    }

    // Remove deleted records from the database
    if (!empty($deletedIds)) {
        $invoice_alocation->where_in('id', $deletedIds)->delete_many();
    }
    
    for ($i = 0; $i < $rowCount; $i++) {
        $categoryId = $_POST['categoryId'][$i];
        $quantity = $_POST['quantity'][$i];
        $price = $_POST['price'][$i];
        $employeeId = $_POST['employeeId'][$i];

        // Check if the 'id' index exists in the $_POST array for each iteration
        if (isset($_POST['id'][$i])) {
            $Id = $_POST['id'][$i];
            var_dump($Id);

            // Check if $Id is not empty or null before querying the database
            if ($Id !== null && $Id !== '') {
                // Check if records already exist for the current invoice, category, and employee
                $existingRecord = ORM::for_table('invoice_alocation')->where('id', $Id)->find_one();

                if ($queryupdate == '1' && $existingRecord) {
                    // Update the existing record
                    $existingRecord->set('category_id', $categoryId);
                    $existingRecord->set('qty', $quantity);
                    $existingRecord->set('price', $price);
                    $existingRecord->set('employee_id', $employeeId);
                    $existingRecord->save();
                }
            }
        } else {
            // Create a new record only if 'id' is not provided
            $newRecord = ORM::for_table('invoice_alocation')->create();
            $newRecord->invoice_id = $invoiceId;
            $newRecord->category_id = $categoryId;
            $newRecord->employee_id = $employeeId;
            $newRecord->qty = $quantity;
            $newRecord->price = $price;
            $newRecord->status = 'pending';
            $newRecord->save(); // Save data to the database
                        
            $d = ORM::for_table('sys_invoices')->find_one($invoiceId);
            $invoicenumber = $d->invoicenum;

            // Send email to the employee
            $employee = ORM::for_table('crm_accounts')->where('id', $employeeId)->find_one();
            if ($employee) {
                $to = $employee->email;
                $username = $employee->name;
                $subject = "Invoice Assigned";
                $txt = "You have been assigned an invoice <b>" . $invoicenumber . "</b>. Please check your account for details.";
                // $txt = "You have been assigned an invoice <b>" . $invoiceId . "</b>. Please check your account for details.";
                $headers = ""; // Add any necessary headers
                send_email_brevo_api($to, $username, $subject, $txt, $headers);
                // Update email status
                $newRecord->email_status = 1;
                $newRecord->save();
            }
        }
    }

    break;
    
   case 'employee-invoice-form-proceed':
    // Get invoice ID from the form
    $invoiceId = $_POST['invoiceId'];

    // Update status to 1 for all records of the invoice in the invoice_alocation table
    $invoiceAllocations = ORM::for_table('invoice_alocation')
        ->where('invoice_id', $invoiceId)
        ->find_many();
    $today = date('Y-m-d H:i:s');
    foreach ($invoiceAllocations as $allocation) {
        $allocation->status = 1;
        $allocation->completed_date = $today;
        $allocation->save();
        
        // Get allocation details
        $allocationId = $allocation->id;
        $categoryId = $allocation->category_id;
        $employeeId = $allocation->employee_id;
        $quantity = $allocation->qty;
        $price = $allocation->price;

        // Extract date from created_at
        $createdAtDate = date('Y-m-d', strtotime($allocation->created_at));
        
        // Calculate earn amount
        $earnAmount = $quantity * $price;

        $branch_id = get_branch_id('crm_accounts', $employeeId);

        // Insert data into crm_timesheet table
        $timesheetEntry = ORM::for_table('crm_timesheet')->create();
        $timesheetEntry->invoice_alocation_id = $allocationId; // Map allocation ID
        $timesheetEntry->branch_id = $branch_id;
        $timesheetEntry->employee_id = $employeeId;
        // $timesheetEntry->date = date('Y-m-d'); // Assuming today's date
        $timesheetEntry->date = $createdAtDate; // Use extracted date
        $timesheetEntry->amount = $price;
        $timesheetEntry->qty = $quantity;
        $timesheetEntry->earn_amount = $earnAmount;
        $timesheetEntry->created_at = date('Y-m-d H:i:s');
        $timesheetEntry->updated_at = date('Y-m-d H:i:s');
        $timesheetEntry->save();
    }
    break;

    case 'add-proforma-payment':

        Event::trigger('invoices/add-proforma-payment/');

        $sid = $routes['2'];
        $d = ORM::for_table('sys_performa')->find_one($sid);

        if ($d) {
            $itotal = $d['subtotal'];
            $ic = $d['credit'];
						$discount = $d['discount'];
            $np = $itotal - $ic;
            $a_opt = '';
            // <option value="{$ds['account']}">{$ds['account']}</option>
            $a = ORM::for_table('sys_accounts')->find_many();
            foreach ($a as $acs) {
                $a_opt .= '<option value="' . $acs['account'] . '" selected>' . $acs['account'] . '</option>';
            }

            $pms_opt = '';
            // <option value="{$pm['name']}">{$pm['name']}</option>
            $pms = ORM::for_table('sys_pmethods')->order_by_asc('sorder')->find_many();
            foreach ($pms as $pm) {
                $pms_opt .= '<option value="' . $pm['name'] . '">' . $pm['name'] . '</option>';
            }

            $cats_opt = '';

            $cats = ORM::for_table('sys_cats')->where('type', 'Income')->order_by_asc('sorder')->find_many();

            foreach ($cats as $cat) {
                $cats_opt .= '<option value="' . $cat['name'] . '">' . $cat['name'] . '</option>';
            }


            echo '
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Proforma #' . $d['id'] . '</h3>
</div>
<div class="modal-body">

<form class="form-horizontal" role="form" id="form_add_payment" method="post">
<div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Account'].'</label>
    <div class="col-sm-10">
       <select id="account" name="account">
                            <option value="">'.$_L['Choose an Account'].'</option>

' . $a_opt . '

                        </select>
    </div>
  </div>

<div class="form-group">
    <label for="date" class="col-sm-2 control-label">'.$_L['Date'].'</label>
    <div class="col-sm-10">
      <input type="text" class="form-control datepicker"  value="' . date('Y-m-d') . '" name="date" id="date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
    </div>
  </div>

<div class="form-group">
    <label for="description" class="col-sm-2 control-label">'.$_L['Description'].'</label>
    <div class="col-sm-10">
      <input type="text" id="description" name="description" class="form-control" value="Proforma ' . $d['invoicenum'] . ' '.$_L['Payment'].'">
    </div>
  </div>
<div class="form-group">
    <label for="amount" class="col-sm-2 control-label">'.$_L['Amount'].'</label>
    <div class="col-sm-10">
      <input type="text" id="amount" name="amount" class="form-control amount"   data-a-sign="' . $config['currency_code'] . ' " data-a-dec="' . $config['dec_point'] . '" data-a-sep="' . $config['thousands_sep'] . '"
data-d-group="2" value="' . $np . '">
    </div>
  </div>
<div class="form-group">
    <label for="cats" class="col-sm-2 control-label">'.$_L['Category'].'</label>
    <div class="col-sm-10">
       <select id="cats" name="cats">
                             <option value="Uncategorized">'.$_L['Uncategorized'].'</option>

' . $cats_opt . '

                        </select>
    </div>
  </div>
  <div class="form-group">
    <label for="payer_name" class="col-sm-2 control-label">'.$_L['Payer'].'</label>
    <div class="col-sm-10">
      <input type="text" id="payer_name" name="payer_name" class="form-control" value="' . $d['account'] . '" disabled>
    </div>
  </div>

   <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Method'].'</label>
    <div class="col-sm-10">
      <select id="pmethod" name="pmethod" onchange="showDiv(this.value)">
                                <option value="">'.$_L['Select Payment Method'].'</option>


                                ' . $pms_opt . '


                            </select>
    </div>
  </div>
 <div class="form-group" id="sabir" style="display:none;">
 <label for="subject" class="col-sm-2 control-label">Reference No:</label>
     <div class="col-sm-10">
      <input type="text" id="reference_no" name="reference_no" class="form-control" value="">
    </div>
  </div>

</form>

</div>
<div class="modal-footer">
<input type="hidden" id="payer" name="payer" value="' . $d['userid'] . '">
	<button id="save_proforma_payment" class="btn btn-primary">'.$_L['Save'].'</button>

		<button type="button" data-dismiss="modal" class="btn">'.$_L['Close'].'</button>
</div>
<script>
function showDiv(elem){
	console.log(elem);
		if(elem == "Cheque" || elem == "Electronic Transfer"){   
				document.getElementById("sabir").style.display = "block";
		}
		else{
			document.getElementById("sabir").style.display = "none";
		}
}

</script>';
        } else {
            exit('Invoice Not Found');
        }
        break;


    case 'mail_performa_':
        Event::trigger('invoices/mail_performa_/');
        $sid = $routes['2'];
        $etpl = $routes['3'];
        $d = ORM::for_table('sys_performa')->find_one($sid);
        if ($d) {
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
						
            $msg = Invoice::gen_proforma_email($sid,$etpl);
            if($msg){
                $subj = str_replace('{{company}}',$a->company,$msg['subject']);
                $message_o = $msg['body'];
								
                $email = $msg['email'];
                $name = $msg['name'];
            }
            else{
                $subj = '';
                $message_o = '';
                $email = '';
                $name = '';
            }
            if($d['cn'] != ''){
                $dispid = $d['cn'];
            }
            else{
                $dispid = $d['id'];
            }
            $in = $d['invoicenum']/* .$dispid */;
            echo '
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Performa #' . $d['id'] . '</h3>
</div>
<div class="modal-body">

<form class="form-horizontal" role="form" id="email_form" method="post">


<div class="form-group">
    <label for="toemail" class="col-sm-2 control-label">'.$_L['To'].'</label>
    <div class="col-sm-10">
      <input type="email" id="toemail" name="toemail" class="form-control" value="' . $email . '">
    </div>
  </div>

  <div class="form-group">
    <label for="ccemail" class="col-sm-2 control-label">'.$_L['Cc'].'</label>
    <div class="col-sm-10">
      <input type="email" id="ccemail" name="ccemail" class="form-control" value="">
    </div>
  </div>

  <div class="form-group">
    <label for="bccemail" class="col-sm-2 control-label">'.$_L['Bcc'].'</label>
    <div class="col-sm-10">
      <input type="email" id="bccemail" name="bccemail" class="form-control" value="">
      <span class="help-block"><a href="#" id="send_bcc_to_admin">'.$_L['Send Bcc to Admin'].'</a></span>
    </div>
  </div>

    <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Subject'].'</label>
    <div class="col-sm-10">
      <input type="text" id="subject" name="subject" class="form-control" value="' . $subj . '">
    </div>
  </div>

  <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Message Body'].'</label>
    <div class="col-sm-10">
      <textarea class="form-control sysedit" rows="3" name="message" id="message">' . $message_o . '</textarea>
      <input type="hidden" id="toname" name="toname" value="' . $name . '">
      <input type="hidden" id="i_cid" name="i_cid" value="' . $a['id'] . '">
      <input type="hidden" id="i_iid" name="i_iid" value="' . $d['id'] . '">
    </div>
  </div>


<div class="form-group">
    <label for="attach_pdf" class="col-sm-2 control-label">'.$_L['Attach PDF'].'</label>
    <div class="col-sm-10">
      <div class="checkbox c-checkbox">
                          <label>
                            <input type="checkbox" name="attach_pdf" id="attach_pdf" value="Yes" checked><span class="fa fa-check"></span>  <i class="fa fa-paperclip"></i> Proforma_'.$in.'.pdf
                          </label>
                        </div>
    </div>
  </div>


</form>

</div>
<div class="modal-footer">
	<button id="send_proforma" class="btn btn-primary">'.$_L['Send'].'</button>

		<button type="button" data-dismiss="modal" class="btn">'.$_L['Close'].'</button>
</div>';
        } else {
            exit('Invoice Not Found');
        }
        break; 
				case 'mail_invoice_':
        Event::trigger('invoices/mail_invoice_/');
        $sid = $routes['2'];
        $etpl = $routes['3'];
        $d = ORM::for_table('sys_invoices')->find_one($sid);
        if ($d) {
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $msg = Invoice::gen_email($sid,$etpl);
            if($msg){
                $subj = str_replace('{{company}}',$a->company,$msg['subject']);
                $message_o = $msg['body'];
								
                $email = $msg['email'];
                $name = $msg['name'];
            }
            else{
                $subj = '';
                $message_o = '';
                $email = '';
                $name = '';
            }
            if($d['cn'] != ''){
                $dispid = $d['cn'];
            }
            else{
                $dispid = $d['id'];
            }
            $in = $d['invoicenum']/* .$dispid */;
            echo '
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Invoice #' . $d['id'] . '</h3>
</div>
<div class="modal-body">

<form class="form-horizontal" role="form" id="email_form" method="post">


<div class="form-group">
    <label for="toemail" class="col-sm-2 control-label">'.$_L['To'].'</label>
    <div class="col-sm-10">
      <input type="email" id="toemail" name="toemail" class="form-control" value="' . $email . '">
    </div>
  </div>

  <div class="form-group">
    <label for="ccemail" class="col-sm-2 control-label">'.$_L['Cc'].'</label>
    <div class="col-sm-10">
      <input type="email" id="ccemail" name="ccemail" class="form-control" value="">
    </div>
  </div>

  <div class="form-group">
    <label for="bccemail" class="col-sm-2 control-label">'.$_L['Bcc'].'</label>
    <div class="col-sm-10">
      <input type="email" id="bccemail" name="bccemail" class="form-control" value="">
      <span class="help-block"><a href="#" id="send_bcc_to_admin">'.$_L['Send Bcc to Admin'].'</a></span>
    </div>
  </div>

    <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Subject'].'</label>
    <div class="col-sm-10">
      <input type="text" id="subject" name="subject" class="form-control" value="' . $subj . '">
    </div>
  </div>

  <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">'.$_L['Message Body'].'</label>
    <div class="col-sm-10">
      <textarea class="form-control sysedit" rows="3" name="message" id="message">' . $message_o . '</textarea>
      <input type="hidden" id="toname" name="toname" value="' . $name . '">
      <input type="hidden" id="i_cid" name="i_cid" value="' . $a['id'] . '">
      <input type="hidden" id="i_iid" name="i_iid" value="' . $d['id'] . '">
    </div>
  </div>


<div class="form-group">
    <label for="attach_pdf" class="col-sm-2 control-label">'.$_L['Attach PDF'].'</label>
    <div class="col-sm-10">
      <div class="checkbox c-checkbox">
                          <label>
                            <input type="checkbox" name="attach_pdf" id="attach_pdf" value="Yes" checked><span class="fa fa-check"></span>  <i class="fa fa-paperclip"></i> Invoice_'.$in.'.pdf
                          </label>
                        </div>
    </div>
  </div>


</form>

</div>
<div class="modal-footer">
	<button id="send" class="btn btn-primary">'.$_L['Send'].'</button>

		<button type="button" data-dismiss="modal" class="btn">'.$_L['Close'].'</button>
</div>';
        } else {
            exit('Invoice Not Found');
        }
        break;
    case 'send_email':
        Event::trigger('invoices/send_email/');
        $msg = '';
        $email = _post('toemail');
        $cc = _post('ccemail');
        $bcc = _post('bccemail');
        $subject = _post('subject');
        $toname = _post('toname');
        $cid = _post('i_cid');
        $iid = _post('i_iid');
        $d = ORM::for_table('sys_invoices')->find_one($iid);
        if($d['cn'] != ''){
            $dispid = $d['cn'];
        }
        else{
            $dispid = $d['id'];
        }
        $in = $d['invoicenum']/* .$dispid */;

        $message = $_POST['message'];
				
        $attach_pdf = _post('attach_pdf');
        $attachment_path = '';
        $attachment_file = '';
        if($attach_pdf == 'Yes'){
            Invoice::pdf($iid,'store');
            $attachment_path = 'application/storage/temp/Invoice_'.$in.'.pdf';
						
            $attachment_file = 'Invoice_'.$in.'.pdf';
        }
        if (!Validator::Email($email)) {
            $msg .= 'Invalid Email <br>';
        }
        if (!Validator::Email($cc)) {
            $cc = '';
        }
        if (!Validator::Email($bcc)) {
            $bcc = '';
        }
        if ($subject == '') {
            $msg .= 'Subject is Required <br>';
        }
        if ($message == '') {
            $msg .= 'Message is Required <br>';
        }
        if ($msg == '') {
            //now send email
          $res =  Notify_Email::_send($toname, $email, $subject, $message, $cid, $iid, $cc, $bcc, $attachment_path, $attachment_file);
            // Now check for
            echo '<div class="alert alert-success fade in">Mail Sent!</div>';
        } else {
            echo '<div class="alert alert-danger fade in">' . $msg . '</div>';
        }
        break; 

				case 'send_proforma_email':
        Event::trigger('invoices/send_proforma_email/');
        $msg = '';
        $email = _post('toemail');
        $cc = _post('ccemail');
        $bcc = _post('bccemail');
        $subject = _post('subject');
        $toname = _post('toname');
        $cid = _post('i_cid');
        $iid = _post('i_iid');
        $d = ORM::for_table('sys_performa')->find_one($iid);
        if($d['cn'] != ''){
            $dispid = $d['cn'];
        }
        else{
            $dispid = $d['id'];
        }
        $in = $d['invoicenum']/* .$dispid */;

        $message = $_POST['message'];
				
        $attach_pdf = _post('attach_pdf');
        $attachment_path = '';
        $attachment_file = '';
        if($attach_pdf == 'Yes'){
            Invoice::proforma_pdf($iid,'store');
            $attachment_path = 'application/storage/temp/Invoice_'.$in.'.pdf';
						
            $attachment_file = 'Invoice_'.$in.'.pdf';
        }
        if (!Validator::Email($email)) {
            $msg .= 'Invalid Email <br>';
        }
        if (!Validator::Email($cc)) {
            $cc = '';
        }
        if (!Validator::Email($bcc)) {
            $bcc = '';
        }
        if ($subject == '') {
            $msg .= 'Subject is Required <br>';
        }
        if ($message == '') {
            $msg .= 'Message is Required <br>';
        }
        if ($msg == '') {
            //now send email
          $res =  Notify_Email::_send($toname, $email, $subject, $message, $cid, $iid, $cc, $bcc, $attachment_path, $attachment_file);
            // Now check for
            echo '<div class="alert alert-success fade in">Mail Sent!</div>';
        } else {
            echo '<div class="alert alert-danger fade in">' . $msg . '</div>';
        }
        break;

    case 'stop_recurring':

        Event::trigger('invoices/stop_recurring/');


        $id = $routes['2'];
        $id = str_replace('sid', '', $id);
        $d = ORM::for_table('sys_invoices')->find_one($id);
        if ($d) {

            $d->r = '0';
            $d->save();
            r2(U . 'invoices/list-recurring', 's', 'Recurring Disabled for Invoice: ' . $id);

        } else {
            echo 'Invoice not found';
        }
        break;


    case 'add-payment-post':

        Event::trigger('invoices/add-payment-post/');
        
        /*echo '<pre>';
            var_dump($_POST);
        echo '</pre>';exit;*/        

        $msg = '';
        $account = _post('account');
        $date = _post('date');
        $amount = _post('amount');
        $amount = Finance::amount_fix($amount);
        $payerid = _post('payer');
        $pmethod = _post('pmethod');
        $branch_id = _post('branch_id');
        $ref = _post('ref');
        if($payerid == ''){
            $payerid = '0';
        }
        $amount = str_replace($config['currency_code'], '', $amount);
        $amount = str_replace(',', '', $amount);
        if (!is_numeric($amount)) {
            $msg .= 'Invalid Amount' . '<br>';
        }
        $cat = _post('cats');
        $iid = _post('iid');


        if ($payerid == '') {
            $msg .= 'Payer Not Found' . '<br>';
        }
        $description = _post('description');
        $msg = '';
        if ($description == '') {
            $msg .= $_L['description_error'] . '<br>';
        }

        if (Validator::Length($account, 100, 1) == false) {
            $msg .= 'Please choose an Account' . '<br>';
        }


        if (is_numeric($amount) == false) {
					$msg .= $_L['amount_error'] . '<br>';
        }

        if ($msg == '') {
            //find the current balance for this account
            $a = ORM::for_table('sys_accounts')->where('account', $account)->find_one();
            $cbal = $a['balance'];
            $nbal = $cbal + $amount;
            $a->balance = $nbal;
            $a->save();
            $d = ORM::for_table('sys_transactions')->create();
            $d->branch_id = $branch_id;
            $d->account = $account;
            $d->type = 'Income';
            $d->payerid = $payerid;

            $d->amount = $amount;
            $d->category = $cat;
            $d->method = $pmethod;
            $d->ref = $ref;
            $d->tags = '';


            $d->description = $description;
            $d->date = $date;
            $d->dr = '0.00';
            $d->cr = $amount;
            $d->bal = $nbal;
            $d->iid = $iid;


            //others
            $d->payer = '';
            $d->payee = '';
            $d->payeeid = '0';
            $d->status = 'Cleared';
            $d->tax = '0.00';
            //save
            $d->save();
            $tid = $d->id();
            _log('New Deposit: ' . $description . ' [TrID: ' . $tid . ' | Amount: ' . $amount . ']', 'Admin', $user['id']);
            _msglog('s', 'Transaction Added Successfully');
            //now work with invoice
            $i = ORM::for_table('sys_invoices')->find_one($iid);
            if ($i) {
                $pc = $i['credit'];
                $it = $i['subtotal'];
                $dp = $it - $pc;
                if (($dp == $amount) OR (($dp < $amount))) {
                    $i->status = 'Paid';

                } else {

                    $i->status = 'Partially Paid';
                }
                $i->credit = $pc + $amount;
                $i->save();
            }
            /* //now work with Proforma */
						$pinvoicenum = 'P'.$i->invoicenum; 
						$pr = ORM::for_table('sys_performa')->where('inv_id',$iid)->find_one();
            /* $pr = ORM::for_table('sys_performa')->where('invoicenum',$pinvoicenum)->find_one(); */
						
            if ($pr) {
							$pr->status = $i->status;
							$pr->save();
            }
            echo $tid;
        } else {
            echo '<div class="alert alert-danger fade in">' . $msg . '</div>';
        }

        break;


    case 'add-proforma-payment-post':

        Event::trigger('invoices/add-proforma-payment-post/');

        $msg = '';
        $account = _post('account');
        $date = _post('date');
        $amount = _post('amount');
        $amount = Finance::amount_fix($amount);
        $payerid = _post('payer');
        $pmethod = _post('pmethod');
        $ref = _post('ref');
        if($payerid == ''){
					$payerid = '0';
        }
        $amount = str_replace($config['currency_code'], '', $amount);
        $amount = str_replace(',', '', $amount);
        if (!is_numeric($amount)) {
					$msg .= 'Invalid Amount' . '<br>';
        }
        $cat = _post('cats');
        $iid = _post('iid');


        if ($payerid == '') {
					$msg .= 'Payer Not Found' . '<br>';
        }
        $description = _post('description');
        $msg = '';
        if ($description == '') {
					$msg .= $_L['description_error'] . '<br>';
        }

        if (Validator::Length($account, 100, 1) == false) {
					$msg .= 'Please choose an Account' . '<br>';
        }


        if (is_numeric($amount) == false) {
					$msg .= $_L['amount_error'] . '<br>';
        }

        if ($msg == '') {

            //find the current balance for this account
            $a = ORM::for_table('sys_accounts')->where('account', $account)->find_one();
            $cbal = $a['balance'];
            $nbal = $cbal + $amount;
            $a->balance = $nbal;
            $a->save(); 
            $d = ORM::for_table('sys_proforma_transactions')->create();
            $d->account = $account;
            $d->type = 'Income';
            $d->payerid = $payerid;

            $d->amount = $amount;
            $d->category = $cat;
            $d->method = $pmethod;
            $d->ref = $ref;
            $d->tags = '';


            $d->description = $description;
            $d->date = $date;
            $d->dr = '0.00';
            $d->cr = $amount;
            $d->bal = $nbal;
            $d->iid = $iid;


            //others
            $d->payer = '';
            $d->payee = '';
            $d->payeeid = '0';
            $d->status = 'Cleared';
            $d->tax = '0.00';
            //save
            $d->save();
            $tid = $d->id();
            _log('New Deposit: ' . $description . ' [TrID: ' . $tid . ' | Amount: ' . $amount . ']', 'Admin', $user['id']);
            _msglog('s', 'Transaction Added Successfully');
            //now work with invoice
            $i = ORM::for_table('sys_performa')->find_one($iid);
            if ($i) {
							$pc = $i['credit'];
							$it = $i['subtotal'];
							$dp = $it - $pc;
							if (($dp == $amount) OR (($dp < $amount))) {
									$i->status = 'Paid';

							} else {

									$i->status = 'Partially Paid';
							}
							$i->credit = $pc + $amount;
							$i->save();
            }
            echo $tid;
        } else {
            echo '<div class="alert alert-danger fade in">' . $msg . '</div>';
        }

        break;

    case 'export_csv':

        $fileName = 'transactions_'.time().'.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen( 'php://output', 'w' );

        $headerDisplayed = false;

        // $results = ORM::for_table('crm_Accounts')->find_array();
        $results = db_find_array('sys_invoices');

        foreach ( $results as $data ) {
            // Add a header row if it hasn't been added yet
            if ( !$headerDisplayed ) {
                // Use the keys from $data as the titles
                fputcsv($fh, array_keys($data));
                $headerDisplayed = true;
            }

            // Put the data into the stream
            fputcsv($fh, $data);
        }
// Close the file
        fclose($fh);



        break;

    case 'payments':

        $mode_css = Asset::css('footable/css/footable.core.min');

        $mode_js = Asset::js(array('numeric','footable/js/footable.all.min'));

        $d = ORM::for_table('sys_transactions')->where_not_equal('iid','0')->limit(500)->find_array();

        $ui->assign('d',$d);

        $ui->assign('xheader', $mode_css);
        $ui->assign('xfooter', $mode_js);

        $ui->assign('xjq', '
        
        $(\'.footable\').footable();
        
         $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });
$(".cdelete").click(function (e) {
        e.preventDefault();
        var id = this.id;
        bootbox.confirm("'.$_L['are_you_sure'].'", function(result) {
           if(result){
               var _url = $("#_url").val();
               window.location.href = _url + "delete/invoice/" + id;
           }
        });
    });



 ');

        $ui->display('payments.tpl');


        break;


        case 'get-designs-by-clothid':

        Event::trigger('invoices/get-designs-by-clothid/');


        $id = $_POST['cloth_id'];
        $designs = ORM::for_table('sys_designs')->where('cloth_id', $id)->find_many();

        $options = '<option value="">Select Design</option>';
        foreach($designs as $row)
        {
            $image = json_decode($row['image'] , true);
            $options .= '<option data-image="'.$image[0].'" value="'.$row['id'].'">'.$row['name'].'</option>';
        }
        echo $options;

        break;

        case 'delete-addtional-img':

        Event::trigger('invoices/delete-addtional-img/');


        $id = $_POST['inv_id'];
        $addt_img = $_POST['addt_img'];

        $d = ORM::for_table('sys_invoices')->find_one($id);
        
        $img_array = array();
        foreach(json_decode($d['additional_imgs']) as $row)
        {
            if($row == $addt_img)
            {
                unlink($addt_img);
            }
            else
            {
                $img_array[] = $row;
            }
        }
        $imgs = json_encode($img_array);

        $d->additional_imgs = $imgs;
        $d->save();

        break;  
        
        
        case 'get-scanned-product':
            $id = $_POST['product_id'];
            $last_row = (!empty($_POST['last_row'])) ? str_replace('i_','',$_POST['last_row']) + 1 : 1;        
            $items = '';

            $product = ORM::for_table('sys_items')->find_one($id);
            if($product['id'])
            {
                $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

                $item_desc = $product['description'] ? ' ('.$product['description'].')' : '';

                $items .= 
                '
                <tr>
                    <td class="number">
                        '.$product_img.'
                        <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                        <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                        <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                    </td>
                    <td>
                        <input type="text" class="form-control item_name" name="desc[]" value="'.$product['name'].$item_desc.'" id="i_'.$last_row.'" required="">
                    </td>
                    <td>
                        <input type="text" class="form-control qty" value="1" name="qty[]">
                    </td>
                    <td>
                        <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                    </td>
                    <td class="ltotal">
                        <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price'].'" readonly="" required="">
                    </td>
                    <td>
                    <input type="hidden" name="item_type[]" value="product">
                    </td>                 
                    <td class="delete">
                        <i class="fa fa-trash tr-remove"></i>
                    </td>
                </tr>            
                ';
                $last_row++;
            }

            echo $items;

        break; 

        case 'get-scanned-product-edit-invoice':
            $id = $_POST['product_id'];
            $last_row = (!empty($_POST['last_row'])) ? str_replace('i_','',$_POST['last_row']) + 1 : 1;        
            $items = '';

            $product = ORM::for_table('sys_items')->find_one($id);
            if($product['id'])
            {
                $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

                $item_desc = $product['description'] ? ' ('.$product['description'].')' : '';

                $items .= 
                '
                <tr>
                    <td class="number">
                        '.$product_img.'
                        <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                        <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                        <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                    </td>
                    <td>
                        <input type="text" class="form-control item_name" name="desc[]" value="('.$product['name'].$item_desc.'" id="i_'.$last_row.'" required="">
                    </td>
                    <td>
                        <input type="text" class="form-control qty" value="1" name="qty[]">
                    </td>
                    <td>
                        <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                    </td>
                    <td class="ltotal">
                        <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price'].'" readonly="" required="">
                    </td>
                    <td class="hide">
                    <input type="hidden" name="item_type[]" value="product">
                    </td>                 
                </tr>            
                ';                

                $last_row++;
            }

            echo $items;

        break;         

        case 'get-customized-items':

        Event::trigger('invoices/get-customized-items/');

        $id = $_POST['design_id'];
        $last_row = (!empty($_POST['last_row'])) ? str_replace('i_','',$_POST['last_row']) + 1 : 1;

        $design = ORM::for_table('sys_designs')->find_one($id);
        $cloth  = ORM::for_table('sys_cloths')->find_one($design['cloth_id']);

        if(empty($design['name']))
        {
            return false;
        }

        $items = '';

        $fabrics = json_decode($design['fabrics'], true);
        
        foreach($fabrics as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['fabric_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['fabric_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['fabric_qty'].'" readonly="" required="">
                </td>
                <td>
                <input type="hidden" name="item_type[]" value="product">
                </td>                 
                <td class="delete">
                    <i class="fa fa-trash tr-remove"></i>
                </td>
            </tr>            
            ';
            $last_row++;
        }

        $stones = json_decode($design['stones'], true);
        
        foreach($stones as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['stone_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['stone_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['stone_qty'].'" readonly="" required="">
                </td>
                <td>
                    <input type="hidden" name="item_type[]" value="product">
                </td>                 
                <td class="delete">
                    <i class="fa fa-trash tr-remove"></i>
                </td>
            </tr>            
            ';
            $last_row++;
        }  
        
        

        $handwork = json_decode($design['handworks'], true);
        
        foreach($handwork as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['handwork_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['handwork_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['handwork_qty'].'" readonly="" required="">
                </td>
                <td>
                    <input type="hidden" name="item_type[]" value="product">
                </td>                 
                <td class="delete">
                    <i class="fa fa-trash tr-remove"></i>
                </td>
            </tr>            
            ';
            $last_row++;
        }

        $others = json_decode($design['others'], true);
        
        foreach($others as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['other_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['other_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['other_qty'].'" readonly="" required="">
                </td>
                <td>
                    <input type="hidden" name="item_type[]" value="product">
                </td>                 
                <td class="delete">
                    <i class="fa fa-trash tr-remove"></i>
                </td>
            </tr>            
            ';
            $last_row++;
        }        




        $design_img = json_decode($design['image'], true)[0]; 
        $design_image = $design_img ? '<a target="_blank" href="'.$design_img.'"><img width="50px" height="50px" src="'.$design_img.'"></a>' : '-';
        
        $items .= 
        '
        <tr>
            <td class="number">
                '.$design_image.'
                <input type="hidden" class="form-control sid" name="s_id[]" value="0" id="s_id">
                <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$design['id'].'" id="p_id">
                <input type="hidden" name="pimg[]" value="'.$design_img.'">
            </td>
            <td>
                <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - Silai" id="i_'.$last_row.'" required="">
            </td>
            <td>
                <input type="text" class="form-control qty" value="1" name="qty[]">
            </td>
            <td>
                <input type="text" class="form-control item_price" name="amount[]" value="'.$design['price'].'">
            </td>
            <td class="ltotal">
                <input type="number" class="form-control lvtotal" name="total[]" value="'.$design['price'].'" readonly="" required="">
            </td>
            <td>
            <input type="hidden" name="item_type[]" value="design">
        </td>             
            <td class="delete">
                <i class="fa fa-trash tr-remove"></i>
            </td>
        </tr>            
        ';     
        $last_row++;   

        echo $items;

        break;


        case 'get-customized-items-for-edit-invoice':

        Event::trigger('invoices/get-customized-items-for-edit-invoice/');

        $id = $_POST['design_id'];
        $last_row = (!empty($_POST['last_row'])) ? str_replace('i_','',$_POST['last_row']) + 1 : 1;

        $design = ORM::for_table('sys_designs')->find_one($id);
        $cloth  = ORM::for_table('sys_cloths')->find_one($design['cloth_id']);


        $items = '';

        $fabrics = json_decode($design['fabrics'], true);
        
        foreach($fabrics as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['fabric_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['fabric_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['fabric_qty'].'" readonly="" required="">
                </td>
                <td class="hide">
                <input type="hidden" name="item_type[]" value="product">
                </td>                 
            </tr>            
            ';
            $last_row++;
        }

        $stones = json_decode($design['stones'], true);
        
        foreach($stones as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['stone_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['stone_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['stone_qty'].'" readonly="" required="">
                </td>
                <td class="hide">
                    <input type="hidden" name="item_type[]" value="product">
                </td>                 
            </tr>            
            ';
            $last_row++;
        }
        
        $handwork = json_decode($design['handworks'], true);
        
        foreach($handwork as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['handwork_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['handwork_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['handwork_qty'].'" readonly="" required="">
                </td>
                <td class="hide">
                    <input type="hidden" name="item_type[]" value="product">
                </td>                 
            </tr>            
            ';
            $last_row++;
        }
        
        $others = json_decode($design['others'], true);
        
        foreach($others as $row)
        {
            $product = ORM::for_table('sys_items')->find_one($row['other_id']);

            $product_img = $product['product_image'] ? '<a target="_blank" href="'.$product['product_image'].'"><img width="50px" height="50px" src="'.$product['product_image'].'"></a>' : '-';

            $items .= 
            '
            <tr>
                <td class="number">
                    '.$product_img.'
                    <input type="hidden" class="form-control sid" name="s_id[]" value="'.$product['item_number'].'" id="s_id">
                    <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$product['id'].'" id="p_id">
                    <input type="hidden" name="pimg[]" value="'.$product['product_image'].'">
                </td>
                <td>
                    <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - '.$product['name'].'" id="i_'.$last_row.'" required="">
                </td>
                <td>
                    <input type="text" class="form-control qty" value="'.$row['other_qty'].'" name="qty[]">
                </td>
                <td>
                    <input type="text" class="form-control item_price" name="amount[]" value="'.$product['sales_price'].'">
                </td>
                <td class="ltotal">
                    <input type="number" class="form-control lvtotal" name="total[]" value="'.$product['sales_price']*$row['other_qty'].'" readonly="" required="">
                </td>
                <td class="hide">
                    <input type="hidden" name="item_type[]" value="product">
                </td>                 
            </tr>            
            ';
            $last_row++;
        }        


        $design_img = json_decode($design['image'], true)[0]; 
        $design_image = $design_img ? '<a target="_blank" href="'.$design_img.'"><img width="50px" height="50px" src="'.$design_img.'"></a>' : '-';
        
        $items .= 
        '
        <tr>
            <td class="number">
                '.$design_image.'
                <input type="hidden" class="form-control sid" name="s_id[]" value="0" id="s_id">
                <input type="hidden" class="form-control item_id" name="p_id[]" value="'.$design['id'].'" id="p_id">
                <input type="hidden" name="pimg[]" value="'.$design_img.'">
            </td>
            <td>
                <input type="text" class="form-control item_name" name="desc[]" value="('.$cloth['name'].' / '.$design['name'].') - Silai" id="i_'.$last_row.'" required="">
            </td>
            <td>
                <input type="text" class="form-control qty" value="1" name="qty[]">
            </td>
            <td>
                <input type="text" class="form-control item_price" name="amount[]" value="'.$design['price'].'">
            </td>
            <td class="ltotal">
                <input type="number" class="form-control lvtotal" name="total[]" value="'.$design['price'].'" readonly="" required="">
            </td>
            <td class="hide">
            <input type="hidden" name="item_type[]" value="design">
        </td>             
        </tr>            
        ';     
        $last_row++;   

        echo $items;

        break;        


    default:
        echo 'action not defined';
}
