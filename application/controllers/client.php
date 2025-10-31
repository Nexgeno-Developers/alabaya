<?php

$ui->assign('_application_menu', 'invoices');
$ui->assign('_st', 'Invoices');
$ui->assign('_title', $config['CompanyName']);

if(isset($routes[1]) && ($routes[1] != '')){
    $action = $routes[1];
}
else{
    $action = 'login';
}



$ui->assign('tplheader', 'sections/client_header');
$ui->assign('tplfooter', 'sections/client_footer');


Event::trigger('client',array($action));


switch ($action) {

    case 'iview-tailor':

        Event::trigger('client/iview/');

        $xfooter = Asset::js(array('numeric'));

        $id  = $routes['2'];
				$_SESSION['id'] = $id; 
				$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
				$ui->assign('txnid', $txnid);
        $d = ORM::for_table('sys_invoices')->find_one($id);
        if($d){
            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }

						$comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);      
						$ui->assign('comp', $comp);
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid',$id)->order_by_asc('id')->find_many();
						foreach ($items as &$row){
							$t = ORM::for_table('sys_tax')->select(array('taxtype'))->find_one($row['tax_id']);
							$row['taxtype'] = $t['taxtype'];
						}
            $ui->assign('items',$items);
            //find related transactions
            $trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();

            $trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
            $ui->assign('trs', $trs);
            $ui->assign('trs_c', $trs_c);
//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $ui->assign('a',$a);
            $ui->assign('d',$d);

            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];
            if($d['credit'] != '0.00'){
                $i_due = $i_total - $i_credit;
            }
            else{
                $i_due =  $d['subtotal'];
            }

            $ui->assign('i_due', $i_due);
            $pgs = ORM::for_table('sys_pg')->where('status','Active')->order_by_asc('sorder')->find_many();
            $ui->assign('pgs',$pgs);
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();
            $ui->assign('cf',$cf);

            $x_html = '';

            Event::trigger('view_invoice');

            $ui->assign('xfooter', $xfooter);

            $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });');


            $ui->assign('x_html',$x_html);

            $ui->display('client-iview-tailor.tpl');

        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;    

    case 'iview':

        Event::trigger('client/iview/');

        $xfooter = Asset::js(array('numeric'));

        $id  = $routes['2'];
				$_SESSION['id'] = $id; 
				$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
				$ui->assign('txnid', $txnid);
        $d = ORM::for_table('sys_invoices')->find_one($id);
        if($d){
            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }

						$comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);      
						$ui->assign('comp', $comp);
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid',$id)->order_by_asc('id')->find_many();
						foreach ($items as &$row){
							$t = ORM::for_table('sys_tax')->select(array('taxtype'))->find_one($row['tax_id']);
							$row['taxtype'] = $t['taxtype'];
						}
            $ui->assign('items',$items);
            //find related transactions
            $trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();

            $trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
            $ui->assign('trs', $trs);
            $ui->assign('trs_c', $trs_c);
//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $ui->assign('a',$a);
            $ui->assign('d',$d);

            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];
            if($d['credit'] != '0.00'){
                $i_due = $i_total - $i_credit;
            }
            else{
                $i_due =  $d['subtotal'];
            }

            $ui->assign('i_due', $i_due);
            $pgs = ORM::for_table('sys_pg')->where('status','Active')->order_by_asc('sorder')->find_many();
            $ui->assign('pgs',$pgs);
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();
            $ui->assign('cf',$cf);

            $x_html = '';

            Event::trigger('view_invoice');

            $ui->assign('xfooter', $xfooter);

            $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });');


            $ui->assign('x_html',$x_html);

            $ui->display('client-iview.tpl');

        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;

    case 'proforma-iview':

        Event::trigger('client/proforma-iview/');

        $xfooter = Asset::js(array('numeric'));

        $id  = $routes['2'];
				$_SESSION['id'] = $id; 
				$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
				$ui->assign('txnid', $txnid);
        $d = ORM::for_table('sys_performa')->find_one($id);
        if($d){
            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }

						$comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);      
						$ui->assign('comp', $comp);
            $items = ORM::for_table('sys_performaitems')->where('invoiceid',$id)->order_by_asc('id')->find_many();
						foreach ($items as &$row){
							$t = ORM::for_table('sys_tax')->select(array('taxtype'))->find_one($row['tax_id']);
							$row['taxtype'] = $t['taxtype'];
						}
            $ui->assign('items',$items);
            //find related transactions
            $trs_c = ORM::for_table('sys_proforma_transactions')->where('iid', $id)->count();

            $trs = ORM::for_table('sys_proforma_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
            $ui->assign('trs', $trs);
            $ui->assign('trs_c', $trs_c);
//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $ui->assign('a',$a);
            $ui->assign('d',$d);

            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];
            if($d['credit'] != '0.00'){
                $i_due = $i_total - $i_credit;
            }
            else{
                $i_due =  $d['subtotal'];
            }

            $ui->assign('i_due', $i_due);
            $pgs = ORM::for_table('sys_pg')->where('status','Active')->order_by_asc('sorder')->find_many();
            $ui->assign('pgs',$pgs);
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();
            $ui->assign('cf',$cf);

            $x_html = '';

            Event::trigger('view_invoice');

            $ui->assign('xfooter', $xfooter);

            $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });');


            $ui->assign('x_html',$x_html);

            $ui->display('proforma-iview.tpl');

        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;


    case 'q':

        Event::trigger('client/q/');

        $id  = $routes['2'];
        $d = ORM::for_table('sys_quotes')->find_one($id);
        if($d){
            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }


            $items = ORM::for_table('sys_quoteitems')->where('qid',$id)->order_by_asc('id')->find_many();
            $ui->assign('items',$items);

            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $ui->assign('a',$a);
            $ui->assign('d',$d);

            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();
            $ui->assign('cf',$cf);

            $x_html = '';




            $ui->assign('x_html',$x_html);

            $ui->display('client-quote.tpl');

        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;




    case 'iprint':

        Event::trigger('client/iprint/');

        $id  = $routes['2'];
        $d = ORM::for_table('sys_invoices')->find_one($id);
        if($d){

            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }

            //find all activity for this user
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid',$id)->order_by_asc('id')->find_many();
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
            require 'application/lib/invoices/render.php';

        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;

    case 'ipdf':

        Event::trigger('client/ipdf/');

        $id  = $routes['2'];
				$app_config = ORM::for_table('sys_appconfig')->find_one('85'); 
				
				$ui->assign('app_config', $app_config);				
        $d = ORM::for_table('sys_invoices')->find_one($id);            
        if($d){
            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
						$comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);      
      $ui->assign('comp', $comp);
      $ui->assign('id', $id);
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }
            //find all activity for this user
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid',$id)->order_by_asc('id')->find_many();
						foreach ($items as &$row){
							$t = ORM::for_table('sys_tax')->select(array('taxtype'))->find_one($row['tax_id']);
							$row['taxtype'] = $t['taxtype'];
						}
	
            $trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();

            $trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
						
						//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];

            if($d['credit'] != '0.00'){
                $i_due = $i_total-$i_credit;
            }
            else{
                $i_due = $i_total;
            }

					 /* get bank details */
					
           /* $i_due = number_format($i_due,2,$config['dec_point'],$config['thousands_sep']); */
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();

            define('_MPDF_PATH','application/lib/mpdf/');

            require('application/lib/mpdf/mpdf.php');

            $pdf_c = '';
            $ib_w_font = 'dejavusanscondensed';
            if($config['pdf_font'] == 'default'){
                $pdf_c = 'c';
                $ib_w_font = 'Helvetica';
            }
            elseif($config['pdf_font'] == 'default'){
                $ib_w_font = 'Helvetica';
            }
            else{

            }

            $mpdf=new mPDF($pdf_c,'A4','','',5,5,2,5,10,10);
         /*  $mpdf->SetProtection(array('print')); */
            $mpdf->SetTitle($config['CompanyName'].$_L['Invoice']);
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

            $pdf_tpl = 'application/lib/invoices/pdf-x2.php';
							
            Event::trigger('invoices/before_pdf_render/',array($id));

            ob_start();

            require $pdf_tpl;

            $html = ob_get_contents();

            ob_end_clean();

            $mpdf->WriteHTML($html);

            if (isset($routes['4']) AND ($routes['4'] == 'dl')) {
                $mpdf->Output($d['invoicenum']. '.pdf', 'D'); # D
            } else {
                $mpdf->Output($d['invoicenum']. '.pdf', 'I'); # D
            }
        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break; 


        case 'ipdf-tailor':

        Event::trigger('client/ipdf-tailor/');

        $id  = $routes['2'];
				$app_config = ORM::for_table('sys_appconfig')->find_one('85'); 
				
				$ui->assign('app_config', $app_config);				
        $d = ORM::for_table('sys_invoices')->find_one($id);            
        if($d){
            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
						$comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);      
      $ui->assign('comp', $comp);
      $ui->assign('id', $id);
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }
            //find all activity for this user
            $items = ORM::for_table('sys_invoiceitems')->where('invoiceid',$id)->order_by_asc('id')->find_many();
						foreach ($items as &$row){
							$t = ORM::for_table('sys_tax')->select(array('taxtype'))->find_one($row['tax_id']);
							$row['taxtype'] = $t['taxtype'];
						}
	
            $trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();

            $trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
						
						//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];

            if($d['credit'] != '0.00'){
                $i_due = $i_total-$i_credit;
            }
            else{
                $i_due = $i_total;
            }

					 /* get bank details */
					
           /* $i_due = number_format($i_due,2,$config['dec_point'],$config['thousands_sep']); */
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();

            define('_MPDF_PATH','application/lib/mpdf/');

            require('application/lib/mpdf/mpdf.php');

            $pdf_c = '';
            $ib_w_font = 'dejavusanscondensed';
            if($config['pdf_font'] == 'default'){
                $pdf_c = 'c';
                $ib_w_font = 'Helvetica';
            }
            elseif($config['pdf_font'] == 'default'){
                $ib_w_font = 'Helvetica';
            }
            else{

            }

            $mpdf=new mPDF($pdf_c,'A4','','',5,5,2,5,10,10);
         /*  $mpdf->SetProtection(array('print')); */
            $mpdf->SetTitle($config['CompanyName'].$_L['Invoice']);
            $mpdf->SetAuthor($config['CompanyName']);
            $mpdf->SetWatermarkText('');//ib_lan_get_line($d['status'])
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = $ib_w_font;

            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            if($config['pdf_font'] == 'AdobeCJK'){
                $mpdf->useAdobeCJK = true;
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;
            }

            $pdf_tpl = 'application/lib/invoices/pdf-x2-tailor.php';
							
            Event::trigger('invoices/before_pdf_render/',array($id));

            ob_start();

            require $pdf_tpl;

            $html = ob_get_contents();

            ob_end_clean();

            $mpdf->WriteHTML($html);

            if (isset($routes['4']) AND ($routes['4'] == 'dl')) {
                $mpdf->Output($d['invoicenum']. '.pdf', 'D'); # D
            } else {
                $mpdf->Output($d['invoicenum']. '.pdf', 'I'); # D
            }
        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;        

				case 'performapdf':

        Event::trigger('client/performapdf/');

        $id  = $routes['2'];
				$app_config = ORM::for_table('sys_appconfig')->find_one('85'); 
				
				$ui->assign('app_config', $app_config);				
        $d = ORM::for_table('sys_performa')->find_one($id);            
        if($d){
            $token = $routes['3'];
            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
						$comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);      
      $ui->assign('comp', $comp);
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }
            //find all activity for this user
            $items = ORM::for_table('sys_performaitems')->where('invoiceid',$id)->order_by_asc('id')->find_many();
						foreach ($items as &$row){
							$t = ORM::for_table('sys_tax')->select(array('taxtype'))->find_one($row['tax_id']);
							$row['taxtype'] = $t['taxtype'];
						}
	
            $trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();

            $trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
						
						//find the user
            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['subtotal'];

            if($d['credit'] != '0.00'){
                $i_due = $i_total-$i_credit;
            }
            else{
                $i_due = $i_total;
            }

					 /* get bank details */
					
           /* $i_due = number_format($i_due,2,$config['dec_point'],$config['thousands_sep']); */
            $cf = ORM::for_table('crm_customfields')->where('showinvoice','Yes')->order_by_asc('id')->find_many();

            define('_MPDF_PATH','application/lib/mpdf/');

            require('application/lib/mpdf/mpdf.php');

            $pdf_c = '';
            $ib_w_font = 'dejavusanscondensed';
            if($config['pdf_font'] == 'default'){
                $pdf_c = 'c';
                $ib_w_font = 'Helvetica';
            }
            elseif($config['pdf_font'] == 'default'){
                $ib_w_font = 'Helvetica';
            }
            else{

            }

            $mpdf=new mPDF($pdf_c,'A4','','',5,5,2,5,10,10);
         /*  $mpdf->SetProtection(array('print')); */
            $mpdf->SetTitle($config['CompanyName'].$_L['Invoice']);
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

            $pdf_tpl = 'application/lib/invoices/performapdf.php';
							
            Event::trigger('invoices/before_pdf_render/',array($id));

            ob_start();

            require $pdf_tpl;

            $html = ob_get_contents();

            ob_end_clean();

            $mpdf->WriteHTML($html);

            if (isset($routes['4']) AND ($routes['4'] == 'dl')) {
                $mpdf->Output($d['invoicenum']. '.pdf', 'D'); # D
            } else {
                $mpdf->Output($d['invoicenum']. '.pdf', 'I'); # D
            }
        }
        else{
            r2(U . 'customers/list', 'e', $_L['Account_Not_Found']);
        }

        break;

    case 'qpdf':

        Event::trigger('client/qpdf/');


        $id  = $routes['2'];

        $d = ORM::for_table('sys_quotes')->find_one($id);
        if ($d) {

            //find all activity for this user
            $items = ORM::for_table('sys_quoteitems')->where('qid', $id)->order_by_asc('id')->find_many();


            $a = ORM::for_table('crm_accounts')->find_one($d['userid']);



            $cf = ORM::for_table('crm_customfields')->where('showinvoice', 'Yes')->order_by_asc('id')->find_many();


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
            $mpdf->SetWatermarkText($d['status']);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = $ib_w_font;
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            if($config['pdf_font'] == 'AdobeCJK'){
                $mpdf->useAdobeCJK = true;
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;
            }

            ob_start();

            require 'application/lib/invoices/q-x2.php';

            $html = ob_get_contents();


            ob_end_clean();

            $mpdf->WriteHTML($html);

            if (isset($routes[4]) AND ($routes[4] == 'dl')) {
                $mpdf->Output(date('Y-m-d') . _raid(4) . '.pdf', 'D'); # D
            } else {
                $mpdf->Output(date('Y-m-d') . _raid(4) . '.pdf', 'I'); # D
            }
            // $mpdf->Output();



        }
        break;

    case 'ipay':

        Event::trigger('client/ipay/');


        $id  = $routes[2];
        $token = $routes[3];
        $pg = _post('pg');

        Event::trigger('client/ipay/pg',array($pg,$id,$token));

        $d = ORM::for_table('sys_invoices')->find_one($id);

        if($d){

            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }

            //check pg
            $ui->assign('d',$d);


            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['total'];


            $amount = $i_total-$i_credit;
            $invoiceid = $d['id'];
            $vtoken = $d['vtoken'];
            $ptoken = $d['ptoken'];

            //get user details

            $u = ORM::for_table('crm_accounts')->find_one($d['userid']);




            switch ($pg){

                case 'paypal':
                    $p = ORM::for_table('sys_pg')->where('processor', 'paypal')->find_one();

                    if($p){

                        // get currency

                        $currency_id = $d['currency'];

                        $currency_find = Model::factory('Models_Currency')->find_one($currency_id);

                        if($currency_find){

                            $currency = $currency_id;
                            $currency_code = $currency_find->cname;
                            $currency_rate = $currency_find->rate;


                        }
                        else{

                            $currency = 0;
                            $currency_code = $p['c1'];
                            $currency_rate = 1.0000;

                        }

                        $ppemail = $p['value'];
//

                        $c2 = $p['c2'];
                        if(($c2 != '') AND (is_numeric($c2)) AND($c2 != '1')){
                            $amount = $amount/$c2;
                            $amount = round($amount,2);
                        }
?>
                       <?php
if($d['company_id']=='1'){
$MERCHANT_KEY = "ByhijwgB";
$SALT = "l6zxaPZp1G";
}
else{
$MERCHANT_KEY = "ByhijwgB";

$SALT = "zU8GPYX0tl";
}

// Merchant Key and Salt as provided by Payu.

//$PAYU_BASE_URL = "https://sandboxsecure.payu.in";		// For Sandbox Mode
$PAYU_BASE_URL = "https://secure.payu.in";			// For Production Mode

$action = '';

$posted = array();
if(!empty($_POST)) {
    //print_r($_POST);
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value; 
	
  }
}

$formError = 0;
if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|productinfo|amount|firstname|email|phone";

if(empty($posted['hash']) && sizeof($posted) > 0) {

  if(
          empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
  ) {
    $formError = 1;
	
  } else {
		
	
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
	
}
?>
<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()">
    <h2>PayU Form</h2>
    <br/>
    <?php if($formError) { ?>
	
      <span style="color:red">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php } ?>
    <form action="<?php echo $PAYU_BASE_URL . '/_payment'; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <input type="hidden" name="amount" value="<?php echo $posted['amount'] ?>" />
      <input type="hidden" name="firstname" value="<?php echo $posted['name'] ?>" />
      <input type="hidden" name="email" value="<?php echo $posted['email'] ?>" />
      <input type="hidden" name="phone" value="<?php echo $posted['phone'] ?>" />
      <input type="hidden" name="productinfo" value="<?php echo $posted['productinfo'] ?>" />
      <table>
			 <tr>
          <td>Success URI: </td>
          <td colspan="3"><input name="surl" value="<?php echo (empty($posted['surl'])) ? '' : $posted['surl'] ?>" size="64" /></td>
        </tr>
        <tr>
          <td>Failure URI: </td>
          <td colspan="3"><input name="furl" value="<?php echo (empty($posted['furl'])) ? '' : $posted['furl'] ?>" size="64" /></td>
        </tr>
        <tr>
          <?php if(!$hash) { ?>
            <td colspan="4"><input type="submit" value="Submit" /></td>
          <?php } ?>
        </tr>
      </table>
    </form>
  </body>
</html>

<?php
                    }

                    else{
                        echo 'Paypal is Not Found!';
                    }


                    break;


                case 'manualpayment':

                    Event::trigger('client/manualpayment/');

                    $p = ORM::for_table('sys_pg')->where('processor', 'manualpayment')->find_one();

                    if($p){
                        $ui->assign('i_due', $amount);
                        $ui->assign('ins',$p['value']);
                        $ui->display('client-ipay.tpl');
                    }


                    break;

                case 'stripe':
                    $p = ORM::for_table('sys_pg')->where('processor', 'stripe')->find_one();

                    if($p){
                        $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
                        $it = $i_total - $i_credit;
                        $amount = $it*100;
                        $ins = ' <script
                                        src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
                                        data-key="'.$p['value'].'"
                                        data-amount="'.$amount.'"
                                        data-name="INV #'.$d['id'].'"
                                        data-email="'.$a['email'].'"
                                        data-currency="'.$p['c1'].'"
                                        data-description="Payment for Invoice # '.$d['id'].'">
                                </script>';

                        $ui->assign('ins',$ins);

                        $ui->display('stripe.tpl');
                    }


                    break;


                case 'stripe_post':
                    $p = ORM::for_table('sys_pg')->where('processor', 'stripe')->find_one();
                    if($p){
                        $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
                        $it = $i_total - $i_credit;
                        $amount = $it*100;
                        $currency_code = $p['c1'];

                        require_once('application/lib/stripe/init.php');


                        $description = "Payment For INV # $invoiceid";

                        $cardNumber = _post('cardNumber');

                        $cardExpiry = _post('cardExpiry');

                        $ce = explode('/',$cardExpiry);


                        $cardCVC = _post('cardCVC');

                        $myCard = array('number' => $cardNumber, 'exp_month' => $ce['0'], 'exp_year' => $ce['1']);


                        try {

                            \Stripe\Stripe::setApiKey($p['value']);
                            $charge = \Stripe\Charge::create(array('card' => $myCard, 'amount' => $amount, 'currency' => $currency_code,"description" => $description));


//                       $charge =  '  Stripe\Charge JSON: {
//    "id": "ch_16QJiYAN1GVPX6ZsbBl20gsJ",
//    "object": "charge",
//    "created": 1437319722,
//    "livemode": false,
//    "paid": true,
//    "status": "succeeded",
//    "amount": 193600,
//    "currency": "usd",
//    "refunded": false,
//    "source": {
//        "id": "card_16QJiYAN1GVPX6ZsDKidAMN7",
//        "object": "card",
//        "last4": "4242",
//        "brand": "Visa",
//        "funding": "credit",
//        "exp_month": 5,
//        "exp_year": 2016,
//        "fingerprint": "n0QKFME5XxL1IRG9",
//        "country": "US",
//        "name": null,
//        "address_line1": null,
//        "address_line2": null,
//        "address_city": null,
//        "address_state": null,
//        "address_zip": null,
//        "address_country": null,
//        "cvc_check": null,
//        "address_line1_check": null,
//        "address_zip_check": null,
//        "tokenization_method": null,
//        "dynamic_last4": null,
//        "metadata": [],
//        "customer": null
//    },
//    "captured": true,
//    "balance_transaction": "txn_16QJiYAN1GVPX6Zs24syLCZi",
//    "failure_message": null,
//    "failure_code": null,
//    "amount_refunded": 0,
//    "customer": null,
//    "invoice": null,
//    "description": null,
//    "dispute": null,
//    "metadata": [],
//    "statement_descriptor": null,
//    "fraud_details": [],
//    "receipt_email": null,
//    "receipt_number": null,
//    "shipping": null,
//    "destination": null,
//    "application_fee": null,
//    "refunds": {
//        "object": "list",
//        "total_count": 0,
//        "has_more": false,
//        "url": "\/v1\/charges\/ch_16QJiYAN1GVPX6ZsbBl20gsJ\/refunds",
//        "data": []
//    }
//}';



                            $charge = str_replace('Stripe\Charge JSON:','',$charge);
                           $resp = json_decode($charge,true);
                            $trid = $resp['id'];
                            $last4 = $resp['source']['last4'];
                          $captured = $resp['captured'];

                            if($captured == true){

                                $inv = ORM::for_table('sys_invoices')->find_one($id);
                                if($inv) {

                                    $inv->status = 'Paid';
                                    $inv->save();
                                    Event::trigger('invoices/markpaid/',$invoice=$inv);
                                    _msglog('s','Payment Successful');
                                    r2(U.'client/iview/'.$d['id'].'/'.'token_'.$d['vtoken']);
                                }

                            }

                            else{
                                _msglog('e','This API call cannot be made with a publishable API key. Please use a secret API key. You can find a list of your API keys at https://dashboard.stripe.com/account/apikeys.');
                                r2(U.'client/iview/'.$d['id'].'/'.'token_'.$d['vtoken']);
                            }



                        } catch(\Stripe\Error\Card $e) {
                            // Since it's a decline, \Stripe\Error\Card will be caught
                            $body = $e->getJsonBody();
                            $err  = $body['error'];

                            print('Status is:' . $e->getHttpStatus() . "\n");
                            print('Type is:' . $err['type'] . "\n");
                            print('Code is:' . $err['code'] . "\n");
                            // param is '' in this case
                            print('Param is:' . $err['param'] . "\n");
                            print('Message is:' . $err['message'] . "\n");
                        } catch (\Stripe\Error\InvalidRequest $e) {
                            // Invalid parameters were supplied to Stripe's API
                        } catch (\Stripe\Error\Authentication $e) {
                            // Authentication with Stripe's API failed
                            // (maybe you changed API keys recently)
                        } catch (\Stripe\Error\ApiConnection $e) {
                            // Network communication with Stripe failed
                        } catch (\Stripe\Error\Base $e) {
                            // Display a very generic error to the user, and maybe send
                            // yourself an email
                        } catch (Exception $e) {
                            // Something else happened, completely unrelated to Stripe
                        }

                    }

                    break;


                case 'authorize_net':

                    $p = ORM::for_table('sys_pg')->where('processor', 'authorize_net')->find_one();

                    if($p){

                        $invoiceid = $d['id'];
                        $amount = $i_total - $i_credit;
                        $url = 'https://secure.authorize.net/gateway/transact.dll';
                        $loginID = $p['value'];

                        $transactionKey = $p['c1'];

                        $description = "Invoice Payment - $invoiceid";

                        // an invoice is generated using the date and time
                        $invoice = $invoiceid;
// a sequence number is randomly generated
                        $sequence = rand(1, 1000);
// a timestamp is generated
                        $timeStamp = time();

                        $testMode = "false";
                        if (phpversion() >= '5.1.2') {
                            $fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey);
                        } else {
                            $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey));
                        }
                        $params = array(
                            array('name' => "x_login",
                                'value' => $loginID
                            ),
                            array('name' => "x_amount",
                                'value' => $amount
                            ),
                            array('name' => "x_description",
                                'value' => $description
                            ),
                            array('name' => "x_invoice_num",
                                'value' => $invoice
                            ),
                            array('name' => "x_fp_sequence",
                                'value' => $sequence
                            ),
                            array('name' => "x_fp_timestamp",
                                'value' => $timeStamp
                            ),
                            array('name' => "x_fp_hash",
                                'value' => $fingerprint
                            ),
                            array('name' => "x_test_request",
                                'value' => $testMode
                            ),
                            array('name' => "x_show_form",
                                'value' => "PAYMENT_FORM"
                            )
                        );

                        Fsubmit::form($url, $params);
                    }


                    break;


                case 'ccavenue':

                    $p = ORM::for_table('sys_pg')->where('processor', 'ccavenue')->find_one();

                    if($p){

                        require ('application/lib/misc/ccavenue.php');

                        $currency_code = $p['c2'];
                        $c3 = $p['c3'];

                        if(($c3 != '') AND (is_numeric($c3)) AND($c3 != '1')){
                            $amount = $amount/$c3;
                        }

                        $Merchant_Id = $p['value']; //Given to merchant by ccavenue


                        $WorkingKey = $p['c1']; //Given to merchant by ccavenue

                        $redirect_url = U . "client/ipay_ipn/$invoiceid/token_$ptoken/";


                        require ('application/lib/misc/ccform.php');


                        // Updated Jan 10, 2016

//                        $Checksum = getCheckSum($Merchant_Id,$amount,$invoiceid ,$redirect_url,$WorkingKey);
//
//                        $url = 'https://www.ccavenue.com/shopzone/cc_details.jsp';
//
//
//
//
//                        $params = array(
//
//                            array('name' => "merchant_id",
//                                'value' => $Merchant_Id
//                            ),
//
//                            array('name' => "Redirect_Url",
//                                'value' => $redirect_url
//                            ),
//
//                            array('name' => "amount",
//                                'value' => $amount
//                            ),
//                            array('name' => "order_id",
//                                'value' => $invoiceid
//                            ),
//                            array('name' => "Checksum",
//                                'value' => $Checksum
//                            ),
//                            array('name' => "upload",
//                                'value' => '1'
//                            ),
//                            array('name' => "ActionID",
//                                'value' => 'TXN'
//                            ),
//                            array('name' => "TxnType",
//                                'value' => 'A'
//                            ),
//                            array('name' => "num_cart_items",
//                                'value' => '1'
//                            ),
//                            array('name' => "rm",
//                                'value' => '2'
//                            ),
//                            array('name' => "payment_gross",
//                                'value' => $amount
//                            ),
//                            array('name' => "TxnType",
//                                'value' => 'A'
//                            ),
//                            array('name' => "payment_gross",
//                                'value' => $amount
//                            ),
//                            array('name' => "currency",
//                                'value' => $currency_code
//                            ),
//                            array('name' => "billing_name",
//                                'value' =>$u['account']
//                            ),
//                            array('name' => "billing_address",
//                                'value' =>$u['address']
//                            ),
//                            array('name' => "billing_city",
//                                'value' =>$u['city']
//                            ),
//                            array('name' => "billing_state",
//                                'value' =>$u['state']
//                            ),
//                            array('name' => "billing_zip",
//                                'value' =>$u['zip']
//                            ),
//                            array('name' => "billing_country",
//                                'value' =>'India'
//                            ),
//                            array('name' => "billing_tel",
//                                'value' =>$u['phone']
//                            ),
//                            array('name' => "billing_email",
//                                'value' =>$u['email']
//                            ),
//                            array('name' => "delivery_name",
//                                'value' =>$u['account']
//                            ),
//                            array('name' => "delivery_address",
//                                'value' =>$u['address']
//                            ),
//                            array('name' => "delivery_city",
//                                'value' =>$u['city']
//                            ),
//                            array('name' => "delivery_state",
//                                'value' =>$u['state']
//                            ),
//                            array('name' => "delivery_state",
//                                'value' =>$u['state']
//                            ),
//                            array('name' => "delivery_zip",
//                                'value' =>$u['zip']
//                            ),
//                            array('name' => "delivery_country",
//                                'value' =>$u['country']
//                            ),
//                            array('name' => "delivery_tel",
//                                'value' =>$u['phone']
//                            ),
//                            array('name' => "merchant_param1",
//                                'value' =>''
//                            )
//
//                        );
//
//
//                        Fsubmit::form($url, $params);

                    }



                    break;


                case 'braintree':

                    $p = ORM::for_table('sys_pg')->where('processor', 'braintree')->find_one();
                    Braintree_Configuration::environment($p['c4']);
                    Braintree_Configuration::merchantId($p['value']);
                    Braintree_Configuration::publicKey($p['c1']);
                    Braintree_Configuration::privateKey($p['c2']);

                    if($p){
                        $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
                        $it = $i_total - $i_credit;
                        $amount = $it*100;
                        $clientToken = Braintree_ClientToken::generate(array());
                        $formurl = U . "client/btpay_submitted/$invoiceid/token_$vtoken/";
                        $vamount =  $config['currency_code']. number_format($d['total'],2,$config['dec_point'],$config['thousands_sep']);
                        $ins = '
                      <form id="checkout" method="post" action="'.$formurl.'">
  <div id="payment-form"></div>
  <input type="submit" value="Pay '.$config['currency_code'].' '.$vamount .'">
</form>
                      <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
                      <script>
									var clientToken = "'.$clientToken.'";
									braintree.setup(clientToken, "dropin", {
  									container: "payment-form"
									});
								</script>';
                        $ui->assign('ins',$ins);
                        $ui->display('client-ipay.tpl');
                    }
                    break;



                case 'quickpay':

                    $p = ORM::for_table('sys_pg')->where('processor', 'quickpay')->find_one();

                    if($p){

                        require 'application/lib/misc/quickpay.php';

                        $qp = new Quickpay($p['value'], $p['c1']);

                        $data_fields['msgtype'] = 'authorize';
                        $data_fields['language'] = 'en';
                        $data_fields['ordernumber'] = $invoiceid;
                        $data_fields['amount'] = $amount;
                        $data_fields['currency'] = $p['c3'];
                        $data_fields['continueurl'] = U . "client/ipay_submitted/$invoiceid/token_$vtoken/";
                        $data_fields['cancelurl'] = U . "client/ipay_cancel/$invoiceid/token_$vtoken/";
                        $data_fields['callbackurl'] = U . "client/ipay_ipn/$invoiceid/token_$ptoken/";

//                   echo '
//
//<form action="https://secure.quickpay.dk/form/" method="post">
//
//'.$qp->form_fields($data_fields).'
//
//<input type="submit" value="Open Quickpay payment window" />
//
//</form>
//
//
//';

                        Fsubmit::input('https://secure.quickpay.dk/form/', $qp->form_fields($data_fields));


                    }





                    break;

								





				default: 
					/* 	$MERCHANT_KEY = "rjQUPktU";
					$SALT = "e5iIg1jwi8"; 
					$PAYU_BASE_URL = "https://test.payu.in";*/
					
					if($d['company_id']=='1'){
					$MERCHANT_KEY = "ByhijwgB";
					$SALT = "l6zxaPZp1G";
					}
					else{
					$MERCHANT_KEY = "ByhijwgB";
/* var_dump($MERCHANT_KEY);exit; */
					$SALT = "l6zxaPZp1G";
					}	
				//$PAYU_BASE_URL = "https://sandboxsecure.payu.in";		// For Sandbox Mode
						$PAYU_BASE_URL = "https://secure.payu.in";			// For Production Mode
					$action = '';
					$posted = array();

					$posted['hash']='';
					if(!empty($_POST)) {
						foreach($_POST as $key => $value) {    
							$posted[$key] = $value; 
						}
					}
					
					$formError = 0;
					if(empty($posted['txnid'])) {
						
						$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
					} else {
						$txnid = $posted['txnid'];
					}
					$hash = '';
					// Hash Sequence
					$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
		
					if(empty($posted['hash']) && sizeof($posted) > 0) {
						if(empty($posted['amount']) || empty($posted['firstname']) || empty($posted['email']) || empty($posted['phone']) || empty($posted['productinfo']) || empty($posted['surl']) || empty($posted['furl'])) {
							$formError = 1;
						} else {
							
							$hashVarsSeq = explode('|', $hashSequence);
						
							$hash_string = '';	 
							foreach($hashVarsSeq as $hash_var) {
								$hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
									
								$hash_string .= '|';
								
							}
						
							$hash_string .= $SALT;
							$hash = strtolower(hash('sha512', $hash_string));
							$action = $PAYU_BASE_URL . '/_payment';
						}
					} elseif(!empty($posted['hash'])) {
						$hash = $posted['hash'];
						$action = $PAYU_BASE_URL . '/_payment';
					}
					// exit;
?>
<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()" style="display:none;">
    <h2>PayU Form</h2>
    <br/>
    <?php if($formError) { ?>
		
      <span style="color:red">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <table>
        <tr>
          <td><b>Mandatory Parameters</b></td>
        </tr>
        <tr>
          <td>Amount: </td>
          <td><input name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
          <td>First Name: </td>
          <td><input name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></td>
        </tr>
        <tr>
          <td>Email: </td>
          <td><input name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
          <td>Phone: </td>
          <td><input name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
        </tr>
        <tr>
          <td>Product Info: </td>
          <td colspan="3"><textarea name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
        </tr>
        <tr>
          <td>Success URI: </td>
          <td colspan="3"><input name="surl" value="<?php echo (empty($posted['surl'])) ? '' : $posted['surl'] ?>" size="64" /></td>
        </tr>
        <tr>
          <td>Failure URI: </td>
          <td colspan="3"><input name="furl" value="<?php echo (empty($posted['furl'])) ? '' : $posted['furl'] ?>" size="64" /></td>
        </tr>

        <tr>
          <td colspan="3"><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
        </tr>

      
        <tr>
            <td colspan="4"><input type="submit" value="Submit" /></td>
        </tr>
      </table>
    </form>
  </body>
</html>
<?php
                    /* echo 'Payment Gateway Not Found!'; */

            }

        }
        else{
            echo 'Sorry Invoice Not Found!';
            exit;
        }

        break;

    /*
     * CCAvenue
     *
     *
     */

    case 'proformaipay':

        Event::trigger('client/proformaipay/');


        $id  = $routes[2];
        $token = $routes[3];
        $pg = _post('pg');

        Event::trigger('client/proformaipay/pg',array($pg,$id,$token));

        $d = ORM::for_table('sys_performa')->find_one($id);

        if($d){

            $token = str_replace('token_','',$token);
            $vtoken = $d['vtoken'];
            if($token != $vtoken){
                echo 'Sorry Token does not match!';
                exit;
            }

            //check pg
            $ui->assign('d',$d);


            $i_credit = $d['credit'];
            $i_due = '0.00';
            $i_total = $d['total'];


            $amount = $i_total-$i_credit;
            $invoiceid = $d['id'];
            $vtoken = $d['vtoken'];
            $ptoken = $d['ptoken'];

            //get user details

            $u = ORM::for_table('crm_accounts')->find_one($d['userid']);




            switch ($pg){

                case 'paypal':
                    $p = ORM::for_table('sys_pg')->where('processor', 'paypal')->find_one();

                    if($p){

                        // get currency

                        $currency_id = $d['currency'];

                        $currency_find = Model::factory('Models_Currency')->find_one($currency_id);

                        if($currency_find){

                            $currency = $currency_id;
                            $currency_code = $currency_find->cname;
                            $currency_rate = $currency_find->rate;


                        }
                        else{

                            $currency = 0;
                            $currency_code = $p['c1'];
                            $currency_rate = 1.0000;

                        }

                        $ppemail = $p['value'];
//

                        $c2 = $p['c2'];
                        if(($c2 != '') AND (is_numeric($c2)) AND($c2 != '1')){
                            $amount = $amount/$c2;
                            $amount = round($amount,2);
                        }
?>
                       <?php
if($d['company_id']=='1'){
$MERCHANT_KEY = "ByhijwgB";
$SALT = "l6zxaPZp1G";
}
else{
$MERCHANT_KEY = "ByhijwgB";

$SALT = "zU8GPYX0tl";
}

// Merchant Key and Salt as provided by Payu.

//$PAYU_BASE_URL = "https://sandboxsecure.payu.in";		// For Sandbox Mode
$PAYU_BASE_URL = "https://secure.payu.in";			// For Production Mode

$action = '';

$posted = array();
if(!empty($_POST)) {
    //print_r($_POST);
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value; 
	
  }
}

$formError = 0;
if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|productinfo|amount|firstname|email|phone";

if(empty($posted['hash']) && sizeof($posted) > 0) {

  if(
          empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
  ) {
    $formError = 1;
	
  } else {
		
	
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
	
}
?>
<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()">
    <h2>PayU Form</h2>
    <br/>
    <?php if($formError) { ?>
	
      <span style="color:red">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php } ?>
    <form action="<?php echo $PAYU_BASE_URL . '/_payment'; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <input type="hidden" name="amount" value="<?php echo $posted['amount'] ?>" />
      <input type="hidden" name="firstname" value="<?php echo $posted['name'] ?>" />
      <input type="hidden" name="email" value="<?php echo $posted['email'] ?>" />
      <input type="hidden" name="phone" value="<?php echo $posted['phone'] ?>" />
      <input type="hidden" name="productinfo" value="<?php echo $posted['productinfo'] ?>" />
      <table>
			 <tr>
          <td>Success URI: </td>
          <td colspan="3"><input name="surl" value="<?php echo (empty($posted['surl'])) ? '' : $posted['surl'] ?>" size="64" /></td>
        </tr>
        <tr>
          <td>Failure URI: </td>
          <td colspan="3"><input name="furl" value="<?php echo (empty($posted['furl'])) ? '' : $posted['furl'] ?>" size="64" /></td>
        </tr>
        <tr>
          <?php if(!$hash) { ?>
            <td colspan="4"><input type="submit" value="Submit" /></td>
          <?php } ?>
        </tr>
      </table>
    </form>
  </body>
</html>

<?php
                    }

                    else{
                        echo 'Paypal is Not Found!';
                    }


                    break;


                case 'manualpayment':

                    Event::trigger('client/manualpayment/');

                    $p = ORM::for_table('sys_pg')->where('processor', 'manualpayment')->find_one();

                    if($p){
                        $ui->assign('i_due', $amount);
                        $ui->assign('ins',$p['value']);
                        $ui->display('client-ipay.tpl');
                    }


                    break;

                case 'stripe':
                    $p = ORM::for_table('sys_pg')->where('processor', 'stripe')->find_one();

                    if($p){
                        $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
                        $it = $i_total - $i_credit;
                        $amount = $it*100;
                        $ins = ' <script
                                        src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
                                        data-key="'.$p['value'].'"
                                        data-amount="'.$amount.'"
                                        data-name="INV #'.$d['id'].'"
                                        data-email="'.$a['email'].'"
                                        data-currency="'.$p['c1'].'"
                                        data-description="Payment for Invoice # '.$d['id'].'">
                                </script>';

                        $ui->assign('ins',$ins);

                        $ui->display('stripe.tpl');
                    }


                    break;


                case 'stripe_post':
                    $p = ORM::for_table('sys_pg')->where('processor', 'stripe')->find_one();
                    if($p){
                        $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
                        $it = $i_total - $i_credit;
                        $amount = $it*100;
                        $currency_code = $p['c1'];

                        require_once('application/lib/stripe/init.php');


                        $description = "Payment For INV # $invoiceid";

                        $cardNumber = _post('cardNumber');

                        $cardExpiry = _post('cardExpiry');

                        $ce = explode('/',$cardExpiry);


                        $cardCVC = _post('cardCVC');

                        $myCard = array('number' => $cardNumber, 'exp_month' => $ce['0'], 'exp_year' => $ce['1']);


                        try {

                            \Stripe\Stripe::setApiKey($p['value']);
                            $charge = \Stripe\Charge::create(array('card' => $myCard, 'amount' => $amount, 'currency' => $currency_code,"description" => $description));


//                       $charge =  '  Stripe\Charge JSON: {
//    "id": "ch_16QJiYAN1GVPX6ZsbBl20gsJ",
//    "object": "charge",
//    "created": 1437319722,
//    "livemode": false,
//    "paid": true,
//    "status": "succeeded",
//    "amount": 193600,
//    "currency": "usd",
//    "refunded": false,
//    "source": {
//        "id": "card_16QJiYAN1GVPX6ZsDKidAMN7",
//        "object": "card",
//        "last4": "4242",
//        "brand": "Visa",
//        "funding": "credit",
//        "exp_month": 5,
//        "exp_year": 2016,
//        "fingerprint": "n0QKFME5XxL1IRG9",
//        "country": "US",
//        "name": null,
//        "address_line1": null,
//        "address_line2": null,
//        "address_city": null,
//        "address_state": null,
//        "address_zip": null,
//        "address_country": null,
//        "cvc_check": null,
//        "address_line1_check": null,
//        "address_zip_check": null,
//        "tokenization_method": null,
//        "dynamic_last4": null,
//        "metadata": [],
//        "customer": null
//    },
//    "captured": true,
//    "balance_transaction": "txn_16QJiYAN1GVPX6Zs24syLCZi",
//    "failure_message": null,
//    "failure_code": null,
//    "amount_refunded": 0,
//    "customer": null,
//    "invoice": null,
//    "description": null,
//    "dispute": null,
//    "metadata": [],
//    "statement_descriptor": null,
//    "fraud_details": [],
//    "receipt_email": null,
//    "receipt_number": null,
//    "shipping": null,
//    "destination": null,
//    "application_fee": null,
//    "refunds": {
//        "object": "list",
//        "total_count": 0,
//        "has_more": false,
//        "url": "\/v1\/charges\/ch_16QJiYAN1GVPX6ZsbBl20gsJ\/refunds",
//        "data": []
//    }
//}';



                            $charge = str_replace('Stripe\Charge JSON:','',$charge);
                           $resp = json_decode($charge,true);
                            $trid = $resp['id'];
                            $last4 = $resp['source']['last4'];
                          $captured = $resp['captured'];

                            if($captured == true){

                                $inv = ORM::for_table('sys_invoices')->find_one($id);
                                if($inv) {

                                    $inv->status = 'Paid';
                                    $inv->save();
                                    Event::trigger('invoices/markpaid/',$invoice=$inv);
                                    _msglog('s','Payment Successful');
                                    r2(U.'client/iview/'.$d['id'].'/'.'token_'.$d['vtoken']);
                                }

                            }

                            else{
                                _msglog('e','This API call cannot be made with a publishable API key. Please use a secret API key. You can find a list of your API keys at https://dashboard.stripe.com/account/apikeys.');
                                r2(U.'client/iview/'.$d['id'].'/'.'token_'.$d['vtoken']);
                            }



                        } catch(\Stripe\Error\Card $e) {
                            // Since it's a decline, \Stripe\Error\Card will be caught
                            $body = $e->getJsonBody();
                            $err  = $body['error'];

                            print('Status is:' . $e->getHttpStatus() . "\n");
                            print('Type is:' . $err['type'] . "\n");
                            print('Code is:' . $err['code'] . "\n");
                            // param is '' in this case
                            print('Param is:' . $err['param'] . "\n");
                            print('Message is:' . $err['message'] . "\n");
                        } catch (\Stripe\Error\InvalidRequest $e) {
                            // Invalid parameters were supplied to Stripe's API
                        } catch (\Stripe\Error\Authentication $e) {
                            // Authentication with Stripe's API failed
                            // (maybe you changed API keys recently)
                        } catch (\Stripe\Error\ApiConnection $e) {
                            // Network communication with Stripe failed
                        } catch (\Stripe\Error\Base $e) {
                            // Display a very generic error to the user, and maybe send
                            // yourself an email
                        } catch (Exception $e) {
                            // Something else happened, completely unrelated to Stripe
                        }

                    }

                    break;


                case 'authorize_net':

                    $p = ORM::for_table('sys_pg')->where('processor', 'authorize_net')->find_one();

                    if($p){

                        $invoiceid = $d['id'];
                        $amount = $i_total - $i_credit;
                        $url = 'https://secure.authorize.net/gateway/transact.dll';
                        $loginID = $p['value'];

                        $transactionKey = $p['c1'];

                        $description = "Invoice Payment - $invoiceid";

                        // an invoice is generated using the date and time
                        $invoice = $invoiceid;
// a sequence number is randomly generated
                        $sequence = rand(1, 1000);
// a timestamp is generated
                        $timeStamp = time();

                        $testMode = "false";
                        if (phpversion() >= '5.1.2') {
                            $fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey);
                        } else {
                            $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey));
                        }
                        $params = array(
                            array('name' => "x_login",
                                'value' => $loginID
                            ),
                            array('name' => "x_amount",
                                'value' => $amount
                            ),
                            array('name' => "x_description",
                                'value' => $description
                            ),
                            array('name' => "x_invoice_num",
                                'value' => $invoice
                            ),
                            array('name' => "x_fp_sequence",
                                'value' => $sequence
                            ),
                            array('name' => "x_fp_timestamp",
                                'value' => $timeStamp
                            ),
                            array('name' => "x_fp_hash",
                                'value' => $fingerprint
                            ),
                            array('name' => "x_test_request",
                                'value' => $testMode
                            ),
                            array('name' => "x_show_form",
                                'value' => "PAYMENT_FORM"
                            )
                        );

                        Fsubmit::form($url, $params);
                    }


                    break;


                case 'ccavenue':

                    $p = ORM::for_table('sys_pg')->where('processor', 'ccavenue')->find_one();

                    if($p){

                        require ('application/lib/misc/ccavenue.php');

                        $currency_code = $p['c2'];
                        $c3 = $p['c3'];

                        if(($c3 != '') AND (is_numeric($c3)) AND($c3 != '1')){
                            $amount = $amount/$c3;
                        }

                        $Merchant_Id = $p['value']; //Given to merchant by ccavenue


                        $WorkingKey = $p['c1']; //Given to merchant by ccavenue

                        $redirect_url = U . "client/ipay_ipn/$invoiceid/token_$ptoken/";


                        require ('application/lib/misc/ccform.php');


                        // Updated Jan 10, 2016

//                        $Checksum = getCheckSum($Merchant_Id,$amount,$invoiceid ,$redirect_url,$WorkingKey);
//
//                        $url = 'https://www.ccavenue.com/shopzone/cc_details.jsp';
//
//
//
//
//                        $params = array(
//
//                            array('name' => "merchant_id",
//                                'value' => $Merchant_Id
//                            ),
//
//                            array('name' => "Redirect_Url",
//                                'value' => $redirect_url
//                            ),
//
//                            array('name' => "amount",
//                                'value' => $amount
//                            ),
//                            array('name' => "order_id",
//                                'value' => $invoiceid
//                            ),
//                            array('name' => "Checksum",
//                                'value' => $Checksum
//                            ),
//                            array('name' => "upload",
//                                'value' => '1'
//                            ),
//                            array('name' => "ActionID",
//                                'value' => 'TXN'
//                            ),
//                            array('name' => "TxnType",
//                                'value' => 'A'
//                            ),
//                            array('name' => "num_cart_items",
//                                'value' => '1'
//                            ),
//                            array('name' => "rm",
//                                'value' => '2'
//                            ),
//                            array('name' => "payment_gross",
//                                'value' => $amount
//                            ),
//                            array('name' => "TxnType",
//                                'value' => 'A'
//                            ),
//                            array('name' => "payment_gross",
//                                'value' => $amount
//                            ),
//                            array('name' => "currency",
//                                'value' => $currency_code
//                            ),
//                            array('name' => "billing_name",
//                                'value' =>$u['account']
//                            ),
//                            array('name' => "billing_address",
//                                'value' =>$u['address']
//                            ),
//                            array('name' => "billing_city",
//                                'value' =>$u['city']
//                            ),
//                            array('name' => "billing_state",
//                                'value' =>$u['state']
//                            ),
//                            array('name' => "billing_zip",
//                                'value' =>$u['zip']
//                            ),
//                            array('name' => "billing_country",
//                                'value' =>'India'
//                            ),
//                            array('name' => "billing_tel",
//                                'value' =>$u['phone']
//                            ),
//                            array('name' => "billing_email",
//                                'value' =>$u['email']
//                            ),
//                            array('name' => "delivery_name",
//                                'value' =>$u['account']
//                            ),
//                            array('name' => "delivery_address",
//                                'value' =>$u['address']
//                            ),
//                            array('name' => "delivery_city",
//                                'value' =>$u['city']
//                            ),
//                            array('name' => "delivery_state",
//                                'value' =>$u['state']
//                            ),
//                            array('name' => "delivery_state",
//                                'value' =>$u['state']
//                            ),
//                            array('name' => "delivery_zip",
//                                'value' =>$u['zip']
//                            ),
//                            array('name' => "delivery_country",
//                                'value' =>$u['country']
//                            ),
//                            array('name' => "delivery_tel",
//                                'value' =>$u['phone']
//                            ),
//                            array('name' => "merchant_param1",
//                                'value' =>''
//                            )
//
//                        );
//
//
//                        Fsubmit::form($url, $params);

                    }



                    break;


                case 'braintree':

                    $p = ORM::for_table('sys_pg')->where('processor', 'braintree')->find_one();
                    Braintree_Configuration::environment($p['c4']);
                    Braintree_Configuration::merchantId($p['value']);
                    Braintree_Configuration::publicKey($p['c1']);
                    Braintree_Configuration::privateKey($p['c2']);

                    if($p){
                        $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
                        $it = $i_total - $i_credit;
                        $amount = $it*100;
                        $clientToken = Braintree_ClientToken::generate(array());
                        $formurl = U . "client/btpay_submitted/$invoiceid/token_$vtoken/";
                        $vamount =  $config['currency_code']. number_format($d['total'],2,$config['dec_point'],$config['thousands_sep']);
                        $ins = '
                      <form id="checkout" method="post" action="'.$formurl.'">
  <div id="payment-form"></div>
  <input type="submit" value="Pay '.$config['currency_code'].' '.$vamount .'">
</form>
                      <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
                      <script>
									var clientToken = "'.$clientToken.'";
									braintree.setup(clientToken, "dropin", {
  									container: "payment-form"
									});
								</script>';
                        $ui->assign('ins',$ins);
                        $ui->display('client-ipay.tpl');
                    }
                    break;



                case 'quickpay':

                    $p = ORM::for_table('sys_pg')->where('processor', 'quickpay')->find_one();

                    if($p){

                        require 'application/lib/misc/quickpay.php';

                        $qp = new Quickpay($p['value'], $p['c1']);

                        $data_fields['msgtype'] = 'authorize';
                        $data_fields['language'] = 'en';
                        $data_fields['ordernumber'] = $invoiceid;
                        $data_fields['amount'] = $amount;
                        $data_fields['currency'] = $p['c3'];
                        $data_fields['continueurl'] = U . "client/ipay_submitted/$invoiceid/token_$vtoken/";
                        $data_fields['cancelurl'] = U . "client/ipay_cancel/$invoiceid/token_$vtoken/";
                        $data_fields['callbackurl'] = U . "client/ipay_ipn/$invoiceid/token_$ptoken/";

//                   echo '
//
//<form action="https://secure.quickpay.dk/form/" method="post">
//
//'.$qp->form_fields($data_fields).'
//
//<input type="submit" value="Open Quickpay payment window" />
//
//</form>
//
//
//';

                        Fsubmit::input('https://secure.quickpay.dk/form/', $qp->form_fields($data_fields));


                    }





                    break;

				default: 
					/* 	$MERCHANT_KEY = "rjQUPktU";
					$SALT = "e5iIg1jwi8"; 
					$PAYU_BASE_URL = "https://test.payu.in";*/
					
					if($d['company_id']=='1'){
					$MERCHANT_KEY = "ByhijwgB";
					$SALT = "l6zxaPZp1G";
					}
					else{
					$MERCHANT_KEY = "ByhijwgB";
/* var_dump($MERCHANT_KEY);exit; */
					$SALT = "l6zxaPZp1G";
					}	
				//$PAYU_BASE_URL = "https://sandboxsecure.payu.in";		// For Sandbox Mode
						$PAYU_BASE_URL = "https://secure.payu.in";			// For Production Mode
					$action = '';
					$posted = array();

					$posted['hash']='';
					if(!empty($_POST)) {
						foreach($_POST as $key => $value) {    
							$posted[$key] = $value; 
						}
					}
					
					$formError = 0;
					if(empty($posted['txnid'])) {
						
						$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
					} else {
						$txnid = $posted['txnid'];
					}
					$hash = '';
					// Hash Sequence
					$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
		
					if(empty($posted['hash']) && sizeof($posted) > 0) {
						if(empty($posted['amount']) || empty($posted['firstname']) || empty($posted['email']) || empty($posted['phone']) || empty($posted['productinfo']) || empty($posted['surl']) || empty($posted['furl'])) {
							$formError = 1;
						} else {
							
							$hashVarsSeq = explode('|', $hashSequence);
						
							$hash_string = '';	 
							foreach($hashVarsSeq as $hash_var) {
								$hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
									
								$hash_string .= '|';
								
							}
						
							$hash_string .= $SALT;
							$hash = strtolower(hash('sha512', $hash_string));
							$action = $PAYU_BASE_URL . '/_payment';
						}
					} elseif(!empty($posted['hash'])) {
						$hash = $posted['hash'];
						$action = $PAYU_BASE_URL . '/_payment';
					}
?>
<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()" style="display:none;">
    <h2>PayU Form</h2>
    <br/>
    <?php if($formError) { ?>
		
      <span style="color:red">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <table>
        <tr>
          <td><b>Mandatory Parameters</b></td>
        </tr>
        <tr>
          <td>Amount: </td>
          <td><input name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
          <td>First Name: </td>
          <td><input name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></td>
        </tr>
        <tr>
          <td>Email: </td>
          <td><input name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
          <td>Phone: </td>
          <td><input name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
        </tr>
        <tr>
          <td>Product Info: </td>
          <td colspan="3"><textarea name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
        </tr>
        <tr>
          <td>Success URI: </td>
          <td colspan="3"><input name="surl" value="<?php echo (empty($posted['surl'])) ? '' : $posted['surl'] ?>" size="64" /></td>
        </tr>
        <tr>
          <td>Failure URI: </td>
          <td colspan="3"><input name="furl" value="<?php echo (empty($posted['furl'])) ? '' : $posted['furl'] ?>" size="64" /></td>
        </tr>

        <tr>
          <td colspan="3"><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
        </tr>

      
        <tr>
            <td colspan="4"><input type="submit" value="Submit" /></td>
        </tr>
      </table>
    </form>
  </body>
</html>
<?php
                    /* echo 'Payment Gateway Not Found!'; */

            }

        }
        else{
            echo 'Sorry Invoice Not Found!';
            exit;
        }

        break;

    /*
     * CCAvenue
     *
     *
     */


    case 'ipay_cancel':
				echo 'Transaction Cancelled';
				
				 $d = ORM::for_table('sys_invoices')->find_one($_SESSION['id']);
				 
				 header("Location: http://bill.makent.in/?ng=client/iview/".$_SESSION['id']."/token_".$d['vtoken']);
					exit();

        break;

 case 'proformaipay_cancel':
				echo 'Transaction Cancelled';
				
				 $d = ORM::for_table('sys_performa')->find_one($_SESSION['id']);
				 
				 header("Location: http://bill.makent.in/?ng=client/proforma-iview/".$_SESSION['id']."/token_".$d['vtoken']);
					exit();

        break;


    case 'ipay_submitted':

        Event::trigger('client/ipay_submitted/');

        $id  = $routes['2'];
        $token = $routes['3'];
        r2(U."client/iview/$id/$token/",'s',$_L['Payment Successful']);


        break;
 case 'proformaipay_submitted':

        Event::trigger('client/proformaipay_submitted/');

        $id  = $routes['2'];
        $token = $routes['3'];
        r2(U."client/proforma-iview/$id/$token/",'s',$_L['Payment Successful']);


        break;

    case 'ipay_ipn':
        Event::trigger('client/ipay_ipn/');
        $id  = $routes['2'];
        $token = $routes['3'];
        //   r2(U."client/iview/$id/$token/",'s',$_L['Payment Successful']);

        $d = ORM::for_table('sys_invoices')->find_one($id);
        if($d) {
            $token = $routes['3'];
            $token = str_replace('token_', '', $token);
            $ptoken = $d['ptoken'];
            if ($token != $ptoken) {
                echo 'Sorry Token does not match!';
                exit;
            }

            $d->status = 'Paid';
            $d->save();

            Event::trigger('invoices/markpaid/',$invoice=$d);

        }

        break;


    case 'ipay_success':

        Event::trigger('client/ipay_success/');

        //   r2(U."client/iview/$id/$token/",'s',$_L['Payment Successful']);
        
        /*echo '<pre>';
        var_dump($_POST);
        echo '</pre>';
        exit;*/
        
        $p_info      = explode('@', $_POST['productinfo']);
        $productInfo = $p_info[0];
        $invoiceId   = $p_info[1];
        
        $d = ORM::for_table('sys_invoices')->find_one($invoiceId);
        if($d) {
            
            //invoice
            $id = $d['id'];
			$vtoken = $d['vtoken'];
			
            //data
            $sys_acc = ORM::for_table('sys_accounts')->find_one(2);
            $account     = $sys_acc['account'];
            $date        = date('Y-m-d'); 
            $payerid     = $d['userid'];
            $pmethod     = 'PayUMoney';
            $ref         = "";
            $amount      = $_POST['amount']; //$d['subtotal'] - $d['credit'];
            $cat         = "Uncategorized";
            $iid         = $id;  
            $description = "Invoice $productInfo Payment";
            
            //find the current balance for this account
            $a = ORM::for_table('sys_accounts')->where('account', $account)->find_one();
            $cbal = $a['balance'];
            $nbal = $cbal + $amount;
            $a->balance = $nbal;
            $a->save();
            
            $txnid = $_POST['txnid']; // PayU transaction ID

            // Check if transaction already exists
            $existingTransaction = ORM::for_table('sys_transactions')
                ->where('ref', $txnid)
                ->find_one();
            
            if ($existingTransaction) {
                error_log("Duplicate transaction prevented: $txnid");
                // echo("Duplicate transaction prevented: $txnid");
                r2(U."client/iview/$id/$vtoken/",'s',$_L['Payment Successful']);
                exit; // Stop further execution
            }


            //save in transactions
            $t = ORM::for_table('sys_transactions')->create();
            $t->account = $account;
            $t->type = 'Income';
            $t->payerid = $payerid;
            $t->amount = $amount;
            $t->category = $cat;
            $t->method = $pmethod;
            $t->ref = !empty($ref) ? $ref : $txnid;
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
            
            //invoice
            //$id = $d['id'];
			//$vtoken = $d['vtoken'];
            //$d->status = 'Paid';
            
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
            
            //Payment notification to admin
            $admin = ORM::for_table('sys_users')->find_one(11); //11
            
            if ($admin) { // Ensure admin data is found
                $to = $admin['username']; // Assuming this is the email
                $username = $admin['fullname']; // Name of the admin
                $subject = "Invoice Payment";
                
                $txt = "You have received a payment of $amount for invoice #{$d['invoicenum']}. Payment status is {$d['status']}.";
            
                $headers = "";
            
                send_email_brevo_api($to, $username, $subject, $txt, $headers);
            } else {
                error_log("Admin user with ID 11 not found.");
            }            

            Event::trigger('invoices/markpaid/',$invoice=$d);

            r2(U."client/iview/$id/$vtoken/",'s',$_L['Payment Successful']);

        }

        break;


    case 'proformaipay_success':

        Event::trigger('client/proformaipay_success/');

        //   r2(U."client/iview/$id/$token/",'s',$_L['Payment Successful']);

        $d = ORM::for_table('sys_performa')->find_one($_SESSION['id']);
        if($d) {
          $id = $d['id'];
					$vtoken = $d['vtoken'];

            $d->status = 'Paid';
            $d->save();

            Event::trigger('invoices/markpaid/',$invoice=$d);

            r2(U."client/proforma-iview/$id/$vtoken/",'s',$_L['Payment Successful']);

        }

        break;





    case 'btpay_submitted':

        Event::trigger('client/btpay_submitted/');

        $id  = $routes['2'];
        $d = ORM::for_table('sys_invoices')->find_one($id);
        $ui->assign('d',$d);
        $token = $routes['3'];
        $p = ORM::for_table('sys_pg')->where('processor', 'braintree')->find_one();
        if($p){
            $merchantId	= $p["value"];
            $publicKey	= $p["c1"];
            $privateKey	= $p["c2"];
            $account 	= $p["c3"];
            $environment = $p["c4"];
            $accountname = $p["name"];

            Braintree_Configuration::environment($environment);
            Braintree_Configuration::merchantId($merchantId);
            Braintree_Configuration::publicKey($publicKey);
            Braintree_Configuration::privateKey($privateKey);
            $nonce = isset( $_POST["payment_method_nonce"] )?$_POST["payment_method_nonce"]:0;
            if ($nonce) {
                // get user
                $a = ORM::for_table('crm_accounts')->find_one($d['userid']);
                // get invoice
                $id  = $routes['2'];
                $iid = $id;// invoice ID
                $i = ORM::for_table('sys_invoices')->find_one($iid);
                $d = ORM::for_table('sys_invoices')->find_one($id);
                if($d){
                    // we have an invoice, validate token...
                    $token = $routes['3'];
                    $token = str_replace('token_','',$token);
                    $vtoken = $d['vtoken'];
                    if($token != $vtoken){
                        echo 'Sorry Token does not match!';
                        exit;
                    } else {
                        // echo 'TOKEN MATCHES!!!!!!!!!!!!!!!!';
                        $i_credit = $d['credit'];
                        $i_due = '0.00';
                        $i_total = $d['total'];
                        $amount = $i_total - $i_credit;
                        $invoiceid = $d['id'];

                        $result = Braintree_Transaction::sale(array(
                            'amount' => $amount,
                            'orderId' => $id,
                            'paymentMethodNonce' => $nonce,
                            'options' => array(
                                'submitForSettlement' => True
                            )
                        ));

                        if ($result->success) {


                            $invoiceview = U . "invoices/pdf/$invoiceid/view/token_$vtoken";
                            $invoiceprint = U . "iview/print/$invoiceid/token_$vtoken";

                            // Thank you! Your payment has been successfully processed for $16.95
                            $ins = "Success!: Thank you for your payment.";
//                            $ins.= "<br />".'To PRINT your invoice click here <br> <a class="btn btn-primary" href="'.$invoiceprint.'" target="_blank">Print Invoice</a>';
//                            $date = $result->transaction->createdAt->date; //"2015-06-15 18:52:57.000000"
//                            $amount = $result->transaction->amount;
//                            $amount = Finance::amount_fix($amount);
//                            $payerid = $a["id"];
//                            $pmethod = 'Braintree';
//                            $amount = str_replace($config['currency_code'], '', $amount);
//                            $amount = str_replace(',', '', $amount);
//                            if (!is_numeric($amount)) {
//                                $msg .= 'Invalid Amount' . '<br>';
//                            }
//                            $cat = 'Consulting'; //77; // Consulting income. This should already be defined on the invoice or line item.

//                            $description = $p["name"]; //'Braintree Payment';
//                            $a = ORM::for_table('sys_accounts')->where('id', $account)->find_one(); // get braintree balance
//                            $cbal = $a['balance']; // customer balance
//                            $nbal = $cbal + $amount;
//                            $a->balance = $nbal;
//                            $a->save(); // update customer balance
//                            $d = ORM::for_table('sys_transactions')->create(); // BOF add a transaction
//                            $d->account = $accountname;
//                            $d->type = 'Income';
//                            $d->payerid = $payerid;
//
//                            $d->amount = $amount;
//                            $d->category = $cat;
//                            $d->method = $pmethod;
//                            $d->description = 'Invoice '.$id .' Payment'; //$description;
//                            $d->date = date('Y-m-d');//"2015-06-15 18:52:57.000000"
//                            $d->dr = '0.00';
//                            $d->cr = $amount;
//                            $d->bal = $nbal;
//                            $d->iid = $iid;
//                            $d->save(); // BOF add a transaction
//                            $tid = $d->id();
//                            // log it...
//                            _log('New Deposit: ' . $description . ' [TrID: ' . $tid . ' | Amount: ' . $amount . ']', 'Admin',$payerid);
//                            _msglog('s', 'Transaction Added Successfully');

                            if ($i) {
                                $pc = $i['credit'];
                                $it = $i['total'];
                                $dp = $it - $pc;
                                if (($dp == $amount) OR (($dp < $amount))) {
                                    $i->status = 'Paid';
                                    $i->datepaid = date('Y-m-d H:i:s');
                                    Event::trigger('invoices/markpaid/',$invoice=$i);
                                } else {
                                    $i->status = 'Partially Paid';
                                }
                                $i->credit = $pc + $amount;
                                $i->paymentmethod = $accountname;
                                $i->save();

                            } //if ($i) {
                        } else if ($result->transaction) {
                            $ins = "Error processing transaction:";
                            $ins .= ("\n  code: " . $result->transaction->processorResponseCode);
                            $ins .= ("\n  text: " . $result->transaction->processorResponseText);
                        } else {
                            $ins = ("Validation errors: \n");
                            $ins .= ($result->errors->deepAll());
                        }
//                        $ui->assign('ins',$ins);
//                        $ui->display('client-ipay.tpl');
                        r2(U.'client/iview/'.$i->id.'/'.$i->vtoken.'/','s',$ins);
                    }
                }
            }
            /* eof bernie changes */
        } else echo 'Payment Gateway Not Found!';


        break;

    case 'ccsubmit':


        $p = ORM::for_table('sys_pg')->where('processor', 'ccavenue')->find_one();

        if($p) {

            require('application/lib/misc/ccavenue.php');

            $currency_code = $p['c2'];
            $c3 = $p['c3'];

            if (($c3 != '') AND (is_numeric($c3)) AND ($c3 != '1')) {
                $amount = $amount / $c3;
            }

            $Merchant_Id = $p['value']; //Given to merchant by ccavenue


            $WorkingKey = $p['c1']; //Given to merchant by ccavenue

            $redirect_url = U . "client/ipay_ipn/$invoiceid/token_$ptoken/";


            require('application/lib/misc/ccsubmit.php');

        }


        break;



    case 'login':

        Event::trigger('client/login/');

        Contacts::isLogged();


        $ui->display('client_login.tpl');


        break;


    case 'register':
        $extra_fields = array();
        $ui->assign('extra_fields',$extra_fields);
        Event::trigger('client/register/');

        Contacts::isLogged();

        $ui->assign('xfooter',Asset::js(array('contacts/register')));


        $ui->display('client_register.tpl');


        break;

    case 'forgot_pw':

        Event::trigger('client/forgot_pw/');

        $ui->display('client_forgot_pw.tpl');


        break;

    case 'forgot_pw_post':

        Event::trigger('client/forgot_pw_post/');

        $username = _post('username');

        $d = ORM::for_table('crm_accounts')->where('email',$username)->find_one();

        if($d){

            //

            $fullname = $d->account;

            $password = Ib_Str::random_string(8);

            $password_hash = Password::_crypt($password);

            $d->password = $password_hash;

            $d->save();

            // Send email notification

            $mail = Notify_Email::_init();
            $mail->AddAddress($username, $fullname);
            $mail->Subject = 'Password Reset for '.$config['CompanyName'];
            $mail->MsgHTML('Your Password has been reset to: '. $password.' Go to this link to login with new password- '.U.'client/login/');
            $mail->Send();

            r2(U.'client/login/','s','New Password has been sent to your email.');



        }

        else{

            r2(U.'client/forgot_pw/','e','No User found with this Email');

        }



        break;

    case 'auth':

        Event::trigger('client/auth/');

        $email = _post('username');
        $password = _post('password');

        $auth = Contacts::login($email,$password);

        if($auth){

            // store authentication key in the cookies

            setcookie('ib_ct', $auth, time() + (86400 * 30), "/"); // 86400 = 1 day

            r2(U.'client/dashboard/');



        }
        else{
            r2(U.'client/login/','e',$_L['Invalid Username or Password']);
        }




        break;


    case 'auto_login':
        Event::trigger('client/auto_login/');



        break;


    case 'register_post':

       // sleep(3);

        if(!isset($_SESSION['recaptcha_verified'])){
            $_SESSION['recaptcha_verified'] = false;
        }

        if($config['recaptcha'] == 1){


            if(!$_SESSION['recaptcha_verified']){

                if(Ib_Recaptcha::isValid($config['recaptcha_secretkey']) == false){

                    ib_die($_L['Recaptcha Verification Failed']);

                }
                else{

                    $_SESSION['recaptcha_verified'] = true;

                }

            }



        }

        $msg = '';

        $data = array();

        Event::trigger('client/register_post/');



        $data['account'] = _post('fullname');
        $data['email'] = _post('email');
        $data['password'] = _post('password');
        $data['password2'] = _post('password2');

        $o_password = $data['password'];

        if($data['account'] == ''){
            $msg .= 'Fullname is required <br>';
        }

        if(Validator::Email($data['email']) == false){
            $msg .= $_L['Invalid Email'].' <br>';
        }
        $f = ORM::for_table('crm_accounts')->where('email',$data['email'])->find_one();

        if($f){
            $msg .= $_L['Email already exist'].' <br>';
        }



        if($data['password'] != ''){

            if(!Validator::Length($data['password'],15,5)){
                $msg .= 'Password should be between 6 to 15 characters'. '<br>';

            }

            if($data['password'] != $data['password2']){
                $msg .= 'Passwords does not match'. '<br>';
            }


            $data['password'] = Password::_crypt($data['password']);


        }
        else{

            $msg .= 'Password is required <br>';

        }

        // API call for extra fields



        //

        // optional params

        $data['phone'] = _post('phone');
        $data['address'] = _post('address');
        $data['city'] = _post('city');
        $data['zip'] = _post('zip');
        $data['state'] = _post('');
        $data['country'] = _post('country');
        $data['company'] = _post('company');
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['email_verified'] = 'No';
        $ip = get_client_ip();
        $data['signed_up_ip'] = $ip;
        $isp = gethostbyaddr($ip);
        if(!$isp){

            $isp = '';

        }

        $data['isp'] = $isp;
        $data['balance'] = '0.00';
        $data['status'] = 'Active';
        $data['notes'] = '';
        $data['token'] = '';
        $data['img'] = '';
        $data['web'] = '';
        $data['facebook'] = '';
        $data['google'] = '';
        $data['linkedin'] = '';
        $data['twitter'] = '';
        $data['skype'] = '';
//        $data[''] = '';


//        $ = _post('');



        Event::trigger('client_register_post_data_posted');


        if($msg == ''){

            // create client




            // try to guess location



            //

            $d = ORM::for_table('crm_accounts')->create();

            $d->account = $data['account'];
            $d->email = $data['email'];
            $d->phone = $data['phone'];
            $d->address = $data['address'];
            $d->city = $data['city'];
            $d->zip = $data['zip'];
            $d->state = $data['state'];
            $d->country = $data['country'];
            $d->tags = '';

            //others
            $d->fname = '';
            $d->lname = '';
            $d->company = $data['company'];
            $d->jobtitle = '';
            $d->cid = '0';
            $d->o = '0';
            $d->balance = $data['balance'];
            $d->status = $data['status'];
            $d->notes = $data['notes'];
            $d->password = $data['password'];
            $d->token = '';
            $d->ts = '';
            $d->img = $data['img'];
            $d->web = $data['web'];
            $d->facebook = $data['facebook'];
            $d->google = $data['google'];
            $d->linkedin = $data['linkedin'];

            // v 4.2

            $d->gname = '';
            $d->gid = 0;

            $d->signed_up_ip = $ip;
            $d->isp = $data['isp'];

            //
            $d->save();
            $cid = $d->id();
            _log($_L['New Contact Added'].' '.$data['account'].' [CID: '.$cid.']','Portal Registration');


            $send_email = Ib_Email::send_client_welcome_email($data);

            $auth = Contacts::login($data['email'],$o_password);

            if($auth){

                // store authentication key in the cookies

                setcookie('ib_ct', $auth, time() + (86400 * 30), "/"); // 86400 = 1 day



            }

            echo $cid;

            Event::trigger('client/client_registered',$data);




        }

        else{

            echo $msg;

        }






        break;


    case 'dashboard':
        // 297

        $dashboard_summary_extras = '';
        $dashboard_extra_row_1 = '';
        $c = Contacts::details();

        Event::trigger('client/dashboard/');

        $ui->assign('_application_menu', 'dashboard');
        $ui->assign('_st', $_L['Dashboard']);
        $ui->assign('_title', $config['CompanyName'].' - '.$_L['Dashboard']);

        $cf = ORM::for_table('crm_customfields')->where('ctype','crm')->order_by_asc('id')->find_many();
        $ui->assign('cf',$cf);

        $branch_name = get_branch_name($c->branch_id);
        $ui->assign('branch_name', $branch_name);
        $ui->assign('user',$c);

        $cid = $c->id;
        $d = ORM::for_table('sys_transactions')
            ->where_any_is(array(
                array('payerid' => $cid),
                array('payeeid' => $cid)))->limit(5)
            ->find_many();
        $ui->assign('t',$d);

        $d = ORM::for_table('sys_invoices')->where('userid',$c->id)->limit(5)->find_array();

        $ui->assign('d',$d);

        $d = ORM::for_table('sys_quotes')->where('userid',$c->id)->limit(5)->find_array();

        $ui->assign('q',$d);

        //  aSign: \''.$config['currency_code'].' \',

        $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

    
        dGroup: '.$config['thousand_separator_placement'].',
        aPad: '.$config['currency_decimal_digits'].',
        pSign: \''.$config['currency_symbol_position'].'\',
        aDec: \''.$config['dec_point'].'\',
        aSep: \''.$config['thousands_sep'].'\'
    
        });');

        $employee = ORM::for_table('crm_accounts')->find_one($cid);
        $employee_timesheet = ORM::for_table('crm_timesheet')->where('date', date('Y-m-d'))->where('employee_id', $cid)->find_one();
        
        $ui->assign('APP_URL', APP_URL);
        $ui->assign('employee_id',$employee->id);
        $ui->assign('employee',$employee);
        $ui->assign('cid',$cid);
        $ui->assign('dashboard_summary_extras',$dashboard_summary_extras);
        $ui->assign('dashboard_extra_row_1',$dashboard_extra_row_1);
        
        
        $ui->assign('xheader',Asset::css(array('modal')));
        $ui->assign('xfooter2',Asset::js(array('jquery.dataTables')));
        $ui->assign('xfooter',Asset::js(array('modal')));
        
        
        $ui->display('client_dashboard.tpl');

        break;

 /*start - timesheet*/
    
        // case 'employee-timesheet':
        //     Event::trigger('client/employee-timesheet/');
        //     $c = Contacts::details();   
        //     $cid = $c->id;
        //     $d = ORM::for_table('crm_accounts')->find_one($cid);
        //     if($d)
        //     {
        //         $ui->assign('cid',$cid);
        //         $ui->assign('employee',$d);
        //         $ui->display('timesheet/ajax.client-timesheet-employee.tpl');                
        //     }
        // break;  
        
        case 'list-ajax-timesheet':  
            Event::trigger('client/list-ajax-timesheet/');
            $response = array();
            
            try {
                ## Read value
                $draw = $_POST['draw'];
                $start = $_POST['start'];
                $rowperpage = $_POST['length'];
                $columnIndex = $_POST['order'][0]['column'];
                $columnName = $_POST['columns'][$columnIndex]['data'];
                $columnSortOrder = $_POST['order'][0]['dir'];
                        
                
                $employee_id =  $_POST['employee_id'];
                $todate  = $_POST['todate'];
                $fromdate  = $_POST['fromdate'];
    
                
                $totalRecordwithFilter = ORM::for_table('crm_timesheet')->select('id');
                
                if(!empty($employee_id))
                {
                    $totalRecordwithFilter->where('employee_id', $employee_id);
                }
    
                if(!empty($fromdate) && !empty($todate))
                {
                    $totalRecordwithFilter->where_gte('date', $fromdate);
                    $totalRecordwithFilter->where_lte('date', $todate);
                }
        
                $totalRecordwithFilter->offset($start);
                $totalRecordwithFilter->limit($rowperpage);  
        
                $totalRecordwithFilter = $totalRecordwithFilter->count();
        
        
                $record = ORM::for_table('crm_timesheet');
               
                if(!empty($employee_id))
                {
                    $record->where('employee_id', $employee_id);
                }
    
                if(!empty($fromdate) && !empty($todate))
                {
                    $record->where_gte('date', $fromdate);
                    $record->where_lte('date', $todate);
                }
        
                $records = $record->find_many();
                
                // Calculate sum of earn_amount
                $earnAmountSum = 0;
                foreach ($records as $record) {
                    $earnAmountSum += $record->earn_amount;
                }
                
                $record->offset($start);
                $record->limit($rowperpage); 
                
                if($columnSortOrder == 'asc')
                {
                    $record->order_by_asc($columnName);
                }
                elseif($columnSortOrder == 'desc')
                {
                    $record->order_by_desc($columnName);
                }
                
                $data = array();
                $sr = $start + 1;
                foreach($records as $record){  
                    // Fetch invoice_alocation_id for each record
                    $invoice_alocation = ORM::for_table('invoice_alocation')
                        ->select('invoice_id')
                        ->where('id', $record->invoice_alocation_id)
                        ->find_one();
                    
                    // Fetch invoicenum from sys_invoices if invoice_id is available
                    $invoicenum = 'N/A'; // Default to 'N/A' if invoicenum is not available
                    if ($invoice_alocation) {
                        $invoice_id = $invoice_alocation->invoice_id; // Extract the invoice_id from the result
                    
                        // Fetch invoicenum from sys_invoices using the invoice_id
                        $invoice = ORM::for_table('sys_invoices')
                            ->select('invoicenum')
                            ->where('id', $invoice_id)
                            ->find_one();
                    
                        if ($invoice) {
                            $invoicenum = '<p>' . $invoice->invoicenum . '</p>';
                        }
                    }
                    $event1 = "edit_timesheet_modal('".$record->id."')";
                    $edit = '<a href="javascript:void(0);" class="btn btn-primary btn-xs" onclick="'.$event1.'"><i class="fa fa-edit"></i> edit</a>';  
                    $salery_type = $record->invoice_alocation_id ? 'Per Piece' : 'Per Hour';
                    $data[] = array( 
                        "sr"          => $sr,
                        "employee_type" => $salery_type,
                        "checkin"       => $record->checkin,
                        "checkout"      => $record->checkout,
                        "qty"           => $record->qty,
                        "amount"        => $record->amount,
                        "earn_amount"   => $record->earn_amount,
                        "earnAmountSum" => $earnAmountSum,
                        "invoicenum"    => $invoicenum,
                        "date"          => $record->date,
                        // "action"        => $edit,
                    ); 
                    $sr++;	
                }
          
                ## Response
                $response = array(
                    "draw" => intval($draw),
                    "iTotalRecords" => $totalRecordwithFilter,
                    "iTotalDisplayRecords" => $totalRecordwithFilter,
                    "aaData" => $data,
                    "earnAmountSum" => $earnAmountSum
                );
            } catch (Exception $e) {
                // Handle any exceptions here, possibly by setting an error flag in the response
                $response = array(
                    "error" => "An error occurred: " . $e->getMessage()
                );
            }
            echo json_encode($response);
            $ui->assign('earnAmountSum',$earnAmountSum);
        break;  
        
        case 'edit-timesheet-modal': //in use
            Event::trigger('client/edit-timesheet-modal');
            $timesheet_id = $routes['2'];
            $timesheet = ORM::for_table('crm_timesheet')->find_one($timesheet_id);
            if ($timesheet) {
                $employee_id = $timesheet->employee_id;
                $employee = ORM::for_table('crm_accounts')->where('id', $employee_id)->find_one();
                if ($employee) {
                    $salery_type = $employee->salery_type;
                    
                } else {
                    // Handle the case where employee record is not found
                }
            } else {
                // Handle the case where timesheet record is not found
            }
             
            $ui->assign('salery_type', $salery_type);
            $ui->assign('timesheet', $timesheet);
            $ui->display('timesheet/edit-timesheet-modal.tpl');
        break;   
        
        case 'edit-timesheet-post':
            $record_id   = _post('timesheet_id');
            $type        = _post('type');
            $checkIn     = _post('checkin');
            $checkOut    = _post('checkout');
            $remarks = _post('remarks');
            $qty         = _post('qty');
            $date        = date('Y-m-d', strtotime(_post('checkout')));
            $updatedAt   = date('Y-m-d H:i:s');
            
            $timesheet = ORM::for_table('crm_timesheet')->find_one($record_id);

            if($timesheet){
                
                //get hours between checkin & out
                $checkIn  = strtotime($timesheet->checkin);
                $checkOut = strtotime(date('Y-m-d H:i:s'));
                $seconds_diff = $checkOut - $checkIn;
                $hours = $seconds_diff / (60 * 60); // Convert seconds to hours
                $timesheet->checkout = date('Y-m-d H:i:s', $checkOut);
                $timesheet->qty = ($type == 'per_hour') ? $hours : $qty;
                $timesheet->earn_amount = $timesheet->qty * $timesheet->amount;
                $timesheet->remarks = $remarks;
                $timesheet->updated_at = $updatedAt;
                
                $timesheet->save();
                _msglog('s',$_L['Timesheet_updated_successfully!']);
                echo $timesheet->id();
            }
        break;        
        
        // case 'set-salery-popup-form':
        //     $id = $routes['2'];
        //     $d = ORM::for_table('crm_accounts')->find_one($id);
        //     $ui->assign('d',$d);
        //     $ui->assign('_theme',$_theme);
        //     $ui->display('timesheet/set-salery-popup-form.tpl');
        // break;      
        
        // case 'set-salery-type-post':
        //     $msg = '';
        //     $id   = _post('id');
        //     $salery_type = _post('salery_type');
        //     $salery_amt = _post('salery_amt');
            
        //     if($salery_type == ''){
        //         $msg .= 'Salery type is required <br>';
        //     }
        //     if($salery_amt == ''){
        //         $msg .= 'Salery amount is required <br>';
        //     }
    
        //     if($msg == ''){
        //         $d = ORM::for_table('crm_accounts')->find_one($id);
        //         if($d){
        //             $d->salery_type = $salery_type;
        //             $d->salery_amt = $salery_amt;
        //             $d->save();
        //             _msglog('s',$_L['Updated_successfully!']);
        //             echo $d->id();
        //         }
        //     }
        //     else{
        //         echo $msg;
        //     }
        // break;
        
        case 'timesheet-popup-form':
            Event::trigger('client/timesheet-popup-form');
            
            $id = $routes['2'];
            $employee = ORM::for_table('crm_accounts')->find_one($id);
            $employee_timesheet = ORM::for_table('crm_timesheet')->where('date', date('Y-m-d'))->where('employee_id', $id)->where_null('invoice_alocation_id')->find_one();
            $ui->assign('employee',$employee);
            $ui->assign('timesheet',$employee_timesheet);
            $ui->assign('_theme',$_theme);
            $ui->display('timesheet/timesheet-popup-form.tpl');
        break;   
        
        case 'timesheet-entry-post':
            Event::trigger('client/timesheet-entry-post');
            $record_id   = _post('record_id');
            $type        = _post('type');
            $remarks     = _post('remarks');
            $employee_id = _post('employee_id');
            $checkIn     = ($type == 'per_hour') ? date('Y-m-d H:i:s') : null;
            $checkOut    = ($type == 'per_hour') ? date('Y-m-d H:i:s') : null;
            $qty         = _post('qty');
            $amount      = _post('amount');
            $date        = date('Y-m-d');
            $createdAt   = date('Y-m-d H:i:s');
            $updatedAt   = date('Y-m-d H:i:s');

 
            $timesheet = ORM::for_table('crm_timesheet')->find_one($record_id);
            //var_dump($timesheet->checkin);
            if($timesheet){
                //get hours between checkin & out
                $checkIn  = strtotime($timesheet->checkin);
                $checkOut = strtotime(date('Y-m-d H:i:s'));
                $seconds_diff = $checkOut - $checkIn;
                $hours = $seconds_diff / (60 * 60); // Convert seconds to hours
                $timesheet->checkout = date('Y-m-d H:i:s', $checkOut);
                $timesheet->amount = $amount;
                $timesheet->remarks = $remarks;
                $timesheet->qty = ($type == 'per_hour') ? $hours : $qty;
                $timesheet->earn_amount = $timesheet->qty * $timesheet->amount;
                $timesheet->updated_at = $updatedAt;
            
                $timesheet->save();
                _msglog('s',$_L['CheckOut_successfully!']);
                echo $timesheet->id();
            } else {
                // If record doesn't exist, create a new one
                $insert = ORM::for_table('crm_timesheet')->create();
                $insert->employee_id = $employee_id;
                $insert->checkin     = $checkIn;
                $insert->amount      = $amount;
                $insert->date        = $date;
                $insert->remarks       = $remarks;
                $insert->qty         = ($type == 'per_hour') ? 0 : $qty;
                $insert->earn_amount = $insert->qty * $insert->amount;                
                $insert->created_at  = $createdAt;
                $insert->updated_at  = $updatedAt;
            
                $insert->save();
                _msglog('s',$_L['CheckIn_successfully!']);
                echo $insert->id();
            }
        break;        
        /*end - timesheet*/
        
    case 'invoices':
        Event::trigger('client/invoices/');
        $ui->assign('_application_menu', 'invoices');
        $ui->assign('_st', $_L['Invoices']);
        $ui->assign('_title', $config['CompanyName'].' - '.$_L['Invoices']);

        $c = Contacts::details();
        $branch_name = get_branch_name($c->branch_id);
        $ui->assign('branch_name', $branch_name);
        $ui->assign('user',$c);

        $d = ORM::for_table('sys_invoices')->where('userid',$c->id)->find_array();

        $ui->assign('d',$d);

        $ui->assign('total_invoice',count($d));

        //  aSign: \''.$config['currency_code'].' \',

        $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

   
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });');


        $ui->display('client_invoices.tpl');


        break;
        
/*
    case 'employee_invoices':
        // Trigger an event
        Event::trigger('employee/employee_invoices/');
    
        // Assign variables for UI display
        $ui->assign('_application_menu', 'employee_invoices');
        $ui->assign('_st', 'Employee Invoices');
        $ui->assign('_title', $config['CompanyName'].' - '.$_L['Employee Invoices']);
        
        // Fetch the current user's details
        $c = Contacts::details();
        $branch_name = get_branch_name($c->branch_id);
        $ui->assign('branch_name', $branch_name);
        // Assign current user's data to UI
        $ui->assign('user', $c);
    
        // Get the filter value (Paid/Unpaid) from request
        $filter_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : ''; 
        
        // Fetch all invoice allocations for the current user
        $invoiceAllocations = ORM::for_table('invoice_alocation')
            ->where('employee_id', $c->id)
            ->order_by_desc('created_at') // Sorting by created_at in descending order
            ->find_array();
            
        $ui->assign('total_invoice',count($invoiceAllocations));
        // Initialize an array to hold employee invoices
        $employeeInvoices = [];
        $totalEarnSum = 0; // Variable to store total of Total Earn Amount
        
        // Iterate through each invoice allocation
        foreach ($invoiceAllocations as $allocation) {
            // Get invoice ID, employee ID, and allocation ID from the current allocation
            $invoiceId = $allocation['invoice_id'];
            $employeeId = $allocation['employee_id'];
            $allocationId = $allocation['id'];
            $allocationQty = $allocation['qty'];
            $allocationPrice = $allocation['price'];
            $allocationStatus = $allocation['status'];
            $allocationCreated = date('j F Y', strtotime($allocation['created_at']));
        
            // Fetch invoicenum from sys_invoices if invoice_id is available
            // Fetch invoicenum from sys_invoices using the invoice_id
            // $invoice = ORM::for_table('sys_invoices')
            //     ->select('invoicenum')
            //     ->where('id', $invoiceId)
            //     ->where('delivery_status', 'processing')
            //     ->find_one();
            // $invoiceNum = $invoice ? $invoice->invoicenum : 'N/A'; // Default 'N/A' if not found
    
            // if ($invoice) {
            //     $invoiceNum = '<a href="' . APP_URL . '/?ng=invoices/view/' . $invoice_id . '/" target="_blank">' . $invoice->invoicenum . '</a>';
            // }
            
            // Fetch invoicenum from sys_invoices if invoice_id exists and delivery_status is not "pending"
            $invoice = ORM::for_table('sys_invoices')
                ->select('invoicenum')
                ->where('id', $invoiceId)
                ->where_not_equal('delivery_status', 'pending') // Exclude "pending" invoices
                ->find_one();
        
            if (!$invoice) {
                continue; // Skip this allocation if the invoice is "pending" or not found
            }
        
            $invoiceNum = $invoice->invoicenum;
        
            // Fetch records from crm_timesheet for the current employee ID and allocation ID
            $timesheets = ORM::for_table('crm_timesheet')
                ->where('employee_id', $employeeId)
                ->where('invoice_alocation_id', $allocationId)
                ->find_many() ?: [];
        
            // Initialize total quantity, amount, and total earn amount for the current allocation
            $totalQty = 0;
            $totalAmount = 0;
            $totalEarnAmount = 0;
            $isPaid = false; // Default unpaid
          
            if(count($timesheets) > 0){
                // Iterate through each timesheet record and sum up the quantities, amounts, and earn amounts
                foreach ($timesheets as $timesheet) {
                    $totalQty += $timesheet->qty;
                    $totalAmount += $timesheet->amount;
                    $totalEarnAmount += $timesheet->earn_amount;
                    
                    // If any record has a transaction_id, mark as Paid
                    if (!empty($timesheet->transaction_id)) {
                        $isPaid = true;
                    }
                }
            }else{
                $totalQty = $allocationQty;
                $totalAmount = $allocationPrice;
                $totalEarnAmount = $allocationQty * $allocationPrice;
            }
            
            // Define payment status
            $paymentStatus = $isPaid ? 1 : 0;
        
            // Apply filter based on payment_status selection
            if ($filter_status !== '' && $paymentStatus != $filter_status) {
                continue;
            }
            
            // Add invoice details to the array of employee invoices
            $employeeInvoices[] = [
                'invoice_id' => $invoiceId,
                'invoice_num' => $invoiceNum,
                // 'employee_id' => $employeeId,
                'qty' => $totalQty,
                'amount' => $totalAmount,
                'total_earn_amount' => $totalEarnAmount,
                'status' => $allocationStatus,
                'payment_status' => $paymentStatus,
                'created_at' => $allocationCreated
            ];
            
            // Add to total sum for tfoot
            $totalEarnSum += $totalEarnAmount;
        }
    
        // Assign employee invoices data to UI
        $ui->assign('employeeInvoices', $employeeInvoices);
        $ui->assign('totalEarnSum', $totalEarnSum);
    
        // Display the employee invoices template
        $ui->display('employee_invoices.tpl');
        break;
*/

    // ====================================================
    // VIEW
    // ====================================================
    case 'employee_invoices':
        Event::trigger('employee/employee_invoices/');

        $ui->assign('_application_menu', 'employee_invoices');
        $ui->assign('_st', 'Employee Invoices');
        $ui->assign('_title', $config['CompanyName'].' - '.$_L['Employee Invoices']);

        // current user
        $c = Contacts::details();
        $branch_name = get_branch_name($c->branch_id);
        $ui->assign('branch_name', $branch_name);
        $ui->assign('user', $c);

        // we do NOT fetch invoices here – DataTable will do it
        $ui->assign('xheader', Asset::css(['datatables.min', 'buttons.dataTables.min']));
        $ui->assign('xfooter', Asset::js(['datatables.min', 'dataTables.buttons.min', 'buttons.print.min', 'employee-invoices-dt']));

        $ui->display('employee_invoices.tpl');
        break;


    // ====================================================
    // DATATABLE (AJAX)
    // ====================================================
    case 'employee_invoices-dt':
        // must be logged in contact
        $c = Contacts::details();
        if (!$c) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'unauthorized']);
            break;
        }

        // kill any previous output
        if (function_exists('ob_get_level')) {
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $req    = $_REQUEST;
            $length = isset($req['length']) ? (int)$req['length'] : 10;
            $start  = isset($req['start'])  ? max(0, (int)$req['start']) : 0;

            // filters from form
            $payment_status = isset($req['payment_status']) ? trim($req['payment_status']) : '';
            $order_status   = isset($req['order_status']) ? trim($req['order_status']) : '';
            $search_term    = isset($req['search_term']) ? trim($req['search_term']) : '';
            $date_type      = isset($req['date_type']) ? trim($req['date_type']) : '';
            $date_from      = isset($req['date_from']) ? trim($req['date_from']) : '';
            $date_to        = isset($req['date_to']) ? trim($req['date_to']) : '';

            // DataTable columns
            $columns = [
                0 => 'sr',
                1 => 'invoice_num',
                2 => 'qty',
                3 => 'amount',
                4 => 'total_earn',
                5 => 'order_status',
                6 => 'payment_status',
                7 => 'assign_date',
                8 => 'completed_date',
                9 => 'paid_date',
            ];
            $order_index = isset($req['order'][0]['column']) ? (int)$req['order'][0]['column'] : 7;
            $order_col   = isset($columns[$order_index]) ? $columns[$order_index] : 'assign_date';
            $order_dir   = (isset($req['order'][0]['dir']) && strtolower($req['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

            // -------- base SQL (same as before) --------
            $timesheetAggSQL = "
                SELECT
                    ts.invoice_alocation_id AS alloc_id,
                    SUM(ts.qty)                         AS ts_qty,
                    SUM(ts.amount)                      AS ts_amount,
                    SUM(ts.earn_amount)                 AS ts_earn,
                    MAX(CASE WHEN ts.transaction_id IS NOT NULL AND ts.transaction_id <> '' THEN 1 ELSE 0 END) AS is_paid,
                    MIN(CASE WHEN ts.transaction_id IS NOT NULL AND ts.transaction_id <> '' THEN ts.paid_date ELSE NULL END) AS paid_date
                FROM crm_timesheet ts
                GROUP BY ts.invoice_alocation_id
            ";

            $baseSQL = "
                SELECT
                    alloc.id,
                    alloc.invoice_id,
                    alloc.employee_id,
                    alloc.qty        AS alloc_qty,
                    alloc.price      AS alloc_price,
                    alloc.status     AS alloc_status,
                    alloc.created_at AS assign_date,
                    alloc.completed_date AS completed_date,
                    inv.invoicenum,
                    inv.delivery_status,
                    tsagg.ts_qty,
                    tsagg.ts_amount,
                    tsagg.ts_earn,
                    tsagg.is_paid,
                    tsagg.paid_date
                FROM invoice_alocation alloc
                LEFT JOIN sys_invoices inv
                    ON inv.id = alloc.invoice_id
                LEFT JOIN ( $timesheetAggSQL ) AS tsagg
                    ON tsagg.alloc_id = alloc.id
                WHERE alloc.employee_id = ?
                AND (inv.id IS NULL OR inv.delivery_status <> 'pending')
            ";

            $params = [$c->id];

            // IMPORTANT: use raw_query from ORM
            $allRows = ORM::for_table('invoice_alocation')
                ->raw_query($baseSQL, $params)
                ->find_array();

            $filtered = [];
            foreach ($allRows as $row) {

                // compute final figures
                if (!empty($row['ts_qty'])) {
                    $qty        = (float)$row['ts_qty'];
                    $amount     = (float)$row['ts_amount'];
                    $total_earn = (float)$row['ts_earn'];
                    $is_paid    = (int)$row['is_paid'];
                    $paid_date  = $row['paid_date'];
                } else {
                    $qty        = (float)$row['alloc_qty'];
                    $amount     = (float)$row['alloc_price'];
                    $total_earn = (float)$row['alloc_qty'] * (float)$row['alloc_price'];
                    $is_paid    = 0;
                    $paid_date  = null;
                }

                $alloc_status = (int)$row['alloc_status'];

                // 1) payment filter
                if ($payment_status !== '' && (int)$payment_status !== $is_paid) {
                    continue;
                }

                // 2) order status filter
                if ($order_status !== '' && (int)$order_status !== $alloc_status) {
                    continue;
                }

                // 3) date filter
                if ($date_type !== '' && ($date_from !== '' || $date_to !== '')) {
                    $row_date_raw = null;
                    if ($date_type === 'assign_date') {
                        $row_date_raw = $row['assign_date'];
                    } elseif ($date_type === 'completed_date') {
                        $row_date_raw = $row['completed_date'];
                    } elseif ($date_type === 'paid_date') {
                        $row_date_raw = $paid_date;
                    }

                    if (empty($row_date_raw)) {
                        // no date -> skip
                        continue;
                    }

                    $row_date = date('Y-m-d', strtotime($row_date_raw));

                    if ($date_from !== '' && $row_date < $date_from) {
                        continue;
                    }
                    if ($date_to !== '' && $row_date > $date_to) {
                        continue;
                    }
                }

                // 4) search
                if ($search_term !== '') {
                    $needle = strtolower($search_term);
                    $hay = strtolower(
                        ($row['invoicenum'] ?? '') . ' ' .
                        $qty . ' ' .
                        $amount . ' ' .
                        $total_earn . ' ' .
                        ($alloc_status == 1 ? 'complete' : 'assigned')
                    );
                    if (strpos($hay, $needle) === false) {
                        continue;
                    }
                }

                $filtered[] = [
                    'invoice_num'    => !empty($row['invoicenum']) ? $row['invoicenum'] : 'N/A',
                    'qty'            => $qty,
                    'amount'         => $amount,
                    'total_earn'     => $total_earn,
                    'order_status'   => $alloc_status,
                    'payment_status' => $is_paid,
                    'assign_date'    => $row['assign_date'],
                    'completed_date' => $row['completed_date'],
                    'paid_date'      => $paid_date,
                ];
            }

            $recordsFiltered = count($filtered);

            // sort in PHP
            usort($filtered, function($a, $b) use ($order_col, $order_dir) {
                $dir = ($order_dir === 'ASC') ? 1 : -1;
                $va = $a[$order_col] ?? '';
                $vb = $b[$order_col] ?? '';
                if ($va == $vb) return 0;
                return ($va < $vb) ? (-1 * $dir) : (1 * $dir);
            });

            // slice for paging
            $paged = array_slice($filtered, $start, $length == -1 ? null : $length);

            // totals
            $totalEarnSum = 0;
            foreach ($filtered as $f) {
                $totalEarnSum += (float)$f['total_earn'];
            }

            // build rows for DataTables
            $data = [];
            $sr = $start + 1;
            foreach ($paged as $r) {
                $orderStatusHtml = $r['order_status'] == 1
                    ? '<span class="text-success">Complete</span>'
                    : '<span class="text-danger">Assigned</span>';

                $salaryStatusHtml = $r['payment_status'] == 1
                    ? '<span class="badge badge-success">Paid</span>'
                    : '<span class="badge badge-danger">Unpaid</span>';

                $assign    = $r['assign_date']    ? date('j F Y', strtotime($r['assign_date'])) : '';
                $completed = $r['completed_date'] ? date('j F Y', strtotime($r['completed_date'])) : '';
                $paid      = $r['paid_date']      ? date('j F Y', strtotime($r['paid_date'])) : '';

                $data[] = [
                    $sr++,
                    htmlspecialchars($r['invoice_num']),
                    $r['qty'],
                    $r['amount'],
                    $r['total_earn'],
                    $orderStatusHtml,
                    $salaryStatusHtml,
                    $assign,
                    $completed,
                    $paid,
                ];
            }

            $json = [
                'draw'            => intval($req['draw'] ?? 0),
                'recordsTotal'    => $recordsFiltered,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
                'totals'          => [
                    'total_earn' => number_format($totalEarnSum, 2, '.', ''),
                ],
                'total_count'     => $recordsFiltered,
            ];

            echo json_encode($json);

        } catch (Throwable $e) {
            // make sure we ALWAYS return valid JSON
            echo json_encode([
                'draw'            => intval($_REQUEST['draw'] ?? 0),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
            ]);
        }
        break;



    case 'quotes':
        Event::trigger('client/quotes/');
        $ui->assign('_application_menu', 'quotes');
        $ui->assign('_st', $_L['Quotes']);
        $ui->assign('_title', $config['CompanyName'].' - '.$_L['Quotes']);

        $c = Contacts::details();
        $branch_name = get_branch_name($c->branch_id);
        $ui->assign('branch_name', $branch_name);
        $ui->assign('user',$c);

        $d = ORM::for_table('sys_quotes')->where('userid',$c->id)->find_array();

        $ui->assign('d',$d);

        $ui->assign('total_quotes',count($d));

        $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });');

        $ui->display('client_quotes.tpl');


        break;

    case 'transactions':
        Event::trigger('client/transactions/');
        $ui->assign('_application_menu', 'transactions');
        $ui->assign('_st', $_L['Transactions']);
        $ui->assign('_title', $config['CompanyName'].' - '.$_L['Transactions']);

        $c = Contacts::details();

        $cid = $c->id;
        $branch_name = get_branch_name($c->branch_id);
        $ui->assign('branch_name', $branch_name);
        $ui->assign('user',$c);

        $d = ORM::for_table('sys_transactions')
            ->where_any_is(array(
                array('payerid' => $cid),
                array('payeeid' => $cid)))
            ->find_many();
        $ui->assign('d',$d);

        $ti = ORM::for_table('sys_transactions')
            ->where('payerid',$cid)
            ->sum('cr');
        if($ti == ''){
            $ti = '0';
        }
        $ui->assign('ti',$ti);
        $te = ORM::for_table('sys_transactions')
            ->where('payeeid',$cid)
            ->sum('dr');
        if($te == ''){
            $te = '0';
        }

        $ui->assign('total_quotes',count($d));

        $ui->assign('xjq',' $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });');

        $ui->display('client_transactions.tpl');



        break;


    case 'profile':
        Event::trigger('client/profile/');
        $ui->assign('_application_menu', 'profile');
        $ui->assign('_st', $_L['Profile']);
        $ui->assign('_title', $config['CompanyName'].' - '.$_L['Profile']);

        $c = Contacts::details();
        $branch_name = get_branch_name($c->branch_id);
        $ui->assign('branch_name', $branch_name);
        $ui->assign('user',$c);

        $ui->assign('d',$c);

        $ui->assign('countries',Countries::all($c->country));

        $ui->assign('xfooter',Asset::js(array('contacts/client_profile_edit')));

        $cf = ORM::for_table('crm_customfields')->where('ctype','crm')->order_by_asc('id')->find_many();
        $ui->assign('cf',$cf);


        $ui->display('client_profile.tpl');



        break;


    case 'profile_edit_post':
        Event::trigger('client/profile_edit_post/');
        $c = Contacts::details();
        $id = $c->id;
        $d = ORM::for_table('crm_accounts')->find_one($id);
        if($d){

            $account = _post('account');
            $company = _post('company');

            $email = _post('edit_email');




            $phone = _post('phone');
            $address = _post('address');
            $city = _post('city');
            $state = _post('state');
            $zip = _post('zip');
            $country = _post('country');
            $msg = '';

            if($account == ''){
                $msg .= $_L['Account Name is required']. ' <br>';
            }



            if($email != ($d['email'])){
                $f = ORM::for_table('crm_accounts')->where('email',$email)->find_one();

                if($f){
                    $msg .= $_L['Email already exist'].' <br>';
                }
            }
            if(Validator::Email($email) == false){
                $msg .= $_L['Invalid Email'].' <br>';
            }




            $password = _post('password');




            if($msg == ''){


                $d = ORM::for_table('crm_accounts')->find_one($id);
                $d->account = $account;
                $d->company = $company;


                $d->email = $email;

                $d->phone = $phone;
                $d->address = $address;
                $d->city = $city;
                $d->zip = $zip;
                $d->state = $state;
                $d->country = $country;



                if($password != ''){

                    $d->password = Password::_crypt($password);

                }

                $d->save();





                _msglog('s',$_L['account_updated_successfully']);

                echo $id;
            }
            else{
                echo $msg;
            }

        }
        else{
            r2(U.$myCtrl.'/list', 'e', $_L['Account_Not_Found']);
        }


        break;






    case 'logout':
        Event::trigger('client/logout/');
        $c = Contacts::details();

        Contacts::logout_using_token($c->token);


        setcookie('ib_ut', 'expired', 1, "/");

        r2(U.'client/login/','s','You have successfully logged out.');



        break;

    case 'where':

        r2(U.'client/login/');

        break;


    default:
        echo 'action not defined';
}