<?php
_auth();
$ui->assign('_title', $_L['Transactions'].'- '. $config['CompanyName']);
$ui->assign('_st', $_L['Transactions']);
$ui->assign('_application_menu', 'transactions');

$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);
$mdate = date('Y-m-d');

/* //js var */

$ui->assign('jsvar', '
_L[\'Working\'] = \''.$_L['Working'].'\';
_L[\'Submit\'] = \''.$_L['Submit'].'\';
 ');

Event::trigger('transactions');

switch ($action) {
    case 'deposit':
        // Permission check (keep)
        if(!has_access($user->roleid, 'transactions')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
        Event::trigger('transactions/deposit/');


        $d = ORM::for_table('sys_accounts')->find_many();
        /* $p = ORM::for_table('sys_payers')->find_many(); */
        // $p = ORM::for_table('crm_accounts')->find_many();
        // $ui->assign('p', $p);
        $ui->assign('d', $d);
        $cats = ORM::for_table('sys_cats')->where('type','Income')->order_by_asc('sorder')->find_many();
        $ui->assign('cats', $cats);
        $pms = ORM::for_table('sys_pmethods')->find_many();
        $ui->assign('pms', $pms);
        $ui->assign('mdate', $mdate);

        $tags = Tags::get_all('Income');
        $ui->assign('tags',$tags);
        /* //        $ui->assign('xheader', '
        //<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/select2/select2.css"/>
        //<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/dp/dist/datepicker.min.css"/>
        //'); */

        $ui->assign('xheader', Asset::css(array('dropzone/dropzone','modal','s2/css/select2.min','dp/dist/datepicker.min')));

        /* //        $ui->assign('xfooter', '
        //<script type="text/javascript" src="' . $_theme . '/lib/select2/select2.min.js"></script>
        //<script type="text/javascript" src="' . $_theme . '/lib/dp/dist/datepicker.min.js"></script>
        //<script type="text/javascript" src="' . $_theme . '/lib/numeric.js"></script>
        //<script type="text/javascript" src="' . $_theme . '/lib/deposit.js"></script>
        //'); */

        $ui->assign('xfooter', Asset::js(array('modal','dropzone/dropzone','s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','deposit')));

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
        /* find latest income */
        // $tr = ORM::for_table('sys_transactions')->where('type','Income')->order_by_desc('id')->limit('20')->find_many();
        $tr = ORM::for_table('sys_transactions')
            ->where('type','Income')
            ->where('branch_id', $user['branch_id'])
            ->order_by_desc('id')
            ->limit(20)
            ->find_many();

        $ui->assign('tr', $tr);
        $ui->display('deposit.tpl');

        break;



    case 'deposit-post':

        Event::trigger('transactions/deposit-post/');

        $account = _post('account');
        $date = _post('date');
        $amount = _post('amount');
        /* @since v2. added support for ',' as decimal separator */
        $amount = Finance::amount_fix($amount);
        $payerid = _post('payer');
        $ref = _post('ref');
        $pmethod = _post('pmethod');
        $cat = _post('cats');
        $branch_id = _post('branch_id');
        $tags = $_POST['tags'];

        /* @since Build 4560. added support file attachments */

        $attachments = _post('attachments');

        if($payerid == ''){
            $payerid = '0';
        }
        $description = _post('description');
        $msg = '';
        
        // Validate date
        if ($date == '') {
            $msg .= 'Date is required.<br>';
        } else {
            // Check if date is in YYYY-MM-DD format and is a valid date
            $d = DateTime::createFromFormat('Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) {
                $msg .= 'Invalid date format. Use YYYY-MM-DD.<br>';
            }
        }
        
        if ($description == '') {
            $msg .= $_L['description_error'] . '<br>';
        }

        if (Validator::Length($account, 100, 1) == false) {
            $msg .= $_L['Choose an Account'].' ' . '<br>';
        }

        if (is_numeric($amount) == false) {
            $msg .= $_L['amount_error'] . '<br>';
        } elseif ((float)$amount < 1) {
            $msg .= 'Amount must be at least 1.<br>';
        }

        if ($msg == '') {

            Tags::save($tags,'Income');

            //find the current balance for this account
            $a = ORM::for_table('sys_accounts')->where('account',$account)->find_one();
            $cbal = $a['balance'];
            $nbal = $cbal+$amount;
            $a->balance=$nbal;
            $a->save();
            $d = ORM::for_table('sys_transactions')->create();
            $d->branch_id = $branch_id;
            $d->account = $account;
            $d->type = 'Income';
            $d->payerid =  $payerid;
            $d->tags =  Arr::arr_to_str($tags);
            $d->amount = $amount;
            $d->category = $cat;
            $d->method = $pmethod;
            $d->ref = $ref;

            $d->description = $description;
            // Build 4560
            $d->attachments = $attachments;
            $d->date = $date;
            $d->datetime = $date.' '.date('H:i:s');
            $d->dr = '0.00';
            $d->cr = $amount;
            $d->bal = $nbal;

            //others
            $d->payer = '';
            $d->payee = '';
            $d->payeeid = '0';
            $d->status = 'Cleared';
            $d->tax = '0.00';
            $d->iid = 0;
            //

            $d->save();
            $tid = $d->id();
            _log('New Deposit: '.$description.' [TrID: '.$tid.' | Amount: '.$amount.']','Admin',$user['id']);
            _msglog('s',$_L['Transaction Added Successfully']);
           echo $tid;
        } else {
           echo $msg;
        }
        break;


        case 'expense-get-customer-invoices':
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
            Event::trigger('transactions/expense-get-customer-invoices/');
            $invoice = ORM::for_table('sys_invoices')->where('userid', _post('id'))->find_many();
            
            $option = '<option value="">Choose Invoice</option>';
            foreach($invoice as $i)
            {
                $option .= '<option value="'.$i['id'].'">'.$i['invoicenum'].'</option>';
            }

            echo $option;

        break;        


    case 'expense':
        // Permission check (keep)
        if(!has_access($user->roleid, 'transactions')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
        
        Event::trigger('transactions/expense/');

        $d = ORM::for_table('sys_accounts')->find_many();
        /*$p = ORM::for_table('crm_accounts')->find_many();
        $ui->assign('p', $p);*/
        //$p = ORM::for_table('crm_accounts')->where('gid', 1)->find_many();
        $v = ORM::for_table('crm_accounts')->where('gid', 2)->find_many();
        $ui->assign('d', $d);
        $ui->assign('v', $v);
        $tags = Tags::get_all('Expense');
        $ui->assign('tags',$tags);
        $cats = ORM::for_table('sys_cats')->where('type','Expense')->order_by_asc('sorder')->find_many();
        $ui->assign('cats', $cats);
        $pms = ORM::for_table('sys_pmethods')->find_many();
        $ui->assign('pms', $pms);
        $ui->assign('mdate', $mdate);

        //        $ui->assign('xheader', '
        //<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/select2/select2.css"/>
        //<link rel="stylesheet" type="text/css" href="' . $_theme . '/lib/dp/dist/datepicker.min.css"/>
        //');

        $ui->assign('xheader', Asset::css(array('dropzone/dropzone','modal','s2/css/select2.min','dp/dist/datepicker.min')));

        //        $ui->assign('xfooter', '
        //<script type="text/javascript" src="' . $_theme . '/lib/select2/select2.min.js"></script>
        //<script type="text/javascript" src="' . $_theme . '/lib/dp/dist/datepicker.min.js"></script>
        //<script type="text/javascript" src="' . $_theme . '/lib/numeric.js"></script>
        //<script type="text/javascript" src="' . $_theme . '/lib/expense.js"></script>
        //');

        $ui->assign('xfooter', Asset::js(array('modal','dropzone/dropzone','s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','expense')));

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

        // find latest Expense (admins see all branches)
        $expenseQuery = ORM::for_table('sys_transactions')
            ->where('type','Expense')
            ->order_by_desc('id')
            ->limit(20);

        if($user['roleid'] != 0){
            $expenseQuery->where('branch_id', $user['branch_id']);
        }

        $tr = $expenseQuery->find_many();

        $ui->assign('tr', $tr);

        $employee_id = _get('employee_id');
        $ui->assign('employee_id', $employee_id);
                
        if($employee_id){
            $employee = ORM::for_table('crm_accounts')->find_one($employee_id);
            $ui->assign('employee', $employee);
        }

        $ui->display('expense.tpl');

        break;



    case 'expense-post':

        Event::trigger('transactions/expense-post/');

        $account = _post('account');
        $date = _post('date');
        $amount = _post('amount');
        $amount = Finance::amount_fix($amount);
        $payee = _post('payee');
        $ref = _post('ref');
        $pmethod = _post('pmethod');
        $cat = _post('cats');
        $tags = $_POST['tags'];
        $invoice_id = _post('invoice_id') ? _post('invoice_id') : 0;
        $vendor_id = _post('vendor_id');
        $branch_id = _post('branch_id');
        $attachments = _post('attachments');

        // Get timesheet IDs from the post request
        $timesheet_ids = isset($_POST['timesheet_ids']) ? $_POST['timesheet_ids'] : '';

        if(!is_numeric($payee)){
            $payee = '0';
        }

        $contac = get_type_by_id('crm_accounts', 'id', $payee, 'account');
        $contac = $contac ? ' ('.$contac.')' : '';
        $description = _post('description').$contac;
        $msg = '';
        if ($description == '') {
            $msg .= $_L['description_error'] . '<br>';
        }

        if (Validator::Length($account, 100, 1) == false) {
            $msg .= $_L['Choose an Account'].' ' . '<br>';
        }


        if (is_numeric($amount) == false) {
            $msg .= $_L['amount_error'] . '<br>';
        }

        if ($msg == '') {

            Tags::save($tags,'Expense');

            //find the current balance for this account
            $a = ORM::for_table('sys_accounts')->where('account',$account)->find_one();
            $cbal = $a['balance'];
            $nbal = $cbal-$amount;
            $a->balance=$nbal;
            $a->save();
            $d = ORM::for_table('sys_transactions')->create();
            $d->branch_id = $branch_id;
            $d->account = $account;
            $d->type = 'Expense';
            $d->payeeid =  $payee;
            $d->tags =  Arr::arr_to_str($tags);
            $d->amount = $amount;
            $d->category = $cat;
            $d->method = $pmethod;
            $d->ref = $ref;

            $d->description = $description;
            // Build 4560
            $d->attachments = $attachments;
            $d->date = $date;
            $d->datetime = $date.' '.date('H:i:s');
            $d->dr = $amount;
            $d->cr = '0.00';
            $d->bal = $nbal;
            //others
            $d->payer = '';
            $d->payee = '';
            $d->payerid = '0';
            $d->status = 'Cleared';
            $d->tax = '0.00';
            $d->iid = $invoice_id; //0;
            $d->vendor_id = $vendor_id;

            $d->save();
            $tid = $d->id();
            // echo 'Received timesheet_ids: ' . $timesheet_ids . '<br>';
            
            if (!empty($timesheet_ids)) {
                $idsArray = array_filter(array_map('trim', explode(',', $timesheet_ids)));
            
                // echo 'Parsed IDs: ' . implode(',', $idsArray) . '<br>';
            
                $timesheets = ORM::for_table('crm_timesheet')->where_in('id', $idsArray)->find_many();
            
                // echo 'Found records: ' . count($timesheets) . '<br>';
                $today = date('Y-m-d H:i:s');
                foreach ($timesheets as $ts) {
                    // echo 'Updating ID: ' . $ts->id . '<br>';
                    $ts->set('transaction_id', $tid);
                    $ts->set('paid_date', $today);
                    $ts->save();
                }
            
                // Show missing IDs
                /*$existing_ids = array_map(function($ts) {
                    return $ts->id;
                }, $timesheets);
            
                $missing_ids = array_diff($idsArray, $existing_ids);
                if (!empty($missing_ids)) {
                    echo 'Timesheet IDs not found in DB: ' . implode(',', $missing_ids) . '<br>';
                } else {
                    echo 'All timesheet IDs found and updated successfully.<br>';
                }*/
            } 
            /*else {
                echo 'timesheet_ids is empty or not set.<br>';
            }
            exit();
            */
            
            // **Update crm_timesheet if timesheet_ids are provided**
            /*if (!empty($timesheet_ids)) {
                $idsArray = explode(',', $timesheet_ids); // Convert comma-separated values to an array
    
                ORM::for_table('crm_timesheet')
                    ->where_in('id', $idsArray)
                    ->find_many()
                    ->set('transaction_id', $tid)
                    ->save();
            }*/
            
            _log('New Expense: '.$description.' [TrID: '.$tid.' | Amount: '.$amount.']','Admin',$user['id']);
            _msglog('s',$_L['Transaction Added Successfully']);
            echo $tid;
        } else {
            echo $msg;
        }
        break;

    case 'transfer':

        Event::trigger('transactions/transfer/');


        $d = ORM::for_table('sys_accounts')->find_many();
        $ui->assign('p', $d);
        $ui->assign('d', $d);

        $pms = ORM::for_table('sys_pmethods')->find_many();
        $ui->assign('pms', $pms);
        $ui->assign('mdate', $mdate);
        $tags = Tags::get_all('Transfer');
        $ui->assign('tags',$tags);
        $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min')));

        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','transfer')));

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
        //find latest income
        $tr = ORM::for_table('sys_transactions')->where('type','Transfer')->order_by_desc('id')->limit('20')->find_many();
        $ui->assign('tr', $tr);
        $ui->display('transfer.tpl');

        break;



    case 'transfer-post':

        Event::trigger('transactions/transfer-post/');


        $faccount = _post('faccount');
        $taccount = _post('taccount');
        $date = _post('date');
        $amount = _post('amount');
        $amount = Finance::amount_fix($amount);
        $pmethod = _post('pmethod');
        $ref = _post('ref');

        $description = _post('description');
        $msg = '';
        if (Validator::Length($faccount, 100, 2) == false) {
            $msg .= $_L['Choose an Account'].' ' . '<br>';
        }

        if (Validator::Length($taccount, 100, 2) == false) {
            $msg .= $_L['Choose the Traget Account'].' ' . '<br>';
        }

        if ($description == '') {
            $msg .= $_L['description_error'] . '<br>';
        }

        if (is_numeric($amount) == false) {
            $msg .= $_L['amount_error'] . '<br>';
        }

        //check if from account & target account is same

        if($faccount == $taccount){
            $msg .= $_L['same_account_error'] . '<br>';
        }

        $tags = $_POST['tags'];

        Tags::save($tags,'Transfer');


        if ($msg == '') {
            $a = ORM::for_table('sys_accounts')->where('account',$faccount)->find_one();
            $cbal = $a['balance'];
            $nbal = $cbal-$amount;
            $a->balance=$nbal;
            $a->save();
            $a = ORM::for_table('sys_accounts')->where('account',$taccount)->find_one();
            $cbal = $a['balance'];
            $tnbal = $cbal+$amount;
            $a->balance=$tnbal;
            $a->save();
            $d = ORM::for_table('sys_transactions')->create();
            $d->account = $faccount;
            $d->type = 'Transfer';

            $d->amount = $amount;

            $d->method = $pmethod;
            $d->ref = $ref;
            $d->tags = Arr::arr_to_str($tags);

            $d->description = $description;
            $d->date = $date;
            $d->dr = $amount;
            $d->cr = '0.00';
            $d->bal = $nbal;

            //others
            $d->payer = '';
            $d->payee = '';
            $d->payerid = '0';
            $d->payeeid = '0';
            $d->category = '';
            $d->status = 'Cleared';
            $d->tax = '0.00';
            $d->iid = 0;
            //

            $d->save();
            //transaction for target account
            $d = ORM::for_table('sys_transactions')->create();
            $d->account = $taccount;
            $d->type = 'Transfer';

            $d->amount = $amount;

            $d->method = $pmethod;
            $d->ref = $ref;
            $d->tags = Arr::arr_to_str($tags);
            $d->description = $description;
            $d->date = $date;
            $d->dr = '0.00';
            $d->cr = $amount;
            $d->bal = $tnbal;

            //others
            $d->payer = '';
            $d->payee = '';
            $d->payerid = '0';
            $d->payeeid = '0';
            $d->category = '';
            $d->status = 'Cleared';
            $d->tax = '0.00';
            $d->iid = 0;
            //

            $d->save();
            _msglog('s',$_L['Transaction Added Successfully']);
           echo '1';
        } else {
            echo $msg;
        }
        break;
case 'set_view_mode':

        Event::trigger('transactions/set_view_mode/');

//        if(isset($routes['2']) AND ($routes['2'] != 'tbl')){
//            $mode = 'card';
//        }
//        else{
//            $mode = 'tbl';
//        }

        if(isset($routes[2]) AND ($routes[2] != '')){
            $mode = $routes['2'];
        }

        else{
            $mode = 'tbl';
        }

        $available_mode = array("tbl", "card", "search");
        if (in_array($mode, $available_mode)) {

            update_option('transaction_set_view_mode',$mode);

        }

        r2(U.'transactions/list/');

        break;


    case 'list':

        if(!(has_access($user->roleid, 'transactions') || has_access($user->roleid, 'reports'))) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }

        Event::trigger('transactions/list/');

        // Load branches for dropdown
        $branches = ORM::for_table('sys_accounts')
            ->select('id')
            ->select('alias')
            ->find_array();

        $ui->assign('branches', $branches);
        $ui->assign('xheader', Asset::css(['datatables.min', 'buttons.dataTables.min']));
        $ui->assign('xfooter', Asset::js(['datatables.min', 'dataTables.buttons.min', 'buttons.print.min', 'list-transaction']));

        $ui->display('transactions2.tpl');
        break;


    case 'list-datatable':
        $request = $_REQUEST;

        $columns = [
            0 => 'date',
            1 => 'branch_alias',
            2 => 'type',
            3 => 'description',
            4 => 'method',
            5 => 'category',
            6 => 'amount'
        ];

        // total records (no filters)
        $totalData = (int) ORM::for_table('sys_transactions')->count();

        $length = isset($request['length']) ? (int)$request['length'] : 10;
        $start  = isset($request['start'])  ? max(0, (int)$request['start']) : 0;

        // safe: pick order column only if provided and valid
        $order_index = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 0;
        $order = isset($columns[$order_index]) ? $columns[$order_index] : 'date';
        $dir   = (isset($request['order'][0]['dir']) && strtolower($request['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

        // Build base query (do not execute yet)
        $base_q = ORM::for_table('sys_transactions')->table_alias('t')
            ->select('t.*')
            ->select('b.alias', 'branch_alias')
            ->join('sys_accounts', ['t.branch_id', '=', 'b.id'], 'b');

        // Apply search filter (if any)
        if (!empty($request['search']['value'])) {
            $search = "%" . $request['search']['value'] . "%";
            $base_q->where_raw(
                '(t.description LIKE ? OR t.type LIKE ? OR t.method LIKE ? OR t.category LIKE ? OR b.alias LIKE ?)',
                [$search, $search, $search, $search, $search]
            );
        }

        // Date filter
        if (!empty($request['date_from']) && !empty($request['date_to'])) {
            $base_q->where_raw('t.date BETWEEN ? AND ?', [$request['date_from'], $request['date_to']]);
        }

        // Other filters
        if (!empty($request['branch_id'])) {
            $base_q->where('t.branch_id', $request['branch_id']);
        }
        if (!empty($request['type'])) {
            $base_q->where('t.type', $request['type']);
        }
        if (!empty($request['method'])) {
            $base_q->where('t.method', $request['method']);
        }
        if (!empty($request['category'])) {
            $base_q->where('t.category', $request['category']);
        }
        if (!empty($request['account_id'])) {
            $base_q->where('t.account_id', $request['account_id']);
        }

        // Clone for the count (so data query stays untouched)
        $count_q = clone $base_q;
        $totalFiltered = (int) $count_q->count();

        // Clone for fetch
        $data_q = clone $base_q;

        // apply ordering BEFORE limit/offset
        $data_q->order_by_expr("$order $dir");

        // pagination: only apply limit/offset when length != -1 (DataTables "All" = -1)
        if ($length != -1) {
            $data_q->offset($start)->limit($length);
        }

        // fetch rows as array
        $rows = $data_q->find_array();

        // prepare response
        $data = [];

        foreach ($rows as $r) {
            $amount = (float)$r['amount'];
            // if ($r['type'] === 'Income') $page_income += $amount;
            // if ($r['type'] === 'Expense') $page_expense += $amount;

            $nested = [];
            $nested[] = date($_c['df'], strtotime($r['date']));
            $nested[] = $r['branch_alias'];
            $nested[] = $r['type'];
            $nested[] = $r['description'];
            $nested[] = $r['method'];
            $nested[] = $r['category'];
            $nested[] = '<span class="amount">'.number_format($amount, 2, '.', '').'</span>';
            $nested[] = '<a href="'.U.'transactions/manage/'.$r['id'].'" class="btn btn-xs btn-primary">'.$_L['Manage'].'</a>';

            $data[] = $nested;
        }

/*
        // Clone again for totals (no limit/offset, just filters)
        $sum_q = clone $base_q;

        // Calculate totals for all filtered records
        $sum_income = (float) $sum_q->where('t.type', 'Income')->sum('amount');
        $sum_expense = (float) $sum_q->where('t.type', 'Expense')->sum('amount');

        // Reset query for Expense (because where() stays)
        $sum_q = clone $base_q;
        $sum_expense = (float) $sum_q->where('t.type', 'Expense')->sum('amount');

        $sum_balance = $sum_income - $sum_expense;

        // --- Page totals (just for current display rows) ---
        // $page_income = 0.0;
        // $page_expense = 0.0;
        
        // $page_balance = $page_income - $page_expense;
        $json_data = [
            "draw"            => intval($request['draw'] ?? 0),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
            "totals" => [
                // "page" => [
                //     "income"  => number_format($page_income, 2, '.', ''),
                //     "expense" => number_format($page_expense, 2, '.', ''),
                //     "balance" => number_format($page_balance, 2, '.', '')
                // ],
                "filtered" => [
                    "income"  => number_format($sum_income, 2, '.', ''),
                    "expense" => number_format($sum_expense, 2, '.', ''),
                    "balance" => number_format($sum_balance, 2, '.', '')
                ]
            ]
        ];
*/

        // ---------- FILTERED TOTALS ----------
        // Use the same filters (base_q) with *no* pagination/order to compute sums.
        // NOTE: We'll do small helper closures to keep clones clean.

        $qr_methods = [
            'qr', 'upi', 'upi-qr', 'bhim', 'gpay', 'google pay', 'phonepe', 'paytm', 'paytm qr', 'amazon pay', 'qr-code'
        ];
        // Build IN list string for where_raw
        $qr_in_sql = implode(',', array_fill(0, count($qr_methods), '?'));
        $qr_in_params = $qr_methods; // already lowercase

        $sum_base = clone $base_q;

        // Overall income/expense totals
        $sum_income_q = clone $sum_base;
        $sum_income   = (float) $sum_income_q->where('t.type', 'Income')->sum('amount');

        $sum_expense_q = clone $sum_base;
        $sum_expense   = (float) $sum_expense_q->where('t.type', 'Expense')->sum('amount');

        $sum_balance   = $sum_income - $sum_expense;

        // ---- Income by method buckets ----
        // Income Cash
        $inc_cash_q = clone $sum_base;
        $income_cash = (float) $inc_cash_q
            ->where('t.type', 'Income')
            ->where_raw('LOWER(COALESCE(t.method,"")) = ?', ['cash'])
            ->sum('amount');

        // Income QR (any QR alias)
        $inc_qr_q = clone $sum_base;
        $income_qr = (float) $inc_qr_q
            ->where('t.type', 'Income')
            ->where_raw('LOWER(COALESCE(t.method,"")) IN ('.$qr_in_sql.')', $qr_in_params)
            ->sum('amount');

        // Income Other = total income - cash - qr
        $income_other = max(0.0, $sum_income - $income_cash - $income_qr);

        // ---- Expense by method buckets ----
        $exp_cash_q = clone $sum_base;
        $expense_cash = (float) $exp_cash_q
            ->where('t.type', 'Expense')
            ->where_raw('LOWER(COALESCE(t.method,"")) = ?', ['cash'])
            ->sum('amount');

        $exp_qr_q = clone $sum_base;
        $expense_qr = (float) $exp_qr_q
            ->where('t.type', 'Expense')
            ->where_raw('LOWER(COALESCE(t.method,"")) IN ('.$qr_in_sql.')', $qr_in_params)
            ->sum('amount');

        $expense_other = max(0.0, $sum_expense - $expense_cash - $expense_qr);

        $json_data = [
            "draw"            => intval($request['draw'] ?? 0),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
            "totals" => [
                "filtered" => [
                    "income"  => number_format($sum_income, 2, '.', ''),
                    "expense" => number_format($sum_expense, 2, '.', ''),
                    "balance" => number_format($sum_balance, 2, '.', ''),
                    "by_method" => [
                        "income" => [
                            "cash"  => number_format($income_cash, 2, '.', ''),
                            "qr"    => number_format($income_qr, 2, '.', ''),
                            "other" => number_format($income_other, 2, '.', '')
                        ],
                        "expense" => [
                            "cash"  => number_format($expense_cash, 2, '.', ''),
                            "qr"    => number_format($expense_qr, 2, '.', ''),
                            "other" => number_format($expense_other, 2, '.', '')
                        ]
                    ]
                ]
            ]
        ];
        header('Content-Type: application/json');
        echo json_encode($json_data);
        break;




    /*case 'list':

        Event::trigger('transactions/list/');


        $paginator = Paginator::bootstrap('sys_transactions');
        $d = ORM::for_table('sys_transactions')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('updated_at')->find_many();
        $ui->assign('d',$d);
        $ui->assign('paginator',$paginator);

        $ui->assign('_st', $_L['Transactions'].'<div class="btn-group pull-right" style="padding-right: 10px;">
        <a class="btn btn-success btn-xs" href="'.U.'transactions/export_csv/'.'" style="box-shadow: none;"><i class="fa fa-download"></i></a>
        </div>');

        $ui->assign('xfooter',Asset::js(array('numeric','datatables.min','list-transaction')));

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

        $ui->display('transactions2.tpl');
        // $ui->display('transactions.tpl');
        break;*/
				
    case 'list-proforma':

        Event::trigger('transactions/list-proforma/');


        $paginator = Paginator::bootstrap('sys_proforma_transactions');
        $d = ORM::for_table('sys_proforma_transactions')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('updated_at')->find_many();
        $ui->assign('d',$d);
        $ui->assign('paginator',$paginator);

        $ui->assign('_st', $_L['Transactions'].'<div class="btn-group pull-right" style="padding-right: 10px;">
  <a class="btn btn-success btn-xs" href="'.U.'transactions/export_csv/'.'" style="box-shadow: none;"><i class="fa fa-download"></i></a>
</div>');

        $ui->assign('xfooter',Asset::js(array('numeric','datatables.min','list-transaction')));

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

        $ui->display('proforma_transactions.tpl');
        break;

    case 'a':

        Event::trigger('transactions/a/');

        $d = ORM::for_table('sys_accounts')->find_many();
        // $p = ORM::for_table('sys_payers')->find_many();
        $p = ORM::for_table('crm_accounts')->find_many();
        $ui->assign('p', $p);
        $ui->assign('d', $d);
        $cats = ORM::for_table('sys_cats')->where('type','Income')->order_by_asc('sorder')->find_many();
        $ui->assign('cats', $cats);
        $pms = ORM::for_table('sys_pmethods')->find_many();
        $ui->assign('pms', $pms);
        $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min','dt/media/css/jquery.dataTables.min','modal','css/dta')));

        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','modal','dt/media/js/jquery.dataTables.min','js/dta','js/tra')));

        $ui->assign('xjq', '


 ');

        $ui->display('tra.tpl');

        break;

    case 'tr_ajax':

//        $filter = '';
//
//        $d = ORM::for_table('sys_transactions');
//
//
//        if(isset($_POST['order_id']) AND ($_POST['order_id'] != '')){
//            // $iTotalRecords = ORM::for_table('flexi_req')->where('id',$_POST['order_id'])->count('id');
//            $oid = _post('order_id');
//            //  $filter .= "AND id='$oid' ";
//            $d->where('id',$oid);
//        }
//
//        if(isset($_POST['sender']) AND ($_POST['sender'] != '')){
//            $sender = _post('sender');
//            // $filter .= "AND sender='$sender'";
//            $d->where_like('sender', "%$sender%");
//        }
//
//        if(isset($_POST['receiver']) AND ($_POST['receiver'] != '')){
//            $receiver = _post('receiver');
//            // $filter .= "AND receiver='$receiver' ";
//            $d->where_like('receiver', "%$receiver%");
//        }
//
//        if(isset($_POST['sdate']) AND ($_POST['sdate'] != '') AND isset($_POST['tdate']) AND ($_POST['tdate'] != '')){
//            $sdate = _post('sdate');
//            $tdate = _post('tdate');
//            // $filter .= "AND reqlogtime >= '$sdate 00:00:00' AND reqlogtime <= '$tdate 23:59:59'";
//            $d->where_gte('reqlogtime', "$sdate 00:00:00");
//            $d->where_lte('reqlogtime', "$tdate 23:59:59");
//        }
//
//        if(isset($_POST['type']) AND ($_POST['type'] != '')){
//            $type = _post('type');
//            // $filter .= "AND type='$type' ";
//            $d->where('type',$type);
//
//
//        }
//
//
//
//        if(isset($_POST['trid']) AND ($_POST['trid'] != '')){
//            $trid = _post('trid');
//            //  $filter .= "AND transactionid='$trid' ";
//            $d->where('transactionid',$trid);
//
//        }
//
//        if(isset($_POST['op']) AND ($_POST['op'] != '')){
//            $op = _post('op');
//            //  $filter .= "AND op='$op' ";
//            $d->where('op',$op);
//
//        }
//
//        $iTotalRecords =  $d->count();
//
//
//        $iDisplayLength = intval($_REQUEST['length']);
//        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
//        $iDisplayStart = intval($_REQUEST['start']);
//        $sEcho = intval($_REQUEST['draw']);
//
//        $records = array();
//        $records["data"] = array();
//
//        $end = $iDisplayStart + $iDisplayLength;
//        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
//
//
//        if($end > 1000){
//            exit;
//        }
//        $d->order_by_desc('id');
//        $d->limit($end);
//        $d->offset($iDisplayStart);
//        $x = $d->find_many();
//
//        $i = $iDisplayStart;
//        foreach ($x as $xs){
//
//
//
//
//            $id = ($i + 1);
//            $records["data"][] = array(
//                '<input type="checkbox" name="id[]" value="'.$xs['id'].'">',
//                $xs['id'],
//                $xs['date'],
//                $xs['account'],
//                $xs['type'],
//
//                $xs['amount'],
//                $xs['description'],
//
//                $xs['dr'],
//                $xs['cr'],
//                $xs['bal'],
//
//
//
//                '<a href="#" class="fview btn btn-xs blue btn-editable" id="i'.$xs['id'].'"><i class="icon-list"></i> View</a>',
//            );
//        }
//
//
//        $records["draw"] = $sEcho;
//        $records["recordsTotal"] = $iTotalRecords;
//        $records["recordsFiltered"] = $iTotalRecords;
//        $resp =  json_encode($records);
//        $handler = PhpConsole\Handler::getInstance();
//        $handler->start();
//        $handler->debug($_REQUEST, 'request');
//        echo $resp;


        break;

    case 'list-income':
        if(!has_access($user->roleid, 'reports')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
        Event::trigger('transactions/list-income/');

        $ui->assign('_application_menu', 'reports');
        $paginator = Paginator::bootstrap('sys_transactions','type','Income');
        $d = ORM::for_table('sys_transactions')->where('type','Income')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('date')->find_many();
        $ui->assign('d',$d);

        $ui->assign('xfooter',Asset::js(array('numeric')));
        $ui->assign('xjq','

         $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });

        ');
        $ui->assign('paginator',$paginator);
        $ui->display('transactions.tpl');
        break;

    case 'list-expense':
        if(!has_access($user->roleid, 'reports')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
        Event::trigger('transactions/list-expense/');

        $ui->assign('_application_menu', 'reports');
        $paginator = Paginator::bootstrap('sys_transactions','type','Expense');
        $d = ORM::for_table('sys_transactions')->where('type','Expense')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('date')->find_many();
        $ui->assign('d',$d);

        $ui->assign('xjq','

         $(\'.amount\').autoNumeric(\'init\', {

    aSign: \''.$config['currency_code'].' \',
    dGroup: '.$config['thousand_separator_placement'].',
    aPad: '.$config['currency_decimal_digits'].',
    pSign: \''.$config['currency_symbol_position'].'\',
    aDec: \''.$config['dec_point'].'\',
    aSep: \''.$config['thousands_sep'].'\'

    });

        ');

        $ui->assign('paginator',$paginator);
        $ui->display('transactions.tpl');
        break;



    case 'manage':

        Event::trigger('transactions/manage/');


        $id = $routes['2'];
        $t = ORM::for_table('sys_transactions')->find_one($id);
        if ($t) {
            
            $contact_id = ($t['type'] === 'Income') ? $t['payerid'] : (($t['type'] === 'Expense') ? $t['payeeid'] : 0);
            $contact = null;
            if ($contact_id > 0) {
                $contact = ORM::for_table('crm_accounts')
                    ->table_alias('c')
                    ->select('c.*')
                    ->select('b.alias', 'branch_alias')
                    ->join('sys_accounts', ['c.branch_id', '=', 'b.id'], 'b')
                    ->find_one($contact_id);

                if ($contact) {
                    $contact->display_name =
                        $contact->account
                        . (!empty($contact->phone) ? ' - ' . $contact->phone : '')
                        . (!empty($contact->branch_alias) ? ' [' . $contact->branch_alias . ']' : '');
                }
            }
            $ui->assign('contact', $contact);

            // $p = ORM::for_table('crm_accounts')->find_many();
            // $ui->assign('p', $p);

            $ui->assign('t', $t);
            $d = ORM::for_table('sys_accounts')->find_many();
            $ui->assign('d', $d);
            $icat = '1';
            if(($t['type']) == 'Income'){
                $cats = ORM::for_table('sys_cats')->where('type','Income')->find_many();
                $tags = Tags::get_all('Income');
            }
            elseif(($t['type']) == 'Expense'){
                $cats = ORM::for_table('sys_cats')->where('type','Expense')->find_many();
                $tags = Tags::get_all('Expense');
            }
            else{
                $cats = '0';
                $icat = '0';
                $tags = Tags::get_all('Transfer');
            }

            $ui->assign('tags',$tags);
            $dtags = explode(',',$t['tags']);
            $ui->assign('dtags',$dtags);
            $ui->assign('icat', $icat);
            $ui->assign('cats', $cats);
            $pms = ORM::for_table('sys_pmethods')->find_many();
            $ui->assign('pms', $pms);

            $ui->assign('mdate', $mdate);
            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min')));
            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','tr-manage')));
            $ui->display('manage-transaction.tpl');
        } else {
            r2(U . 'transactions/list', 'e', $_L['Transaction_Not_Found']);
        }

        break;


    case 'proforma-manage':

        Event::trigger('transactions/proforma-manage/');


        $id = $routes['2'];
        $t = ORM::for_table('sys_proforma_transactions')->find_one($id);
        if ($t) {
            $p = ORM::for_table('crm_accounts')->find_many();
            $ui->assign('p', $p);
            $ui->assign('t', $t);
            $d = ORM::for_table('sys_accounts')->find_many();
            $ui->assign('d', $d);
            $icat = '1';
            if(($t['type']) == 'Income'){
                $cats = ORM::for_table('sys_cats')->where('type','Income')->find_many();
                $tags = Tags::get_all('Income');
            }
            elseif(($t['type']) == 'Expense'){
                $cats = ORM::for_table('sys_cats')->where('type','Expense')->find_many();
                $tags = Tags::get_all('Expense');
            }
            else{
                $cats = '0';
                $icat = '0';
                $tags = Tags::get_all('Transfer');
            }

            $ui->assign('tags',$tags);
            $dtags = explode(',',$t['tags']);
            $ui->assign('dtags',$dtags);
            $ui->assign('icat', $icat);
            $ui->assign('cats', $cats);
            $pms = ORM::for_table('sys_pmethods')->find_many();
            $ui->assign('pms', $pms);

            $ui->assign('mdate', $mdate);
            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min')));
            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','tr-manage')));
            $ui->display('manage_proforma_transaction.tpl');
        } else {
            r2(U . 'transactions/list', 'e', $_L['Transaction_Not_Found']);
        }

        break;
    case 'edit-post':

        Event::trigger('transactions/edit-post/');


        $id = _post('id');
        $d = ORM::for_table('sys_transactions')->find_one($id);
        if($d){
            $cat = _post('cats');
            $pmethod = _post('pmethod');
            $ref = _post('ref');
            $date = _post('date');
            $payer = _post('payer');
            $payee = _post('payee');
            $description = _post('description');
            $msg = '';
            if ($description == '') {
                $msg .= $_L['description_error'] . '<br>';
            }



            if(!is_numeric($payer)){
                $payer = '0';
            }

            if(!is_numeric($payee)){
                $payee = '0';
            }

            $tags = $_POST['tags'];


            if ($msg == '') {
                //find the current balance for this account

                Tags::save($tags,$d['type']);

                $d->category = $cat;
                $d->payerid = $payer;
                $d->payeeid = $payee;
                $d->method = $pmethod;
                $d->ref = $ref;
                $d->tags = Arr::arr_to_str($tags);
                $d->description = $description;
                $d->date = $date;
                $d->datetime = $date.' '.date('H:i:s');

                $d->save();
                _msglog('s',$_L['edit_successful']);
                echo $d->id();
            } else {
                echo $msg;
            }
        }
        else{
            echo 'Transaction Not Found';
        }




        break;   

				case 'edit-proforma-post':

        Event::trigger('transactions/edit-proforma-post/');


        $id = _post('id');
        $d = ORM::for_table('sys_proforma_transactions')->find_one($id);
        if($d){
            $cat = _post('cats');
            $pmethod = _post('pmethod');
            $ref = _post('ref');
            $date = _post('date');
            $payer = _post('payer');
            $payee = _post('payee');
            $description = _post('description');
            $msg = '';
            if ($description == '') {
                $msg .= $_L['description_error'] . '<br>';
            }



            if(!is_numeric($payer)){
                $payer = '0';
            }

            if(!is_numeric($payee)){
                $payee = '0';
            }

            $tags = $_POST['tags'];


            if ($msg == '') {
                //find the current balance for this account

                Tags::save($tags,$d['type']);

                $d->category = $cat;
                $d->payerid = $payer;
                $d->payeeid = $payee;
                $d->method = $pmethod;
                $d->ref = $ref;
                $d->tags = Arr::arr_to_str($tags);
                $d->description = $description;
                $d->date = $date;

                $d->save();
                _msglog('s',$_L['edit_successful']);
                echo $d->id();
            } else {
                echo $msg;
            }
        }
        else{
            echo 'Transaction Not Found';
        }


        break;
    case 'delete-post':
        Event::trigger('transactions/delete-post/');
        $id = _post('id');
        $iid = get_type_by_id('sys_transactions', 'id', _post('id'), 'iid');
        $amount = get_type_by_id('sys_transactions', 'id', _post('id'), 'amount');
				 $d = ORM::for_table('sys_invoices')->find_one($iid);
				 if(!empty($d)){
					$d->set(array(
								'credit'		 			=> $d['credit']-$amount
							));
							$d->save(); //save
				 }
				 
        $timesheets = ORM::for_table('crm_timesheet')
            ->where('transaction_id', $id)
            ->find_many();
        
        foreach ($timesheets as $timesheet) {
            $timesheet->set('transaction_id', null);
            $timesheet->save();
        }
            
        if(Transaction::delete($id)){
            r2(U . 'transactions/list', 's', $_L['transaction_delete_successful']);
        }
        else{
            r2(U . 'transactions/list', 'e', $_L['an_error_occured']);
        }
        break;
				
		case 'delete-proforma-post':
        Event::trigger('transactions/delete-proforma-post/');
        $id = _post('id');
        $iid = get_type_by_id('sys_proforma_transactions', 'id', _post('id'), 'iid');
        $amount = get_type_by_id('sys_proforma_transactions', 'id', _post('id'), 'amount');
				$d = ORM::for_table('sys_performa')->find_one($iid);
				$d->set(array(
								'credit'		 			=> $d['credit']-$amount
							));
				$d->save(); //save
        if(Proformatransaction::delete($id)){
            r2(U . 'transactions/list-proforma', 's', $_L['transaction_delete_successful']);
        }
        else{
            r2(U . 'transactions/list-proforma', 'e', $_L['an_error_occured']);
        }
        break;


    case 'post':

        break;

    case 's':
        Event::trigger('transactions/s/');
        $d = ORM::for_table('sys_accounts')->find_many();
        // $p = ORM::for_table('sys_payers')->find_many();
        $c = ORM::for_table('crm_accounts')->find_many();
        $ui->assign('c', $c);
        $ui->assign('d', $d);
        $cats = ORM::for_table('sys_cats')->where('type','Income')->order_by_asc('sorder')->find_many();
        $ui->assign('cats', $cats);
        $pms = ORM::for_table('sys_pmethods')->find_many();
        $ui->assign('pms', $pms);
        $mdate = date('Y-m-d');
        $fdate = date('Y-m-d', strtotime('today - 30 days'));
        $ui->assign('fdate', $fdate);
        $ui->assign('tdate', $mdate);
        $ui->assign('xheader', Asset::css(array('s2/css/select2.min','dp/dist/datepicker.min','modal')));
        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'dp/dist/datepicker.min','dp/i18n/'.$config['language'],'numeric','modal','js/tra')));

        $ui->display('trs.tpl');


        break;

    case 'export_csv':

        Event::trigger('transactions/export_csv/');

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
        $results = db_find_array('sys_transactions');

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


    case 'handle_attachment':



        $uploader   =   new Uploader();
        $uploader->setDir('application/storage/transactions/');
        $uploader->sameName(false);
        $uploader->setExtensions(array('jpg','jpeg','png','gif','pdf'));  //allowed extensions list//
        if($uploader->uploadFile('file')){   //txtFile is the filebrowse element name //
            $uploaded  =   $uploader->getUploadName(); //get uploaded file name, renames on upload//

            $file = $uploaded;
            $msg = 'Uploaded Successfully';
            $success = 'Yes';

        }else{//upload failed
            $file = '';
            $msg = $uploader->getMessage();
            $success = 'No';
        }

        $a = array(
            'success' => $success,
            'msg' =>$msg,
            'file' =>$file
        );

        header('Content-Type: application/json');

        echo json_encode($a);


        break;

    default:
        echo 'action not defined';
}
