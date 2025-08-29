<?php
$date = date('Y-m-d');
	 $duedate = ORM::for_table('sys_performa')->where_lt('duedate', $date)->where_not_equal('status', 'paid')->find_array();

		$subject ='Reminder about payment';
		$message ='';
		foreach($duedate as $due){

			if(strtotime($due['duedate']) < strtotime($date)){	

			$toname = $due['account'];
			$email = get_type_by_id('crm_accounts', 'id', $due['userid'], 'email');
			$phone = get_type_by_id('crm_accounts', 'id', $due['userid'], 'phone');
			$cid = $due['userid'];
			$iid = $due['id'];
			$msg = Invoice::gen_proforma_email($iid,'overdue');
			$message = $msg['body'];
			$in = $due['invoicenum'];
			$cc = '';
			$bcc = '';
			Invoice::proforma_pdf($iid,'store');
			$attachment_path = 'application/storage/temp/Proforma_'.$in.'.pdf';
			$attachment_file = 'Proforma_'.$in.'.pdf';
			$company = get_type_by_id('sys_accounts', 'id', $due['company_id'], 'account');
			$price = $due['subtotal']-$due['credit'];

			Notify_Email::_send($toname, 'sabir.makent@gmail.com', $subject, $message, $cid, $iid, $cc, $bcc, $attachment_path, $attachment_file); 
			$sms = 'Your Payment is pending for invoice id '.$iid.' to Mak Enterprises.';
			/* sendSMS($phone, $sms); */
 ?>