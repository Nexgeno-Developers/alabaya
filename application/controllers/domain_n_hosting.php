<?php 

if(!isset($myCtrl)){
    $myCtrl = 'domain_n_hosting';
}
_auth();
$ui->assign('_application_menu', 'domain_n_hosting');
$ui->assign('_title', 'Domain And Hosting - '. $config['CompanyName']);
$ui->assign('_st', 'Domain and Hosting');

$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);

$ui->assign('jsvar', '
_L[\'Working\'] = \''.$_L['Working'].'\';
_L[\'Submit\'] = \''.$_L['Submit'].'\';
 ');


switch ($action) {
    case 'add':

        Event::trigger('domain_n_hosting/add/');

				$c = ORM::for_table('crm_accounts')->select('id')->select('account')->select('company')->select('email')->order_by_asc('account')->find_many();
				$services = ORM::for_table('sys_items')->where('type','Service')->order_by_asc('id')->find_many();
        $ui->assign('services',$services);
				
        $ui->assign('c', $c);
				$ui->assign('idate', date('Y-m-d'));
				
        $ui->assign('xheader', Asset::css(array('modal','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'add-contact','modal')));
        $tags = Tags::get_all('Contacts');
        $ui->assign('tags',$tags);
        $ui->assign('xjq', '$("#country").select2({ theme: "bootstrap" }); ');
        $ui->assign('jsvar', '_L[\'Working\'] = \''.$_L['Working'].'\'; ');
        $currencies = Model::factory('Models_Currency')->find_array();
        $ui->assign('currencies',$currencies);
				
        $ui->display('add-domain-n-hosting.tpl');
      break;



  case 'generate-proforma':
	  Event::trigger('domain_n_hosting/generate-proforma/');
		$i = 1;
		$id = $routes['2'];
		$c = ORM::for_table('crm_domain_n_hosting')->where('id', $id)->find_one();
		$total = $c['domain_price']+$c['hosting_price'];
		$taxTotal = ($c['domain_price']+$c['hosting_price'])*18/100;
		if(!empty($c['domain_price']) && !empty($c['hosting_price'])){
				$i = 2;
		}
		$notes = '<p>- GST as Applicable @ Time of Raising Invoice.</p><p>- Our current billing Name : Mak Enterprises<br></p><p>- Support is applicable against the payment of the said invoice shared as against this proposal.</p><p>- Website maintenance charges will be sperate.</p><p>- Payment: Immediate.</p>';
		$inv_no = ORM::for_table('sys_performa')->where('company_id', 2)->max('invoice_no');
			
						$inv_no = $inv_no+1;
			/* save to sys_invoices */
            $d = ORM::for_table('sys_performa')->create();
            $d->userid = $c['account'];
            $d->account = get_type_by_id('crm_accounts', 'id', $c['account'], 'account');
						$d->company_id = 2;
						$d->invoice_no = $inv_no;
						$d->cn = '';
						$d->invoicenum = 'PMI'.date('ymd').$inv_no;
            $d->date = date('ymd');
            $d->duedate = date('ymd');
            $d->datepaid = date("Y-m-d H:i:s");
            $d->subtotal = Finance::amount_fix($total+$taxTotal);
						 $d->discount_type = '';
            $d->discount_value = '';
            $d->discount = '';
            $d->total = Finance::amount_fix($total);
            $d->vtoken = _raid(10);
            $d->ptoken = _raid(10);
            $d->status = 'Unpaid';
            $d->notes = $notes;
            $d->r = 0;
            $d->nd = date('ymd');
						$d->taxamt = Finance::amount_fix($taxTotal);
						$d->taxid = 1;
            /* //others */
            $d->paymentmethod = '';
            $d->currency = 1;
            $d->currency_symbol = 'RS';
            $d->currency_rate = 1.0000;

            $d->save(); /* //save */
						 $invoiceid = $d->id();
						 for($j=1;$j<=$i;$j++){
							 	$d = ORM::for_table('sys_performaitems')->create();
								$d->invoiceid = $invoiceid;
								$d->userid = $c['account'];
								if($j==1){
									$d->itemcode = 2;
									$d->description = 'Domain';
									$d->amount = Finance::amount_fix($c['domain_price']);
									$taxamt = $c['domain_price']*18/100;
									$total = $c['domain_price']+$taxamt;
								}
								if($j==2){
									$d->itemcode = 3;
									$d->description = 'Hosting';
										$d->amount = Finance::amount_fix($c['hosting_price']);
										$taxamt = $c['hosting_price']*18/100;
										$total = $c['hosting_price']+$taxamt;
								}
								
								
								$d->qty = Finance::amount_fix(1);
								$d->taxrate = 18.00;
								$d->tax_id = 1;
								
								$d->taxamount = Finance::amount_fix($taxamt);
								$d->total = Finance::amount_fix($taxamt + $total);
								/* //others */
								$d->type = '';
								$d->relid = '0';
								$d->duedate = date('Y-m-d');
								$d->paymentmethod = '';
								$d->notes = '';

								$d->save(); /* //save */
								$pid = $d->id();
             _log('Proforma is Created'.' '.'PMI'.date('ymd').$inv_no.' [PID: '.$pid.']','Admin',$user['id']);
						  
						 }
						 r2(U . 'domain_n_hosting/list', 's', 'Proforma is Created');	
	  break;
    case 'edit':
		$ui->assign('xheader', Asset::css('s2/css/select2.min'));
        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'add-contact')));
        $id  = $routes['2'];
					$c = ORM::for_table('crm_accounts')->select('id')->select('account')->select('company')->select('email')->order_by_desc('id')->find_many();
        $ui->assign('c', $c);
        $d = ORM::for_table('crm_domain_n_hosting')->find_one($id);
				/* $dom_host = array('Domain','Hosting','Domain And Hosting'); */
				var_dump($d['service']);
				if($d['service'] == 1 || $d['service'] == 2 || $d['service'] == 3){
					$services = ORM::for_table('sys_items')->where('type','Service')->where_any_is(array(
                array('name' => 'Domain'),
                array('name' => 'Hosting'),
                array('name' => 'Domain And Hosting')))
            ->order_by_asc('id')->find_many();
				}else{
					$services = ORM::for_table('sys_items')->where('type','Service')->where_raw('(`name` != ? AND `name` != ? AND `name` != ?)', array('Domain', 'Hosting', 'Domain And Hosting'))->order_by_asc('id')->find_many();
				}
				/* $services = ORM::for_table('sys_items')->where('type','Service')->order_by_asc('id')->find_many(); */
        $ui->assign('services',$services);
        if($d){

            $ui->assign('d',$d);
            $ui->display('domain-n-hosting-edit.tpl');

        }
        else{
					r2(U . 'domain_n_hosting/list', 'e', 'Domain Not Found');
        }


        break;

    case 'add-post':
			Event::trigger('domain_n_hosting/add-post/');
			Event::trigger('domain_n_hosting/add-post/_on_start');

			$cid = _post('cid');
			$domain_name = _post('domain_name');
			$amount = _post('amount');
			$service = _post('service');
			
			$domain_host_plan = NULL;
			$service_plan = NULL;
			$register_date = _post('register_date');
			
			/* for Domain=1 Hosting=2 and "Domain and Hosting"=3 */
			if($service == 1 || $service == 2 || $service == 3){
				$domain_host_plan = _post('domain_host_plan');
				$dh_today = strtotime($register_date);
				$dh_today = strtotime('+ '.$domain_host_plan.' year', $dh_today);
				$expiry_date = date('Y-m-d',$dh_today);
			}else{
				$service_plan = _post('service_plan');
				$serv_today = strtotime($register_date);
				$serv_after = strtotime('+ '.$service_plan.' month', $serv_today);
				$expiry_date = date('Y-m-d',$serv_after);
			}

			$msg = '';

			if($cid == ''){
					$msg .= $_L['Customer Name is required'].' <br>';
			}

			if($msg == ''){

				$d = ORM::for_table('crm_domain_n_hosting')->create();

				$d->account = $cid;
				$d->domain_name = $domain_name;
				$d->service = $service;
				$d->amount 	= $amount;
				$d->d_h_plan_yearly = $domain_host_plan;
				$d->service_plan_monthly = $service_plan;
				$d->register_date = $register_date;
				$d->expiry_date = $expiry_date;
				$d->created = strtotime(date('Y-m-d'));
				$d->save();
				$lid = $d->id();
				 _log('New Domain and Hosting Added'.' '.$domain_name.' [LID: '.$lid.']','Admin',$user['id']);
				 
				echo $lid; 
			} else {
				echo $msg;
			}
			
    break;
    case 'view':

        Event::trigger('domain_n_hosting/view/');

        $id  = $routes['2'];
        $d = ORM::for_table('crm_domain_n_hosting')->find_one($id);
        if($d){

            $extra_tab = '';
            $extra_jq = '';

            $tab = route(3);

            if(!$tab){

                $tab = 'summary';

            }

            $ui->assign('tab',$tab);

            Event::trigger('domain_n_hosting/view/_on_start');

            $ui->assign('extra_tab', $extra_tab);

            /* // invoice count */

            $inv_count = ORM::for_table('sys_invoices')->where('userid',$id)->count();

            if($inv_count == ''){
                $inv_count = 0;
            }

            $ui->assign('inv_count',$inv_count);

            /* //find all activity for this user
//            $ac = ORM::for_table('sys_activity')->where('cid',$id)->limit(20)->order_by_desc('id')->find_many();
//            $ui->assign('ac',$ac); */

            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','sn/summernote','sn/summernote-bs3','sn/summernote-application','imgcrop/assets/css/croppic')));




            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'sn/summernote.min','imgcrop/croppic','numeric','profile')));

            $ui->assign('xjq', '
 var cid = $(\'#cid\').val();
    var _url = $("#_url").val();
    var cb = function cb (){

            };'.$extra_jq);

            $ui->assign('d',$d);

            Event::trigger('domain_n_hosting/view/_on_display');

            $ui->display('account-profile-alt.tpl');

        }
        else{
            r2(U . 'domain_n_hosting/list/', 'e', 'Domain Not Found');
        }

        break;

    case 'list':
		/* //send_mail_domain_hosting(); */
        Event::trigger('domain_n_hosting/list/');

      /* //  $ui->assign('_st', $_L['Contacts'].'<span class="pull-right"><a href="'.U.'contacts/set_view_mode/card/'.'"><i class="fa fa-th"></i></a> <a href="'.U.'contacts/set_view_mode/tbl/'.'"><i class="fa fa-align-justify"></i></a> <a href="'.U.'contacts/set_view_mode/search/'.'"><i class="fa fa-search"></i></a></span>'); */

        $ui->assign('_st', 'Domain and Hosting'.'<div class="btn-group pull-right" style="padding-right: 10px;">
					<a class="btn btn-success btn-xs" href="'.U.'domain_n_hosting/set_view_mode/card/'.'" style="box-shadow: none;"><i class="fa fa-th"></i></a>
					<a class="btn btn-primary btn-xs" href="'.U.'domain_n_hosting/set_view_mode/tbl/'.'" style="box-shadow: none;"><i class="fa fa-align-justify"></i></a>
					<a class="btn btn-success btn-xs" href="'.U.'domain_n_hosting/set_view_mode/search/'.'" style="box-shadow: none;"><i class="fa fa-search"></i></a>
					<a class="btn btn-primary btn-xs" href="'.U.'domain_n_hosting/export_csv/'.'" style="box-shadow: none;"><i class="fa fa-download"></i></a>
					<a class="btn btn-success btn-xs" href="'.U.'domain_n_hosting/import_csv/'.'" style="box-shadow: none;"><i class="fa fa-upload"></i></a>
				</div>');

        $name = _post('name');
       /*  //find all tags */

        $mode_css = '';
        $mode_js = '';

        if($config['contact_set_view_mode'] == 'search'){

           /*  // Foo Table */

            $mode_css = Asset::css('footable/css/footable.core.min');

            $mode_js = Asset::js(array('footable/js/footable.all.min','domain_n_hosting/mode_search'));

            $d = ORM::for_table('crm_domain_n_hosting')->order_by_desc('id')->find_many();

            $paginator['contents'] = '';


        }
        else{
            $paginator = Paginator::bootstrap('crm_domain_n_hosting');
            $d = ORM::for_table('crm_domain_n_hosting')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('id')->find_many();
        }

        $mode_css = Asset::css(array('footable/css/footable.core.min','modal','sn/summernote','sn/summernote-bs3','modal','sn/summernote-application'));
				$mode_js = Asset::js(array('footable/js/footable.all.min','contacts/mode_search','sn/summernote.min','modal'));
				$ui->assign('d',$d);
        $ui->assign('paginator',$paginator);

                $ui->assign('xheader', $mode_css);


        $ui->assign('xfooter', $mode_js.
            '<script type="text/javascript" src="' . $_theme . '/lib/list-contacts.js"></script>');
        $ui->assign('jsvar', '_L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';');
        $ui->display('list-domain-n-hosting.tpl');

        break;


    case 'edit-post':

        Event::trigger('domain_n_hosting/edit-post/');
				$id = $_POST['id'];
        $d = ORM::for_table('crm_domain_n_hosting')->find_one($id);
        if($d){
            $account 					= _post('cid');
            $domain_name 			= _post('domain_name');
            $service 					= _post('service');
            $amount 					= _post('amount');
						
						$domain_host_plan = NULL;
						$service_plan = NULL;
						$register_date = _post('register_date');
						
						/* for Domain=1 Hosting=2 and "Domain and Hosting"=3 */
						if($service == 1 || $service == 2 || $service == 3){
							$domain_host_plan = _post('domain_host_plan');
							$dh_today = strtotime($register_date);
							$dh_today = strtotime('+ '.$domain_host_plan.' year', $dh_today);
							$expiry_date = date('Y-m-d',$dh_today);
						}else{
							$service_plan = _post('service_plan');
							$serv_today = strtotime($register_date);
							$serv_after = strtotime('+ '.$service_plan.' month', $serv_today);
							$expiry_date = date('Y-m-d',$serv_after);
						}
            $msg = '';

            if($domain_name == ''){
                $msg .= $_L['Domain Name is required']. ' <br>';
            }

            if($msg == ''){

                $d = ORM::for_table('crm_domain_n_hosting')->find_one($id);
								
								$d->account = $account;
								$d->domain_name = $domain_name;
								$d->service = $service;
								$d->amount 	= $amount;
								$d->d_h_plan_yearly = $domain_host_plan;
								$d->service_plan_monthly = $service_plan;
								$d->register_date = $register_date;
								$d->expiry_date = $expiry_date;

                $d->save();


                _msglog('s','Domain and Hosting Updated Successfully.');

                echo $id;
            }
            else{
                echo $msg;
            }

        }
        else{
            r2(U.$myCtrl.'/list', 'e', 'Domain Not Found.');
        }

        break;
    case 'delete':

        Event::trigger('contacts/delete/');


        $id = $routes['2'];
        if($_app_stage == 'Demo'){
            r2(U.$myCtrl.'/list/', 'e', 'Sorry! Deleting Account is disabled in the demo mode.');
        }
        $d = ORM::for_table('crm_accounts')->find_one($id);
        if($d){
            $d->delete();
            r2(U.$myCtrl.'/list/', 's', $_L['account_delete_successful']);
        }

        break;

        case 'renewal':
          Event::trigger('domain_n_hosting/renewal/');
          $sid = $routes['2'];
          $d = ORM::for_table('crm_domain_n_hosting')->find_one($sid);
          if ($d) {
              $a = ORM::for_table('crm_accounts')->find_one($d['account']);
              $msg = ORM::for_table('sys_email_templates')->where('tplname', 'Rental:Reminder for Domain Hosting')->find_one();;
              if($msg){
                  $subj = str_replace('{{company}}',$a->company,$msg['subject']);
                  $message_o = str_replace(['{{domain_name}}','{{renewal_date}}'],[$d->domain_name,$d->expiry_date],$msg['message']); $msg['message'];
                  
                  $email = $a['email'];
                  $name = $a['account'];
              }
              else{
                  $subj = '';
                  $message_o = '';
                  $email = '';
                  $name = '';
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

        case 'send_reminder_':
          Event::trigger('domain_n_hosting/send_reminder_/');
          $sid = $routes['2'];
          $d = ORM::for_table('crm_domain_n_hosting')->find_one($sid);
          if ($d) {
              $a = ORM::for_table('crm_accounts')->find_one($d['account']);
              $msg = ORM::for_table('sys_email_templates')->where('tplname', 'Rental:Reminder for Domain Hosting')->find_one();;
              if($msg){
                  $subj = str_replace('{{company}}',$a->company,$msg['subject']);
                  $message_o = str_replace(['{{domain_name}}','{{renewal_date}}'],[$d->domain_name,$d->expiry_date],$msg['message']); $msg['message'];
                  
                  $email = $a['email'];
                  $name = $a['account'];
              }
              else{
                  $subj = '';
                  $message_o = '';
                  $email = '';
                  $name = '';
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

        Event::trigger('domain_n_hosting/send_email/');

        $msg = '';
        /* $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid); */
        $email = _post('toemail');
        $toname = _post('toname');
        $subject = _post('subject');
        if($subject == ''){
            $msg .= $_L['Subject is Empty'].' <br>';
        }
        $message = $_POST['message'];
        if($message == ''){
            $msg .= $_L['Message is Empty'].' <br>';
        }
        if($msg == ''){
            //send email
            Notify_Email::_send($toname,$email,$subject,$message,'');
            echo 'Sent';

        }
        else{
            echo $msg;
        }
        break;


    case 'modal_add':

        Event::trigger('contacts/modal_add/');

        $ui->assign('countries',Countries::all($config['country'])); /* // may add this $config['country_code'] */
        $ui->display('modal_add_contact.tpl');


        break;


    case 'set_view_mode':

        Event::trigger('domain_n_hosting/set_view_mode/');

/* //        if(isset($routes['2']) AND ($routes['2'] != 'tbl')){
//            $mode = 'card';
//        }
//        else{
//            $mode = 'tbl';
//        } */

        if(isset($routes[2]) AND ($routes[2] != '')){
            $mode = $routes['2'];
        }

        else{
            $mode = 'tbl';
        }

        $available_mode = array("tbl", "card", "search");
        if (in_array($mode, $available_mode)) {

            update_option('contact_set_view_mode',$mode);

        }

        r2(U.'domain_n_hosting/list/');

        break;



    case 'export_csv':


        $fileName = 'domain_n_hosting_'.time().'.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen( 'php://output', 'w' );

        $headerDisplayed = false;

       /* // $results = ORM::for_table('crm_Accounts')->find_array(); */
        $results = db_find_array('crm_domain_n_hosting',array('id','account','domain_name','domain_price','d_reg_date','d_expiry_date','hosting_price','h_reg_date','h_expiry_date'));

        foreach ( $results as $data ) {
            /* // Add a header row if it hasn't been added yet */
            if ( !$headerDisplayed ) {
                /* // Use the keys from $data as the titles */
                fputcsv($fh, array_keys($data));
                $headerDisplayed = true;
            }

            /* // Put the data into the stream */
            fputcsv($fh, $data);
        }
/* // Close the file */
        fclose($fh);



        break;



    case 'dev_demo_data':


        /* // this only work with dev mode */
        is_dev();





        break;

    case 'import_csv':


        $ui->assign('xheader', Asset::css(array('dropzone/dropzone')));


        $ui->assign('xfooter', Asset::js(array('dropzone/dropzone','domain_n_hosting/import')));



        $ui->display('contacts_import.tpl');



        break;

    case 'csv_upload':

        $uploader   =   new Uploader();
				
        $uploader->setDir('application/storage/temp/');
      /*  // $uploader->sameName(true); */
        $uploader->setExtensions(array('csv')); /*  //allowed extensions list// */
        if($uploader->uploadFile('file')){   /* //txtFile is the filebrowse element name // */
            $uploaded  =   $uploader->getUploadName(); /* //get uploaded file name, renames on upload// */

            $_SESSION['uploaded'] = $uploaded;

        }else{ /* //upload failed */
            _msglog('e',$uploader->getMessage()); /* //get upload error message */
        }


        break;

    case 'csv_uploaded':


        if(isset($_SESSION['uploaded'])){

            $uploaded = $_SESSION['uploaded'];

          /* // _msglog('s',$uploaded);

//            $csvData = file_get_contents('application/storage/temp/'.$uploaded);
//            $lines = explode(PHP_EOL, $csvData);
//            $contacts = array();
//            foreach ($lines as $line) {
//                $contacts[] = str_getcsv($line);
//            } */




            $csv = new parseCSV();
            $csv->auto('application/storage/temp/'.$uploaded);

            $contacts = $csv->data;



            $cn = 0;

            foreach($contacts as $contact){

                $data = array();
                $data['account'] = $contact['Full Name'];
                $data['email'] = $contact['Email'];
                $data['phone'] = $contact['Phone'];
                $data['address'] = $contact['Address'];
                $data['city'] = $contact['City'];
                $data['zip'] = $contact['Zip'];                           
                $data['state'] = $contact['State'];
                $data['country'] = $contact['Country'];
                $data['company'] = $contact['Company'];
                $data['balance'] = $contact['Balance'];
                $data['gst_no'] = $contact['Gst No'];
                
                $save = Contacts::add($data);

                if(is_numeric($save)){

                    $cn++;

                }


            }


            _msglog('s',$cn.' Contacts Imported');

/* //            ob_start();
//            var_dump($contacts);
//            $result = ob_get_clean();
//
//            _msglog('s',$result); */



        }
        else{

            _msglog('e','An Error Occurred while uploading the files');

        }


        break;


    case 'groups':

       /*  // find all groups */

        $gs = ORM::for_table('crm_groups')->order_by_asc('sorder')->find_array();

        $ui->assign('gs',$gs);

        $ui->assign('xfooter',Asset::js(array('contacts/groups')));

        $ui->assign('jsvar', '
_L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';
 ');

        $ui->display('crm_groups.tpl');



        break;


    case 'add_group':

        $group_name = _post('group_name');

        if($group_name != ''){

            /* //check same group already exist */

            $c = ORM::for_table('crm_groups')->where('gname',$group_name)->find_one();

            if($c){

                ib_die('A Group with same name already exist');

            }

            $d = ORM::for_table('crm_groups')->create();
            $d->gname = $group_name;
            $d->color = '';
            $d->discount = '';
            $d->parent = '';
            $d->pid = 0;
            $d->exempt = '';
            $d->description = '';
            $d->separateinvoices = '';
            $d->sorder = 0;
            $d->c1 = '';
            $d->c2 = '';
            $d->c3 = '';
            $d->c4 = '';
            $d->c5 = '';
           $d->save();

            echo $d->id();



        }
        else{

            echo 'Group Name is required';

        }



        break;


    case 'find_by_group':

        $gid = route(2);

        if($gid){

            $g = ORM::for_table('crm_groups')->find_one($gid);

            if($g){

                $d = ORM::for_table('crm_accounts')->where('gid',$gid)->order_by_desc('id')->find_array();

                $ui->assign('d',$d);
                $ui->assign('gid',$gid);

                $ui->assign('xjq',' $(".cdelete").click(function (e) {
        e.preventDefault();
        var id = this.id;
        bootbox.confirm("'.$_L['are_you_sure'].'", function(result) {
           if(result){
               var _url = $("#_url").val();
               window.location.href = _url + "delete/crm-user/" + id + "/'.$gid.'/";
           }
        });
    });
');

                $ui->display('contacts_find_by_group.tpl');


            }

        }




        break;

    case 'group_edit':


        $id = _post('id');
        $id = str_replace('e','',$id);
        $gname = _post('gname');

        $d = ORM::for_table('crm_groups')->find_one($id);

        if($d){

           /*  // update all gname in contacts */

            $o_gname = $d->gname;

            ORM::execute("update crm_accounts set gname='$gname' where gname='$o_gname'");

            $d->gname = $gname;

            $d->save();

            echo $d->id;



        }





        break;

    case 'group_email':

        $gid = route(2);

        if($gid){

            /* // find group */


            $ds = ORM::for_table('crm_accounts')->where('gid',$gid)->where_not_equal('email','')->select('account')->select('email')->order_by_desc('id')->find_array();

            $ui->assign('ds',$ds);

            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','sn/summernote','sn/summernote-bs3','sn/summernote-application')));




            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'sn/summernote.min','contacts/group_email')));
            $ui->display('contacts_group_email.tpl');

        }


        break;


    case 'group_email_post':


/* //        $recipients = array(
//            'person1@domain.com' => 'Person One',
//            'person2@domain.com' => 'Person Two',
//            // ..
//        );
//        foreach($recipients as $email => $name)
//        {
//            $mail->AddAddress($email, $name);
//        } */



        $emails = $_POST['emails'];
        $subject = $_POST['subject'];
        $msg = $_POST['msg'];


        Ib_Email::bulk_email($emails,$subject,$msg,$user->username);

        echo 'Mail Sent!';


/* //       if(Ib_Email::bulk_email($emails,$subject,$msg,$user->username)){
//
//           echo 'Mail Sent!';
//
//       }
//
//        else{
//
//            echo 'An Error Occurred while sending email.';
//
//        } */




        break;


    case 'companies':

        $ui->assign('jsvar', '
_L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';
 ');

        $ui->assign('_application_menu', 'companies');
        $ui->assign('_st', $_L['Companies']);

/* // find all companies */

        $companies = Model::factory('Models_Company')->find_array();

        $ui->assign('xheader',Asset::css(array('modal')));
        $ui->assign('xfooter',Asset::js(array('modal','contacts/companies')));

        $ui->assign('companies',$companies);


        $ui->display('companies.tpl');


        break;

    case 'modal_add_company':

        $id = route(2);


        $company = false;

        if($id != ''){

            $id = str_replace('ae','',$id);
            $id = str_replace('be','',$id);

            $company = Model::factory('Models_Company')->find_one($id);

        }

        $val = array();

        if($company){
            $f_type = 'edit';
            $val['company_name'] = $company->company_name;
            $val['url'] = $company->url;
            $val['email'] = $company->email;
            $val['phone'] = $company->phone;
            $val['logo_url'] = $company->logo_url;
            $val['cid'] = $id;

/* //            $val[''] = $company->; */
        }
        else{
            $f_type = 'create';
            $val['company_name'] = '';
            $val['url'] = 'http://';
            $val['email'] = '';
            $val['phone'] = '';
            $val['logo_url'] = '';
/* //            $val[''] = ''; */
        }

        $ui->assign('f_type',$f_type);
        $ui->assign('val',$val);


        $ui->display('modal_add_company.tpl');

        break;

    case 'add_company_post':


        $data = ib_posted_data();

        if($data['f_type'] == 'edit'){

            $company = Model::factory('Models_Company')->find_one($data['cid']);

            if(!$company){

                i_close('Company Not Found');

            }

        }
        else{

            $company = Model::factory('Models_Company')->create();

        }

        if($data['company_name'] == ''){
            i_close($_L['Company Name is required']);
        }

        if(($data['email'] != '') && (!Validator::Email($data['email']))){
            i_close($_L['Invalid Email']);
        }

        if($data['url'] == 'http'){
            $data['url'] = '';
        }

        $company->company_name = $data['company_name'];
        $company->url = $data['url'];
        $company->email = $data['email'];
        $company->phone = $data['phone'];
        $company->logo_url = $data['logo_url'];
        $company->save();

        echo $company->id();
				
        break;

    default:
        echo 'action not defined';
}