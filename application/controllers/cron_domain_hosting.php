<?php 
$date = date('Y-m-d');
	  
	 
	 $duedate = ORM::for_table('crm_domain_n_hosting')->where_any_is(array(
																												array('d_expiry_date' => $date),
																												array('h_expiry_date' => $date)), '<')
																										->find_array();

	 if(count($duedate)!=0)	{	 
	 foreach($duedate as $due){		 
				$d_days_30 = date($due['d_expiry_date'], strtotime('-30 days'));
			$d_days_15 = date($due['d_expiry_date'], strtotime('-15 days'));
			$d_days_7 = date($due['d_expiry_date'], strtotime('-7 days'));
			$d_days_3 = date($due['d_expiry_date'], strtotime('-3 days'));
			$h_days_30 = date($due['h_expiry_date'], strtotime('-30 days'));
			$h_days_15 = date($due['h_expiry_date'], strtotime('-15 days'));
			$h_days_7 = date($due['h_expiry_date'], strtotime('-7 days'));
			$h_days_3 = date($due['h_expiry_date'], strtotime('-3 days'));
			if($d_days_30 == $date || $d_days_15 == $date || $d_days_7 == $date || $d_days_3 == $date || $h_days_30 == $date || $h_days_15 == $date || $h_days_7 == $date || $h_days_3 == $date || $due['d_expiry_date'] == $date || $due['h_expiry_date'] == $date){
		 $subject ='Reminder about payment';
		 $proforma = ORM::for_table('sys_performa')->where_lt('duedate', $date)->where('userid', $due['account'])->find_one();
		 $toname = get_type_by_id('crm_accounts', 'id', $due['account'], 'company');
		 $email = get_type_by_id('crm_accounts', 'id', $due['account'], 'email');
		 $phone = get_type_by_id('crm_accounts', 'id', $due['account'], 'phone');
		 $cid = $due['account'];
			$iid = $proforma['id'];			
			if($d_days_30 == $date || $d_days_15 == $date || $d_days_7 == $date || $d_days_3 == $date || $due['d_expiry_date'] == $date){
				$msg = Invoice::gen_proforma_email($iid,'domain_renewal', $due['domain_name'], $due['d_expiry_date']);
			}
			
			if($h_days_30 == $date || $h_days_15 == $date || $h_days_7 == $date || $h_days_3 == $date || $due['h_expiry_date'] == $date){
				$msg = Invoice::gen_proforma_email($iid,'hosting_renewal', $due['domain_name'], $due['h_expiry_date']);
			}
			if($h_days_30 == $date && $d_days_30 == $date || $h_days_15 == $date && $d_days_15 == $date || $h_days_7 == $date && $d_days_7 == $date || $h_days_3 == $date && $d_days_3 == $date || $due['d_expiry_date'] == $date && $due['h_expiry_date'] == $date){
				$msg = Invoice::gen_proforma_email($iid,'domain_hosting_renewal', $due['domain_name'], $due['h_expiry_date']);
			}
			

		$in = $proforma['invoicenum'];
		$cc = '';
		$bcc = 'tamirkhan@makent.in';
		Invoice::proforma_pdf($iid,'store');
    $attachment_path = 'application/storage/temp/Proforma_'.$in.'.pdf';
		$attachment_file = 'Proforma_'.$in.'.pdf';
		 $company = get_type_by_id('sys_accounts', 'id', $proforma['company_id'], 'account');
		 $price = $proforma['subtotal']-$proforma['credit'];
		 $user_email = get_type_by_id('crm_accounts', 'id', $due['userid'], 'email');
 Notify_Email::_send($toname, $user_email, $subject, $msg['body'], $cid, $iid, $cc, $bcc, $attachment_path, $attachment_file); 
 sendSMS($phone, $msg['body']); 
 sendSMS(9029075525, $msg['body']); 
	 }
	 }
	 }
	 ?>