<?php 

//it will handle all settings
_auth();
$ui->assign('_title', $_L['Reports'].'- '. $config['CompanyName']);
$ui->assign('_st', $_L['Reports']);
$ui->assign('_application_menu', 'reports');
$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);
$mdate = date('Y-m-d');
$tdate = date('Y-m-d', strtotime('today - 30 days'));

//first day of month
$first_day_month = date('Y-m-01');
//
$this_week_start = date('Y-m-d',strtotime( 'previous sunday'));
// 30 days before
$before_30_days = date('Y-m-d', strtotime('today - 30 days'));
//this month
$month_n = date('n');

switch ($action) {
    case 'statement':

        $d = ORM::for_table('sys_accounts')->find_many();
        $ui->assign('d', $d);

        $ui->assign('mdate', $mdate);
        $ui->assign('tdate', $tdate);
        $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min')));
        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'])));
        $ui->assign('xjq', '
 $("#account").select2();
 $("#cats").select2();
  $("#pmethod").select2();
  $("#payer").select2();
$(\'#dp1\').datepicker({
				format: \'yyyy-mm-dd\'
			});
			$(\'#dp2\').datepicker({
				format: \'yyyy-mm-dd\'
			});

 ');
        $ui->display('statement.tpl');


        break;


    case 'statement-view':

        $fdate = _post('fdate');
        $tdate = _post('tdate');
        $account = _post('account');
        $stype = _post('stype');
        $d = ORM::for_table('sys_transactions');
        $d->where('account', $account);
        if($stype == 'credit'){
            $d->where('dr', '0.00');
        }
        elseif($stype == 'debit'){
            $d->where('cr', '0.00');
        }
        else{

        }
        $d->where_gte('date', $fdate);
        $d->where_lte('date', $tdate);
        $d->order_by_desc('id');
        $x =  $d->find_many();

        // Initialize totals as floats
        $total_dr = 0.00;
        $total_cr = 0.00;
    
        foreach ($x as $transaction) {
            $total_dr += (float) $transaction['dr']; // Convert 'dr' to float before summing
            $total_cr += (float) $transaction['cr']; // Convert 'cr' to float before summing
        }
        
        $ui->assign('d',$x);
        $ui->assign('fdate',$fdate);
        $ui->assign('tdate',$tdate);
        $ui->assign('account',$account);
        $ui->assign('stype',$stype);
        $ui->assign('total_dr', $total_dr);
        $ui->assign('total_cr', $total_cr);

        $ui->display('statement-view.tpl');
        break;

    case 'by-date':


        $d = ORM::for_table('sys_transactions')->where('date',$mdate)->order_by_desc('id')->find_many();
        $dr = ORM::for_table('sys_transactions')->where('date',$mdate)->sum('dr');
        if($dr == ''){
            $dr = '0.00';
        }
        $cr = ORM::for_table('sys_transactions')->where('date',$mdate)->sum('cr');
        if($cr == ''){
            $cr = '0.00';
        }
        $ui->assign('d',$d);
        $ui->assign('dr',$dr);
        $ui->assign('cr',$cr);


        $ui->assign('mdate', $mdate);

        if(Ib_I18n::get_code($config['language']) != 'en'){
            $dp_lan = '<script type="text/javascript" src="' . $_theme . '/lib/datepaginator/locale/'.Ib_I18n::get_code($config['language']).'.js"></script>';
            // $x_lan = '$.fn.datepicker.defaults.language = \''.Ib_I18n::get_code($config['language']).'\';';
            $x_lan = '';
        }
        else{

            $dp_lan = '';
            $x_lan = '';
        }

        $ui->assign('xheader', '
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/datepaginator/bootstrap-datepaginator.min.css"/>
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/datepaginator/bootstrap-datepicker.css"/>
');
        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/datepaginator/moment.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/datepaginator/bootstrap-datepicker.js"></script>
'.$dp_lan.'
<script type="text/javascript" src="' . $_theme . '/lib/datepaginator/bootstrap-datepaginator.min.js"></script>
');

        $mdf = Ib_Internal::get_moment_format($config['df']);
        $today = date('Y-m-d');

        $ui->assign('xjq', $x_lan. '

  $(\'#dpx\').datepaginator(
  {

    selectedDate: \''.$today.'\',
    selectedDateFormat:  \'YYYY-MM-DD\',
    textSelected:  "dddd<br/>'.$mdf.'"
}
  );
   $(\'#dpx\').on(\'selectedDateChanged\', function(event, date) {
  // Your logic goes here
 // alert(date);
 $( "#result" ).html( "<h3>'.$_L['Loading'].'.....</h3>" );
 // $(\'#tdate\').text(moment(date).format("dddd, '.$mdf.'"));
 $.get( "'.U.'ajax.date-summary/" + date, function( data ) {
     $( "#result" ).html( data );
     //alert(date);
     // console.log(date);
 });
});



 ');
        $ui->display('reports-by-date.tpl');


        break;

    case 'income':


        $d = ORM::for_table('sys_transactions')->where('type','Income')->limit(20)->order_by_desc('id')->find_many();
        $ui->assign('d',$d);
        $a = ORM::for_table('sys_transactions')->sum('cr');
        if($a == ''){
            $a = '0.00';
        }
        $ui->assign('a',$a);
        $m = ORM::for_table('sys_transactions')->where('type','Income')->where_gte('date',$first_day_month)->where_lte('date',$mdate)->sum('cr');
        if($m == ''){
            $m = '0.00';
        }
        $ui->assign('m',$m);

        $w = ORM::for_table('sys_transactions')->where_gte('date',$this_week_start)->where_lte('date',$mdate)->sum('cr');
        if($w == ''){
            $w = '0.00';
        }
        $ui->assign('w',$w);

        $m3 = ORM::for_table('sys_transactions')->where_gte('date',$before_30_days)->where_lte('date',$mdate)->sum('cr');
        if($m3 == ''){
            $m3 = '0.00';
        }
        $ui->assign('m3',$m3);

        $ui->assign('mdate', $mdate);
//generate graph string
        $array = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $till = $month_n - 1;
        $gstring = '';
        for ($m=0; $m<=$till; $m++) {
            $mnth = $array[$m];
            $cal = ORM::for_table('sys_transactions')->where_gte('date',date('Y-m-d',strtotime("first day of $mnth")))->where_lte('date',date('Y-m-d',strtotime("last day of $mnth")))->sum('cr');
            $gstring .= '["'.ib_lan_get_line($mnth).'",'.$cal.'], ';

        }
        $gstring = rtrim($gstring,',');

        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.categories.js"></script>

');

        $ui->assign('xjq', '

  var data = [ '.$gstring.' ];

		$.plot("#placeholder", [ data ], {
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: "center"
				}
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}
		});

 ');
        $ui->display('reports-income.tpl');


        break;


    case 'expense':


        $d = ORM::for_table('sys_transactions')->where('type','Expense')->limit(20)->order_by_desc('id')->find_many();
        $ui->assign('d',$d);
        $a = ORM::for_table('sys_transactions')->sum('dr');
        if($a == ''){
            $a = '0.00';
        }
        $ui->assign('a',$a);
        $m = ORM::for_table('sys_transactions')->where('type','Expense')->where_gte('date',$first_day_month)->where_lte('date',$mdate)->sum('dr');
        if($m == ''){
            $m = '0.00';
        }
        $ui->assign('m',$m);

        $w = ORM::for_table('sys_transactions')->where_gte('date',$this_week_start)->where_lte('date',$mdate)->sum('dr');
        if($w == ''){
            $w = '0.00';
        }
        $ui->assign('w',$w);

        $m3 = ORM::for_table('sys_transactions')->where_gte('date',$before_30_days)->where_lte('date',$mdate)->sum('dr');
        if($m3 == ''){
            $m3 = '0.00';
        }
        $ui->assign('m3',$m3);

        $ui->assign('mdate', $mdate);
//generate graph string
        $array = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $till = $month_n - 1;
        $gstring = '';
        for ($m=0; $m<=$till; $m++) {
            $mnth = $array[$m];
            $cal = ORM::for_table('sys_transactions')->where_gte('date',date('Y-m-d',strtotime("first day of $mnth")))->where_lte('date',date('Y-m-d',strtotime("last day of $mnth")))->sum('dr');
            $gstring .= '["'.ib_lan_get_line($mnth).'",'.$cal.'], ';

        }
        $gstring = rtrim($gstring,',');

        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.categories.js"></script>

');

        $ui->assign('xjq', '

  var data = [ '.$gstring.' ];

		$.plot("#placeholder", [ data ], {
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: "center"
				}
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}
		});

 ');
        $ui->display('reports-expense.tpl');


        break;


    case 'income-vs-expense':

        $ai = ORM::for_table('sys_transactions')->sum('cr');
        if($ai == ''){
            $ai = '0.00';
        }
        $ui->assign('ai',$ai);
        $mi = ORM::for_table('sys_transactions')->where_gte('date',$first_day_month)->where_lte('date',$mdate)->sum('cr');
        if($mi == ''){
            $mi = '0.00';
        }
        $ui->assign('mi',$mi);

        $wi = ORM::for_table('sys_transactions')->where_gte('date',$this_week_start)->where_lte('date',$mdate)->sum('cr');
        if($wi == ''){
            $wi = '0.00';
        }
        $ui->assign('wi',$wi);

        $m3i = ORM::for_table('sys_transactions')->where_gte('date',$before_30_days)->where_lte('date',$mdate)->sum('cr');
        if($m3i == ''){
            $m3i = '0.00';
        }
        $ui->assign('m3i',$m3i);

        $ae = ORM::for_table('sys_transactions')->sum('dr');
        if($ae == ''){
            $ae = '0.00';
        }
        $ui->assign('ae',$ae);
        $me = ORM::for_table('sys_transactions')->where_gte('date',$first_day_month)->where_lte('date',$mdate)->sum('dr');
        if($me == ''){
            $me = '0.00';
        }
        $ui->assign('me',$me);





        $ui->assign('mdate', $mdate);
        $aime = $ai-$ae;
        $ui->assign('aime', $aime);
        $mime = $mi-$me;
        $ui->assign('mime', $mime);
//generate graph string
        $array = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $till = $month_n - 1;
        $gstring = '';
        $egstring = '';
        for ($m=0; $m<=$till; $m++) {
            $mnth = $array[$m];
            $cal = ORM::for_table('sys_transactions')->where_gte('date',date('Y-m-d',strtotime("first day of $mnth")))->where_lte('date',date('Y-m-d',strtotime("last day of $mnth")))->sum('dr');
            if($cal == ''){
                $cal = '0';
            }
            $egstring .= '["'.$m.'",'.$cal.'], ';
            $cal = ORM::for_table('sys_transactions')->where_gte('date',date('Y-m-d',strtotime("first day of $mnth")))->where_lte('date',date('Y-m-d',strtotime("last day of $mnth")))->sum('cr');
            if($cal == ''){
                $cal = '0';
            }
            $gstring .= '["'.$m.'",'.$cal.'], ';

        }
        $gstring = rtrim($gstring,',');

        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/chart/jquery.flot.categories.js"></script>

');

        $ui->assign('xjq', '



		var d1 = [ '.$gstring.' ];
		var d2 = [ '.$egstring.' ];



		$.plot("#placeholder", [{
			data: d1,
			lines: { show: true, fill: true }
		},  {
			data: d2,
			lines: { show: true, fill: true }
		}]);

 ');
        $ui->display('reports-income-vs-expense.tpl');


        break;

    case 'categories':

        $d = ORM::for_table('sys_cats')->find_many();
        $ui->assign('d', $d);

        $ui->assign('mdate', $mdate);
        $ui->assign('tdate', $tdate);
        $ui->assign('xheader', '
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/datepicker/css/datepicker.css"/>
');
        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/select2/select2.min.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/datepicker/js/bootstrap-datepicker.js"></script>
');
        $ui->assign('xjq', '

 $("#cat").select2();

$(\'#dp1\').datepicker({
				format: \'yyyy-mm-dd\'
			});
			$(\'#dp2\').datepicker({
				format: \'yyyy-mm-dd\'
			});

 ');
        $ui->display('reports-categories.tpl');


        break;


    case 'category-view':

        $fdate = _post('fdate');
        $tdate = _post('tdate');
        $cat = _post('cat');

        $d = ORM::for_table('sys_transactions');
        $d->where('category', $cat);

        $d->where_gte('date', $fdate);
        $d->where_lte('date', $tdate);
        $d->order_by_desc('id');
        $x =  $d->find_many();

        $ui->assign('d',$x);
        $ui->assign('fdate',$fdate);
        $ui->assign('tdate',$tdate);


        $ui->display('report-common.tpl');
        break;

    case 'payees':

        $d = ORM::for_table('sys_payee')->find_many();
        $ui->assign('d', $d);

        $ui->assign('mdate', $mdate);
        $ui->assign('tdate', $tdate);
        $ui->assign('xheader', '
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/datepicker/css/datepicker.css"/>
');
        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/select2/select2.min.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/datepicker/js/bootstrap-datepicker.js"></script>
');
        $ui->assign('xjq', '

 $("#payee").select2();

$(\'#dp1\').datepicker({
				format: \'yyyy-mm-dd\'
			});
			$(\'#dp2\').datepicker({
				format: \'yyyy-mm-dd\'
			});

 ');
        $ui->display('reports-payees.tpl');


        break;


    case 'payees-view':

        $fdate = _post('fdate');
        $tdate = _post('tdate');
        $payee = _post('payee');

        $d = ORM::for_table('sys_transactions');
        $d->where('payee', $payee);

        $d->where_gte('date', $fdate);
        $d->where_lte('date', $tdate);
        $d->order_by_desc('id');
        $x =  $d->find_many();

        $ui->assign('d',$x);
        $ui->assign('fdate',$fdate);
        $ui->assign('tdate',$tdate);


        $ui->display('report-common.tpl');
        break;

    case 'payers':

        $d = ORM::for_table('sys_payers')->find_many();
        $ui->assign('d', $d);

        $ui->assign('mdate', $mdate);
        $ui->assign('tdate', $tdate);
        $ui->assign('xheader', '
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/datepicker/css/datepicker.css"/>
');
        $ui->assign('xfooter', '
<script type="text/javascript" src="' . $_theme . '/lib/select2/select2.min.js"></script>
<script type="text/javascript" src="' . $_theme . '/lib/datepicker/js/bootstrap-datepicker.js"></script>
');
        $ui->assign('xjq', '

 $("#payer").select2();

$(\'#dp1\').datepicker({
				format: \'yyyy-mm-dd\'
			});
			$(\'#dp2\').datepicker({
				format: \'yyyy-mm-dd\'
			});

 ');
        $ui->display('reports-payers.tpl');


        break;


    case 'payer-view':

        $fdate = _post('fdate');
        $tdate = _post('tdate');
        $payer = _post('payer');

        $d = ORM::for_table('sys_transactions');
        $d->where('payer', $payer);

        $d->where_gte('date', $fdate);
        $d->where_lte('date', $tdate);
        $d->order_by_desc('id');
        $x =  $d->find_many();

        $ui->assign('d',$x);
        $ui->assign('fdate',$fdate);
        $ui->assign('tdate',$tdate);


        $ui->display('report-common.tpl');
        break;





    case 'cats':

        $ui->assign('xheader', '
<link href="'.APP_URL.'/ui/lib/c3/c3.min.css" rel="stylesheet" type="text/css">
');

        $ui->assign('xfooter', '
<script type="text/javascript" src="'.APP_URL.'/ui/lib/c3/d3.min.js"></script>
<script type="text/javascript" src="'.APP_URL.'/ui/lib/c3/c3.min.js"></script>

');

        $ui->assign('xjq', '

var chart = c3.generate({
    bindto: \'#chart\',
    data: {
	columns: [

		[\''.$_L['Income'].'\', \'0\','.$d1i.','.$d2i.', '.$d3i.', '.$d4i.', '.$d5i.', '.$d6i.', '.$d7i.', '.$d8i.', '.$d9i.', '.$d10i.', '.$d11i.', '.$d12i.', '.$d13i.', '.$d14i.', '.$d15i.', '.$d16i.', '.$d17i.', '.$d18i.', '.$d19i.', '.$d20i.', '.$d21i.', '.$d22i.', '.$d23i.', '.$d24i.', '.$d25i.', '.$d26i.', '.$d27i.', '.$d28i.', '.$d29i.', '.$d30i.', '.$d31i.'],
		[\''.$_L['Expense'].'\', \'0\','.$d1e.','.$d2e.', '.$d3e.', '.$d4e.', '.$d5e.', '.$d6e.', '.$d7e.', '.$d8e.', '.$d9e.', '.$d10e.', '.$d11e.', '.$d12e.', '.$d13e.', '.$d14e.', '.$d15e.', '.$d16e.', '.$d17e.', '.$d18e.', '.$d19e.', '.$d20e.', '.$d21e.', '.$d22e.', '.$d23e.', '.$d24e.', '.$d25e.', '.$d26e.', '.$d27e.', '.$d28e.', '.$d29e.', '.$d30e.', '.$d31e.']
	],
        type: \'area-spline\',
         colors: {
            '.$_L['Income'].': \'#23c6c8\',
            '.$_L['Expense'].': \'#ed5565\'
        }
    }

});

var dchart = c3.generate({
    bindto: \'#dchart\',
    data: {
        columns: [
            [\''.$_L['Income'].'\', '.$mi.'],
            [\''.$_L['Expense'].'\', '.$me.'],
        ],
        type : \'donut\',
        colors: {
            '.$_L['Income'].': \'#23c6c8\',
            '.$_L['Expense'].': \'#ed5565\'
        }
    },
    donut: {
        title: "'.$_L['Income_Vs_Expense'].'"
    }
});

    $("#set_goal").click(function (e) {
        e.preventDefault();

        bootbox.prompt({
            title: "'.$_L['Set New Goal for Net Worth'].'",
            value: "'.$goal.'",
            buttons: {
        \'cancel\': {
            label: \''.$_L['Cancel'].'\'
        },
        \'confirm\': {
            label: \''.$_L['OK'].'\'
        }
    },
            callback: function(result) {
                if (result === null) {

                } else {
                   // alert(result);
                     $.post( "'.U.'settings/networth_goal/", { goal: result })
        .done(function( data ) {
            location.reload();
        });
                }
            }
        });

    });
 ');

    break;
				
    case 'filter':

        $ui->assign('xheader', Asset::css(array('dt/dataTables.bootstrap')));

        $ui->assign('xfooter', Asset::js(array('dt/jquery.uniform.min','s2/js/select2.min','dp/dist/datepicker.min','dt/jquery.dataTables.min','dt/datatable','dt/dataTables.bootstrap','m','tr_filter')));

        $ui->assign('xjq', ' TableAjax.init(); ');

        $ui->display('tr_filter.tpl');


    break;
				
		case 'gst-reports':
		
			Event::trigger('reports/gst-reports/');
			$invoices = ORM::for_table('sys_invoices')->find_many();		

			foreach($invoices as &$row ){
					$invItem = ORM::for_table('sys_invoiceitems')->where('invoiceid', $row['id'])->find_many();
					$sgst = 0.00;
					$igst = 0.00;
					foreach($invItem as $inv){
							$tax = ORM::for_table('sys_tax')->find_one($inv['tax_id']);
							if($tax['taxtype'] == 'GST'){
								$sgst = $sgst + $inv['taxamount'];
							}else{             
								$igst = $igst + $inv['taxamount'];
							}
					}
					$row['CGST'] = Finance::amount_fix($sgst/2);
					$row['SGST'] = Finance::amount_fix($sgst/2);
					$row['IGST'] = Finance::amount_fix($igst);
            }				//$company = ORM::for_table('sys_accounts')->find_many();	
            $ac = ORM::for_table('crm_accounts')->where('gid', 20)->find_many();			
			//var_dump($invoices[2]);exit;
      $ui->assign('invoices', $invoices);      
      // $ui->assign('company', $company); 
      $ui->assign('ac', $ac); 
			$ui->assign('<div class="btn-group pull-right" style="padding-right: 10px;">
				<a class="btn btn-success btn-xs" href="#" id="csv" style="box-shadow: none;" title="Export Table"><i class="fa fa-download"></i>Export</a>
			</div>');
			
      $ui->assign('xheader', Asset::css(array('css/daterangepicker','s2/css/select2.min')));
      $ui->assign('xfooter', Asset::js(array('daterangepicker','moment.min','gst-reports','s2/js/select2.min')));
      
			$ui->display('gst-reports.tpl');

    break;
		
        case 'gst-reports-ajax':

			$start = _post('start');
			$end = _post('end');			$company = _post('company');
            //$invoices = ORM::for_table('sys_invoices')->where('company_id',$company)->where_gte('date', $start)->where_lte('date', $end)->find_many();
            if(empty($company))
            {
                $invoices = ORM::for_table('sys_invoices')->where_gte('date', $start)->where_lte('date', $end)->find_many();
            }
            else
            {
                $invoices = ORM::for_table('sys_invoices')->where('userid', $company)->where_gte('date', $start)->where_lte('date', $end)->find_many();
            }
            		
					
			foreach($invoices as &$row ){
					$invItem = ORM::for_table('sys_invoiceitems')->where('invoiceid', $row['id'])->find_many();
					$sgst = 0.00;
					$igst = 0.00;
					foreach($invItem as $inv){
							$tax = ORM::for_table('sys_tax')->find_one($inv['tax_id']);
							if($tax['taxtype'] == 'GST'){
								$sgst = $sgst + $inv['taxamount'];
							}else{             
								$igst = $igst + $inv['taxamount'];
							}
					}
					$row['CGST'] = Finance::amount_fix($sgst/2);
					$row['SGST'] = Finance::amount_fix($sgst/2);
					$row['IGST'] = Finance::amount_fix($igst);
			}
			//var_dump($invoices[2]);exit;
      $ui->assign('invoices', $invoices); 
			$ui->assign('<div class="btn-group pull-right" style="padding-right: 10px;">
				<a class="btn btn-success btn-xs" href="'.U.'invoices/export_csv/'.'" style="box-shadow: none;"><i class="fa fa-download"></i>CSV</a>
			</div>');

			$ui->display('gst-reports-ajax.tpl');

    break;
			case 'send_pdf':
		global $config,$_L;
			 $toname = 'Imran';
		 $email = 'imran.makent@gmail.com';
		 			$message = 'GST Reports';
			$subject = 'GST Reports';
			foreach($_POST['invoice_pdf'] as $inv_pdf){
				$id = get_type_by_id('sys_invoices', 'invoicenum', $inv_pdf, 'id');
				$cid = get_type_by_id('sys_invoices', 'invoicenum', $inv_pdf, 'userid');
				$in = $inv_pdf;
				$d = ORM::for_table('sys_invoices')->find_one($id);	
				if ($d) {
					/* //find all activity for this user */
					$items = ORM::for_table('sys_invoiceitems')->where('invoiceid', $id)->order_by_asc('id')->find_many();
					$trs_c = ORM::for_table('sys_transactions')->where('iid', $id)->count();
					$trs = ORM::for_table('sys_transactions')->where('iid', $id)->order_by_desc('id')->find_many();
					$comp = ORM::for_table('sys_accounts')->find_one($d['company_id']);
						/* //find the user */
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

//            $i_due = number_format($i_due,2,$config['dec_point'],$config['thousands_sep']);
            $cf = ORM::for_table('crm_customfields')->where('showinvoice', 'Yes')->order_by_asc('id')->find_many();



            if($d['cn'] != ''){
                $dispid = $d['cn'];
            }
            else{
                $dispid = $d['id'];
            }

            $in = $d['invoicenum']/* .$dispid */;

            define('_MPDF_PATH','application/lib/mpdf/');

            require_once('application/lib/mpdf/mpdf.php');

            $pdf_c = '';
            $ib_w_font = 'dejavusanscondensed';
            if($config['pdf_font'] == 'default'){
                $pdf_c = 'c';
                $ib_w_font = 'Helvetica';
            }
            $mpdf=new mPDF($pdf_c,'A4','','',5,5,2,5,10,10);
      /*     $mpdf->SetProtection(array('print')); */
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

                $mpdf->Output('application/storage/temp/Invoice_'.$in.'.pdf', 'F'); # D

  




        }
    $attachment_path[] = 'application/storage/temp/Invoice_'.$in.'.pdf';
		$attachment_file[] = 'Invoice_'.$in.'.pdf';
			}
		Notify_Email::_send($toname, $email, $subject, $message, $cid, $id, $cc, $bcc, $attachment_path, $attachment_file); 


	 
			break;
    default:
        echo 'action not defined';
				
}