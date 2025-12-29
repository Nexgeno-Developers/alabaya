<?php

if(!isset($myCtrl)){
    $myCtrl = 'contacts';
}
_auth();
$ui->assign('_application_menu', 'contacts');
$ui->assign('_title', $_L['Contacts'].' - '. $config['CompanyName']);
$ui->assign('_st', $_L['Contacts']);

$action = $routes['1'];
$user = User::_info();
$ui->assign('user', $user);

$ui->assign('jsvar', '
_L[\'Working\'] = \''.$_L['Working'].'\';
_L[\'Submit\'] = \''.$_L['Submit'].'\';
 ');


switch ($action) {
    case 'add':

        if(!has_access($user->roleid, 'customers')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }

        Event::trigger('contacts/add/');

            $categories = ORM::for_table('category_employee')->find_many();
            
            $categoryData = [];
            foreach ($categories as $category) {
                $categoryData[] = [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            }
            
            $ui->assign('categoryData', $categoryData);
            
        $ui->assign('countries',Countries::all($config['country'])); // may add this $config['country_code']

        $fs = ORM::for_table('crm_customfields')->where('ctype','crm')->order_by_asc('id')->find_many();
        $ui->assign('fs',$fs);

        // find all groups

        $gs = ORM::for_table('crm_groups')->order_by_asc('sorder')->find_array();

        $ui->assign('gs',$gs);

        $g_selected_id = route(2);

        if($g_selected_id){
            $ui->assign('g_selected_id',$g_selected_id);
        }
        else{
            $ui->assign('g_selected_id','');
        }

        // 🔹 Branch logic (same as "users")
        if($user->roleid == 0){
            // Admin → all branches
            $branches = ORM::for_table('sys_accounts')
                ->order_by_asc('account')
                ->find_array();
        }
        else{
            // Non-admin staff → only their branch
            $branches = ORM::for_table('sys_accounts')
                ->where('id', $user->branch_id)
                ->find_array();
        }
        $ui->assign('branches', $branches);


//        $ui->assign('xheader', '
//<link rel="stylesheet" type="text/css" href="ui/lib/s2/css/select2.min.css"/>
//');
        $ui->assign('xheader', Asset::css('s2/css/select2.min'));
        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'add-contact')));
        $tags = Tags::get_all('Contacts');
        $ui->assign('tags',$tags);
        $ui->assign('xjq', '$("#country").select2({ theme: "bootstrap" }); ');
        $ui->assign('jsvar', '_L[\'Working\'] = \''.$_L['Working'].'\'; ');
        $currencies = Model::factory('Models_Currency')->find_array();
        $ui->assign('currencies',$currencies);
        $ui->display('add-contact.tpl');
      break;

case 'category-employee-list':
    if(!has_access($user->roleid, 'contact_category')) {
        r2(U."dashboard",'e',$_L['You do not have permission']);
    }
    Event::trigger('contacts/category-employee-list/');
    
    // Fetch category data
    $categories = ORM::for_table('category_employee')->order_by_asc('id')->find_many();
    
    // Assign categories to the UI with edit and delete buttons
    $categoryData = [];
    foreach ($categories as $category) {
        $categoryData[] = [
            'id' => $category->id,
            'name' => $category->name,
            'price' => $category->price,
            'status' => $category->status,
            'edit_button' => '<button class="btn btn-primary btn-xs" onclick="edit_category_modal(' . $category->id . ')"><i class="fa fa-edit"></i> Edit</button>',
            'delete_button' => '<button class="btn btn-danger btn-xs" onclick="delete_category(' . $category->id . ')"><i class="fa fa-trash"></i> Delete</button>'
        ];
    }
    $ui->assign('xheader',Asset::css(array('modal')));
    $ui->assign('xfooter',Asset::js(array('modal')));    
    $ui->assign('categories', $categoryData ?: []); // Ensure categories is an array even if no data is found
    
    $ui->display('list-all-category.tpl');
    break;

    
    case 'add-category-employee':
        if(!has_access($user->roleid, 'contact_category')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }
        // Trigger event
        Event::trigger('contacts/add-category-employee/');
       
        // Display the add category template
        $ui->display('add-category.tpl');
        break;
    
    case 'addcategoryemployee-post':
        // Trigger event
        Event::trigger('contacts/addcategoryemployee-post/');
    
        // Process form data
        if(isset($_POST['name'], $_POST['price'], $_POST['status'])) {
            $categoryName = $_POST['name'];
            $price = $_POST['price'];
            $status = $_POST['status'];
            
            // Save category to database
            $newCategory = ORM::for_table('category_employee')->create();
            $newCategory->name = $categoryName;
            $newCategory->price = $price;
            $newCategory->status = $status;
            $newCategory->save();
    
            // Redirect or display success message
        } else {
            // Handle invalid form data
        }
        break;
    
    case 'edit-category-modal':
        Event::trigger('contacts/edit-category-modal');
        
        $category_id = $routes['2'];
        $existCategory = ORM::for_table('category_employee')->where('id', $category_id)->find_one();
        if ($existCategory) {
            // Extract category details
            $id = $existCategory->id;
            $name = $existCategory->name;
            $price = $existCategory->price;
            $status = $existCategory->status;
            
            // Assign category details to the UI for rendering in the template
            $ui->assign('category', [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'status' => $status
            ]);
            
            $ui->display('category/edit-category-modal.tpl');
        } else {
            // Record not found, handle accordingly
        }
        break;
    
    
    case 'edit-category-post':
        $category_id   = _post('category_id');
        $categoryName = _post('name');
        $price = _post('price');
        $status = _post('status');
        $updatedAt   = date('Y-m-d H:i:s');
        
        $existCategory = ORM::for_table('category_employee')->find_one($category_id);
        if($existCategory){
            $existCategory->name = $categoryName;
            $existCategory->price = $price;
            $existCategory->status = $status;
            $existCategory->updated_at = $updatedAt;
            $existCategory->save();
            _msglog('s', $_L['Category_updated_successfully!']);
            // Return any necessary response
        }
        break;
    
    case 'delete-category':
        $category_id = $routes['2'];
        $existCategory = ORM::for_table('category_employee')->where('id', $category_id)->find_one();
        if ($existCategory) {
            $existCategory->delete();
            _msglog('s', $_L['Category_deleted_successfully!']);
            // Redirect or refresh the page after deletion
        } else {
            // Record not found, handle accordingly
        }
        break;
    
    


    case 'summary':


        Event::trigger('contacts/summary/');


        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
				//var_dump($d);
        if($d){
            // Get branch name
            $branch = ORM::for_table('sys_accounts')->find_one($d['branch_id']);
            $d['branch_name'] = $branch ? $branch->account : '';
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

            $ui->assign('te',$te);
            $ui->assign('d',$d);

            $cf = ORM::for_table('crm_customfields')->where('ctype','crm')->order_by_asc('id')->find_many();
            $ui->assign('cf',$cf);

            // Find Profit

            if($ti > $te){

                $happened = $_L['Profit'];
                $css_class = 'green';

                $d_amount = $ti-$te;

            }
            else{
                $happened = $_L['Loss'];
                $css_class = 'danger';
                $d_amount = $te-$ti;
            }

            $ui->assign('happened',$happened);
            $ui->assign('css_class',$css_class);
            $ui->assign('d_amount',$d_amount);

            $ui->display('ajax.contact-summary.tpl');

        }
        else{

        }


        break;

    case 'activity':

        Event::trigger('contacts/activity/');

        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        if($d){
            $ac = ORM::for_table('sys_activity')->where('cid',$cid)->limit(20)->order_by_desc('id')->find_many();
            $ui->assign('ac',$ac);
            $ui->display('ajax.contact-activity.tpl');
        }
        else{

        }


        break;


    case 'invoices':

        Event::trigger('contacts/invoices/');

        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        if($d){
$i = ORM::for_table('sys_invoices')->where('userid',$cid)->find_many();
            $ui->assign('i',$i);
            $ui->display('ajax.contact-invoices.tpl');
        }
        else{

        }


        break;


    case 'transactions':

        Event::trigger('contacts/transactions/');

        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        if($d){
            $tr = ORM::for_table('sys_transactions')
                ->where_raw('(`payerid` = ? OR `payeeid` = ?)', array($cid, $cid))
                ->order_by_desc('id')->find_many();
            $ui->assign('tr',$tr);
            $ui->assign('cid',$cid);
            $ui->display('ajax.contact-transactions.tpl');
        }
        else{

        }


        break;
        
        
        case 'balanceSheetVendor':
            Event::trigger('contacts/balanceSheetVendor/');
            $cid = _post('cid');
            $d = ORM::for_table('crm_accounts')->find_one($cid);
            if($d)
            {
                //$i = ORM::for_table('sys_invoiceitems')->where('v_id',$cid)->find_many();
                /*$ge = ORM::for_table('sys_general_entry')
                ->where('type', 'General')
                ->where_raw('(`payerid` = ? OR `payeeid` = ?)', array($cid, $cid))
                ->order_by_desc('id')->find_many();*/   
                //$ui->assign('ge',$ge);
                //$ui->assign('i',$i);
                
                $company = ORM::for_table('sys_accounts')->select('account')->select('address')->where('id', 2)->find_one();
                $creditStock = ORM::for_table('sys_items_stock')->where('type', 'credit')->where('vendor_id', $cid)->find_many();
                $debitStock  = ORM::for_table('sys_items_stock')->where('type', 'debit')->where('vendor_id', $cid)->find_many();
                $expenseTr = ORM::for_table('sys_transactions')->where('type', 'Expense')->where_raw('(`payerid` = ? OR `payeeid` = ?)', array($cid, $cid))->order_by_desc('id')->find_many();
                $incomeTr = ORM::for_table('sys_transactions')->where('type', 'Income')->where_raw('(`payerid` = ? OR `payeeid` = ?)', array($cid, $cid))->order_by_desc('id')->find_many();
                
                $ui->assign('cid',$cid);
                $ui->assign('company', $company);
                $ui->assign('creditStock', $creditStock);
                $ui->assign('debitStock', $debitStock);
                $ui->assign('expenseTr',$expenseTr);
                $ui->assign('incomeTr',$incomeTr);
                
                

                $ui->display('ajax.contact-balance-sheet-vendor.tpl');
            }
        break;        

        /*start - timesheet*/
        case 'employee-timesheet':
            Event::trigger('contacts/employee-timesheet/');
            $cid = _post('cid');
            $d = ORM::for_table('crm_accounts')->find_one($cid);
            if($d)
            {
                $ui->assign('cid',$cid);
                $ui->assign('employee',$d);
                $ui->display('timesheet/ajax.contact-timesheet-employee.tpl');                
            }
        break;  
        
        case 'list-ajax-timesheet':  
            Event::trigger('contacts/list-ajax-timesheet/');
        
            $response = array();
            
            try {
                ## Read value
                $draw = $_POST['draw'];
                $start = $_POST['start'];
                $rowperpage = $_POST['length'];
                // $columnIndex = $_POST['order'][0]['column'];
                // $columnName = $_POST['columns'][$columnIndex]['data'];
                // $columnSortOrder = $_POST['order'][0]['dir'];
                $columnIndex = $_POST['order'][0]['column'] ?? 0;
                $columnSortOrder = $_POST['order'][0]['dir'] ?? 'desc';

                $sortableColumns = [
                    'date'           => 'crm_timesheet.date',
                    'qty'            => 'crm_timesheet.qty',
                    'amount'         => 'crm_timesheet.amount',
                    'earn_amount'    => 'crm_timesheet.earn_amount',
                    'paid_date'      => 'crm_timesheet.paid_date',
                    'completed_date' => 'invoice_alocation.completed_date',
                ];

                $requestedColumn = $_POST['columns'][$columnIndex]['data'] ?? '';
                $orderColumn = $sortableColumns[$requestedColumn] ?? 'crm_timesheet.date';

                $employee_id =  isset($_POST['employee_id']) ? $_POST['employee_id'] : '';
                $todate  = isset($_POST['todate']) ? $_POST['todate'] : '';
                $fromdate  = isset($_POST['fromdate']) ? $_POST['fromdate'] : '';
                
                $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';
                $salary_type = isset($_POST['salary_type']) ? $_POST['salary_type'] : '';
        
                // Retrieve the salery_type of the employee from the crm_accounts table
                // $employee = ORM::for_table('crm_accounts')->find_one($employee_id);
                // $salery_type = $employee ? str_replace('_', ' ', strtoupper($employee->salery_type)) : null;
                // var_dump($salery_type);
                
                // Initialize the ORM query for counting total records
                $totalRecordQuery = ORM::for_table('crm_timesheet')->select('id');
                
                // Apply filters based on inputs
                if(!empty($employee_id)) {
                    $totalRecordQuery->where('employee_id', $employee_id);
                } 
                if (!empty($fromdate) && !empty($todate)) {
                    $totalRecordQuery->where_gte('date', $fromdate)->where_lte('date', $todate);
                }
                if ($payment_status == 'paid') {
                    $totalRecordQuery->where_not_null('transaction_id');
                } elseif ($payment_status == 'unpaid') {
                    $totalRecordQuery->where_null('transaction_id');
                }
                if ($salary_type == 'per_piece') {
                    $totalRecordQuery->where_not_null('invoice_alocation_id');
                } elseif ($salary_type == 'per_hour') {
                    $totalRecordQuery->where_null('invoice_alocation_id');
                }

                // Count total records without pagination
                $totalRecordCount = $totalRecordQuery->count();
                
                // Initialize the ORM query for fetching records
                // $recordQuery = ORM::for_table('crm_timesheet');

                // Build record query WITH joins to fetch invoicenum and completed_date
                // $recordQuery = ORM::for_table('crm_timesheet')
                //     ->select('crm_timesheet.*')
                //     ->select('crm_timesheet.paid_date', 'paid_date')
                //     ->left_outer_join('invoice_alocation', 'crm_timesheet.invoice_alocation_id = invoice_alocation.id')
                //     ->select('invoice_alocation.completed_date', 'completed_date')
                //     ->select('invoice_alocation.invoice_id', 'ia_invoice_id')
                //     ->left_outer_join('sys_invoices', 'invoice_alocation.invoice_id = sys_invoices.id')
                //     ->select('sys_invoices.invoicenum', 'invoicenum');
                    
                // Build record query WITH joins to fetch invoicenum and completed_date
                $recordQuery = ORM::for_table('crm_timesheet')
                    ->select_many([
                        'timesheet_id'         => 'crm_timesheet.id',
                        'employee_id'          => 'crm_timesheet.employee_id',
                        'branch_id'            => 'crm_timesheet.branch_id',
                        'checkin'              => 'crm_timesheet.checkin',
                        'checkout'             => 'crm_timesheet.checkout',
                        'qty'                  => 'crm_timesheet.qty',
                        'amount'               => 'crm_timesheet.amount',
                        'earn_amount'          => 'crm_timesheet.earn_amount',
                        'remarks'              => 'crm_timesheet.remarks',
                        'invoice_alocation_id' => 'crm_timesheet.invoice_alocation_id',
                        'date'                 => 'crm_timesheet.date',
                        'transaction_id'       => 'crm_timesheet.transaction_id',
                        'paid_date'            => 'crm_timesheet.paid_date',
                    ])
                    ->left_outer_join('invoice_alocation', 'crm_timesheet.invoice_alocation_id = invoice_alocation.id')
                    ->select_many([
                        'completed_date' => 'invoice_alocation.completed_date',
                        'ia_invoice_id'  => 'invoice_alocation.invoice_id',
                    ])
                    ->left_outer_join('sys_invoices', 'invoice_alocation.invoice_id = sys_invoices.id')
                    ->select_many([
                        'invoice_id' => 'sys_invoices.id',
                        'invoicenum' => 'sys_invoices.invoicenum',
                    ]);

                // Apply filters to recordQuery (same as before)
                if (!empty($employee_id)) {
                    $recordQuery->where('crm_timesheet.employee_id', $employee_id);
                }
                if (!empty($fromdate) && !empty($todate)) {
                    $recordQuery->where_gte('crm_timesheet.date', $fromdate)->where_lte('crm_timesheet.date', $todate);
                }
                if ($payment_status == 'paid') {
                    $recordQuery->where_not_null('crm_timesheet.transaction_id');
                } elseif ($payment_status == 'unpaid') {
                    $recordQuery->where_null('crm_timesheet.transaction_id');
                }
                if ($salary_type == 'per_piece') {
                    $recordQuery->where_not_null('crm_timesheet.invoice_alocation_id');
                } elseif ($salary_type == 'per_hour') {
                    $recordQuery->where_null('crm_timesheet.invoice_alocation_id');
                }

                // IMPORTANT: apply ordering to the query BEFORE executing find_many()
                if ($columnSortOrder === 'asc') {
                    $recordQuery->order_by_asc($orderColumn);
                } else {
                    $recordQuery->order_by_desc($orderColumn);
                }

                // Now fetch all rows matching filters to calculate totals / timesheet list
                $Records = $recordQuery->find_many();

                // Build totals and list of timesheet ids & invoice numbers
                $timesheetIds = [];
                $invoiceNums = [];
                $earnAmountSumTotal = 0.0;

                foreach ($Records as $record) {
                    $earnAmountSumTotal += (float) round($record->earn_amount, 2);
                    if (!empty($record->earn_amount) && (float)$record->earn_amount > 0) {
                        if (!empty($record->timesheet_id)) {
                            $timesheetIds[] = $record->timesheet_id;
                        }
                        if (!empty($record->invoicenum)) {
                            $invoiceNums[] = $record->invoicenum;
                        }
                    }
                }

                $timesheetIds = array_unique($timesheetIds);
                $invoiceNums  = array_unique($invoiceNums);

                $invoiceDescription = !empty($invoiceNums) ? implode(', ', $invoiceNums) . ' ' . date("F Y") . ' Salary' : 'Salary';
                $timesheetIdsParam = !empty($timesheetIds) ? implode(',', $timesheetIds) : '';

                // ---------- Now build a FRESH paginated query (same selects/joins/filters + ordering), then apply offset/limit ----------
                $paginatedQuery = ORM::for_table('crm_timesheet')
                    ->select_many([
                        'timesheet_id'         => 'crm_timesheet.id',
                        'employee_id'          => 'crm_timesheet.employee_id',
                        'branch_id'            => 'crm_timesheet.branch_id',
                        'checkin'              => 'crm_timesheet.checkin',
                        'checkout'             => 'crm_timesheet.checkout',
                        'qty'                  => 'crm_timesheet.qty',
                        'amount'               => 'crm_timesheet.amount',
                        'earn_amount'          => 'crm_timesheet.earn_amount',
                        'remarks'              => 'crm_timesheet.remarks',
                        'invoice_alocation_id' => 'crm_timesheet.invoice_alocation_id',
                        'date'                 => 'crm_timesheet.date',
                        'transaction_id'       => 'crm_timesheet.transaction_id',
                        'paid_date'            => 'crm_timesheet.paid_date',
                    ])
                    ->left_outer_join('invoice_alocation', 'crm_timesheet.invoice_alocation_id = invoice_alocation.id')
                    ->select_many(['completed_date' => 'invoice_alocation.completed_date', 'ia_invoice_id' => 'invoice_alocation.invoice_id'])
                    ->left_outer_join('sys_invoices', 'invoice_alocation.invoice_id = sys_invoices.id')
                    ->select_many(['invoice_id' => 'sys_invoices.id', 'invoicenum' => 'sys_invoices.invoicenum']);

                // apply same filters to paginatedQuery
                if (!empty($employee_id)) {
                    $paginatedQuery->where('crm_timesheet.employee_id', $employee_id);
                }
                if (!empty($fromdate) && !empty($todate)) {
                    $paginatedQuery->where_gte('crm_timesheet.date', $fromdate)->where_lte('crm_timesheet.date', $todate);
                }
                if ($payment_status == 'paid') {
                    $paginatedQuery->where_not_null('crm_timesheet.transaction_id');
                } elseif ($payment_status == 'unpaid') {
                    $paginatedQuery->where_null('crm_timesheet.transaction_id');
                }
                if ($salary_type == 'per_piece') {
                    $paginatedQuery->where_not_null('crm_timesheet.invoice_alocation_id');
                } elseif ($salary_type == 'per_hour') {
                    $paginatedQuery->where_null('crm_timesheet.invoice_alocation_id');
                }

                // apply ordering to paginated query
                if ($columnSortOrder === 'asc') {
                    $paginatedQuery->order_by_asc($orderColumn);
                } else {
                    $paginatedQuery->order_by_desc($orderColumn);
                }

                // fetch the paginated slice
                $records = $paginatedQuery->offset($start)->limit($rowperpage)->find_many();
        

                $earnAmountSum = 0;
                foreach ($records as $record) {
                    $earnAmountSum += round($record->earn_amount, 2);
                }
                            

        
                // Prepare data for DataTables
                $data = array();
                $sr = $start + 1;

                foreach($records as $record) {
                    $employeeId     = $record->employee_id;
                    
                    $event1 = "edit_timesheet_modal('".$record->timesheet_id."')";
                    $payment_status_text = !empty($record->transaction_id) ? 'Paid' : 'Unpaid';
                    $edit = '';
                    if($payment_status_text === 'Unpaid'){
                        $edit = '<a href="javascript:void(0);" class="btn btn-primary btn-xs" onclick="'.$event1.'"><i class="fa fa-edit"></i> edit</a>';  
                    }
                    $salery_type = $record->invoice_alocation_id ? 'Per Piece' : 'Per Hour';
                    
                    // Prepare Pay Now button data
                    $payNowButton = "";
                    if(!empty($timesheetIdsParam) && $payment_status == 'unpaid'){
                        if (!empty($invoiceNums) && $salary_type == 'per_piece') {
                            $payNowButton = '<a id="pay-now-action" href="' . APP_URL . '/?ng=transactions/expense&description=' . urlencode($invoiceDescription) . '&total_amount=' . $earnAmountSumTotal . '&employee_id=' . $employeeId . '&timesheet_ids=' . urlencode($timesheetIdsParam) . '" target="_blank">Pay Now</a>';
                        }elseif(!empty($fromdate) && !empty($todate) && $salary_type == 'per_hour'){
                            $description = $fromdate . " To " . $todate . " Salary";
                            $payNowButton = '<a id="pay-now-action" href="' . APP_URL . '/?ng=transactions/expense&description=' . urlencode($description) . '&total_amount=' . $earnAmountSumTotal . '&employee_id=' . $employeeId . '&timesheet_ids=' . urlencode($timesheetIdsParam) . '" target="_blank">Pay Now</a>';
                        }
                    }
                    
                    // Fetch invoice_alocation_id for each record
                    $invoice_alocation2 = ORM::for_table('invoice_alocation')
                        ->select('invoice_id')
                        ->where('id', $record->invoice_alocation_id)
                        ->find_one();
                    
                    // Fetch invoicenum from sys_invoices if invoice_id is available
                    $invoicenum = 'N/A'; // Default to 'N/A' if invoicenum is not available
                    if ($invoice_alocation2) {
                        $invoice_id2 = $invoice_alocation2->invoice_id; // Extract the invoice_id from the result
                    
                        // Fetch invoicenum from sys_invoices using the invoice_id
                        $invoice2 = ORM::for_table('sys_invoices')
                            ->select('invoicenum')
                            ->where('id', $invoice_id2)
                            ->find_one();
                    
                        if ($invoice2) {
                            $invoicenum = '<a href="' . APP_URL . '/?ng=invoices/view/' . $invoice_id2 . '/" target="_blank">' . $invoice2->invoicenum . '</a>';
                        }
                    }
                    
                    // $invoicenum = $record->invoicenum 
                    //     ? '<a href="'.APP_URL.'/?ng=invoices/view/'.$record->invoice_id.'/" target="_blank">'.$record->invoicenum.'</a>' 
                    //     : 'N/A'; // Default to 'N/A' if invoicenum is null

                    // Format paid_date and completed_date (if empty fallback to N/A)
                    // $paid_date = !empty($record->paid_date) ? $record->paid_date : 'N/A';
                    // $completed_date = !empty($record->completed_date) ? $record->completed_date : 'N/A';
                    $paid_date = !empty($record->paid_date) ? date('Y-m-d', strtotime($record->paid_date)) : 'N/A';
                    $completed_date = !empty($record->completed_date) ? date('Y-m-d', strtotime($record->completed_date)) : 'N/A';


                    $data[] = array( 
                        "sr"                => $sr,                      
                        "employee_type"     => $salery_type,                      
                        "checkin"           => $record->checkin,
                        "checkout"          => $record->checkout,
                        "qty"               => $record->qty,
                        "amount"            => $record->amount,
                        "earn_amount"       => $record->earn_amount,
                        "earnAmountSum"     => ($totalRecordCount > 0) ? ($totalRecordCount < 10 ? $earnAmountSum : $earnAmountSumTotal) : 0,
                        "payNowButton"      => $payNowButton ?? '',
                        "invoicenum"        => $invoicenum,
                        "payment_status"    => $payment_status_text,
                        "remarks"           => $record->remarks,
                        "date"              => $record->date,
                        "paid_date"         => $paid_date,
                        "completed_date"    => $completed_date,
                        "action"            => $edit,
                    ); 
                    $sr++;    
                }
                                // Calculate earnAmountSum for displayed records
                // echo '<pre>';
                // var_dump($totalRecordCount);
                // echo '</pre>';
                // exit;
                // Prepare response for DataTables
                $response = array(
                    "draw" => intval($draw),
                    "iTotalRecords" => $totalRecordCount,
                    "iTotalDisplayRecords" => $totalRecordCount,
                    "aaData" => $data,
                    "earnAmountSum" => ($totalRecordCount > 0) ? ($totalRecordCount < 10 ? $earnAmountSum : $earnAmountSumTotal) : 0,
                    "payNowButton" => $payNowButton ?? ''
                );
            } catch (Exception $e) {
                // Handle any exceptions here, possibly by setting an error flag in the response
                $response = array(
                    "error" => "An error occurred: " . $e->getMessage()
                );
            }
            
            // Output response as JSON
            echo json_encode($response);
            $ui->assign('earnAmountSum', $totalRecordCount < 10 ? $earnAmountSum : $earnAmountSumTotal);
            $ui->assign('payNowButton', $payNowButton ?? '');
        break;
        
        
        case 'edit-timesheet-modal': //in use
            Event::trigger('contacts/edit-timesheet-modal/');
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
            // Check if timesheet's date is today's date
            $is_today = false; // Default to false
            $today = new DateTime(); // Get current date
            if (isset($timesheet->date)) { // Assuming 'date' is the relevant field in crm_timesheet
                $timesheet_date = new DateTime($timesheet->date); // Convert to DateTime
                if ($timesheet_date->format('Y-m-d') === $today->format('Y-m-d')) {
                    $is_today = true;
                }
            }
                $timesheet_allocation = $timesheet->invoice_alocation_id ?? null;
                $ui->assign('timesheet_allocation', $timesheet_allocation);
                $ui->assign('is_today', $is_today);
                $ui->assign('salery_type', $salery_type);
                $ui->assign('timesheet', $timesheet);
                $ui->display('timesheet/edit-timesheet-modal.tpl');
        break;
        
    
       case 'edit-timesheet-post':
            $record_id   = _post('timesheet_id');
            $type        = _post('type');
            $checkIn     = _post('checkin');
            $checkOut    = _post('checkout');
            $remarks     = _post('remarks');
            $qty         = _post('qty');
            $amount      = _post('amount');
            $date        = date('Y-m-d', strtotime(_post('checkout')));
            $today       = date('Y-m-d');
            $updatedAt   = date('Y-m-d H:i:s');
            
            // Fetch the timesheet record
            $timesheet = ORM::for_table('crm_timesheet')->find_one($record_id);
            
            if ($timesheet) {
                $timesheet_allocation = $timesheet->invoice_alocation_id;
        
                // Debugging: Output values received from the form to error log
                // error_log('Received data:');
                // error_log('qty: ' . $qty);
                // error_log('amount: ' . $amount);
                // error_log('timesheet_allocation: ' . $timesheet_allocation);
                // error_log('checkin: ' . $checkIn);
                // error_log('checkout: ' . $checkOut);
        
                // Prevent updates if the checkout date is today and no allocation exists
                if ($timesheet->date === $today && (!$timesheet_allocation || $timesheet_allocation == '')) {
                    // error_log('Timesheet for today cannot be modified!');
                    echo 'Timesheet for today cannot be modified!';
                    return; // Exit the function
                }
        
                // Case: If timesheet has an allocation and the type is per_hour or per_piece
                if (($type == 'per_hour' || $type == 'per_piece') && $timesheet_allocation && (empty($checkIn) && empty($checkOut))) {
                    // Just update qty and amount if there's an allocation ID
                    // error_log('Updating qty and amount for allocation...');
        
                    $timesheet->checkin = null;
                    $timesheet->checkout = null;
                    $timesheet->qty = $qty;
                    $timesheet->amount = $amount;
        
                    // Debugging: Check if qty and amount are set correctly
                    // error_log('Updated qty: ' . $timesheet->qty);
                    // error_log('Updated amount: ' . $timesheet->amount);
        
                    // Calculate earn_amount using qty * amount
                    $timesheet->earn_amount = ($qty != null) ? ($qty * $amount) : ($timesheet->qty * $timesheet->amount);
        
                    // Debugging: Output the calculated earn_amount
                    // error_log('Calculated earn_amount: ' . $timesheet->earn_amount);
                } 
                // Case: If no allocation and type is per_hour, calculate based on checkin/checkout times
                elseif (!empty($checkIn) && !empty($checkOut)) {
                    // error_log('Calculating qty based on checkin/checkout...');
        
                    $timesheet->checkin = date('Y-m-d H:i:s', strtotime($checkIn));
                    $timesheet->checkout = date('Y-m-d H:i:s', strtotime($checkOut));
        
                    // Debugging: Check if checkin and checkout are valid
                    // error_log('checkin: ' . $timesheet->checkin);
                    // error_log('checkout: ' . $timesheet->checkout);
        
                    // Calculate hours worked
                    $checkInTimestamp = strtotime($timesheet->checkin);
                    $checkOutTimestamp = strtotime($timesheet->checkout);
                    if ($checkInTimestamp && $checkOutTimestamp) {
                        $seconds_diff = $checkOutTimestamp - $checkInTimestamp;
                        $hours = $seconds_diff / (60 * 60); // Convert seconds to hours
                        $timesheet->qty = $hours;
                        $timesheet->earn_amount = $hours * $amount;
        
                        // Debugging: Check the calculated hours and earn_amount
                        // error_log('Calculated hours: ' . $hours);
                        // error_log('Calculated earn_amount: ' . $timesheet->earn_amount);
                    } else {
                        // error_log('Invalid checkin or checkout time format.');
                        echo 'Invalid checkin or checkout time format.';
                        return; // Exit if there's an error in time calculation
                    }
                }
        
                // Update other fields like remarks and updated_at
                $timesheet->remarks = $remarks;
                $timesheet->updated_at = $updatedAt;
        
                $timesheet->save();
                _msglog('s','Timesheet Updated Successfully!');
                echo $timesheet->id();
            } else {
                echo 'Timesheet not found.';
            }
        break;

        
        /*
        case 'edit-timesheet-modal': //in use
            Event::trigger('sales/add/');
            $timesheet_id = $routes['2'];
            $timesheet = ORM::for_table('crm_timesheet')->find_one($timesheet_id); 
            if ($timesheet) {
                $employee_id = $timesheet->employee_id;
                $employee = ORM::for_table('crm_accounts')->where('id', $employee_id)->find_one();
                if ($employee) {
                    // $salery_type = $employee->salery_type;
                    $salery_type = $timesheet->invoice_alocation_id ? 'per_piece' : 'per_hour';
                } else {
                    // Handle the case where employee record is not found
                }
            } else {
                // Handle the case where timesheet record is not found
            }
            
            // Check if timesheet's date is today's date
            $is_today = false; // Default to false
            $today = new DateTime(); // Get current date
            if (isset($timesheet->date)) { // Assuming 'date' is the relevant field in crm_timesheet
                $timesheet_date = new DateTime($timesheet->date); // Convert to DateTime
                if ($timesheet_date->format('Y-m-d') === $today->format('Y-m-d')) {
                    $is_today = true;
                }
            }
    
            $ui->assign('is_today', $is_today);
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
                
                $timesheet->checkin = ($type == 'per_hour') ? date('Y-m-d H:i:s', strtotime(_post('checkin'))) : null ;
                $timesheet->checkout = ($type == 'per_hour') ? date('Y-m-d H:i:s', strtotime(_post('checkout'))) : null ;
                $checkIn  = strtotime($timesheet->checkin);
                $checkOut = strtotime($timesheet->checkout);
                $seconds_diff = $checkOut - $checkIn;
                $hours = $seconds_diff / (60 * 60); // Convert seconds to hours
                $timesheet->qty = ($type == 'per_hour') ? $hours : $qty;
                $timesheet->earn_amount = $timesheet->qty * $timesheet->amount;
                $timesheet->remarks = $remarks;
                $timesheet->updated_at = $updatedAt;
                
                $timesheet->save();
                _msglog('s',$_L['Timesheet_updated_successfully!']);
                echo $timesheet->id();
            }
        break;        
        */
        case 'set-salery-popup-form':
            $id = $routes['2'];
            $d = ORM::for_table('crm_accounts')->find_one($id);
            $ui->assign('d',$d);
            $ui->assign('_theme',$_theme);
            $ui->display('timesheet/set-salery-popup-form.tpl');
        break;      
        
        case 'set-salery-type-post':
            $msg = '';
            $id   = _post('id');
            $salery_type = _post('salery_type');
            $salery_amt = _post('salery_amt');
            $empcode = _post('empcode');
            if($salery_type == ''){
                $msg .= 'Salery type is required <br>';
            }
            if($salery_type == 'per_hour' && $salery_amt == ''){
                $msg .= 'Salery amount is required <br>';
            }
            if ($salery_type == 'per_hour') {
                if($empcode == ''){
                    $msg .= 'Employee code is required <br>';
                }
            }
 
    
            if($msg == ''){
                $d = ORM::for_table('crm_accounts')->find_one($id);
                if($d){
                    $d->salery_type = $salery_type;
                    $d->salery_amt = $salery_amt;
                    if ($salery_type == 'per_hour') {
                        $d->emp_code = $empcode; // Save employee code if hourly
                    }
                    $d->save();
                    _msglog('s','Updated Successfully!');
                    echo $d->id();
                }
            }
            else{
                echo $msg;
            }
        break;
        
        case 'timesheet-popup-form':
            $id = $routes['2'];
            $employee = ORM::for_table('crm_accounts')->find_one($id);
            $employee_timesheet = ORM::for_table('crm_timesheet')->where('date', date('Y-m-d'))->where('employee_id', $id)->where_null('invoice_alocation_id')->find_one();
            $ui->assign('employee',$employee);
            $ui->assign('timesheet',$employee_timesheet);
            $ui->assign('_theme',$_theme);
            $ui->display('timesheet/timesheet-popup-form.tpl');
        break;   
        
        case 'timesheet-entry-post':
            $record_id   = _post('record_id');
            $type        = _post('type');
            $remarks     = _post('remarks');
            $employee_id = _post('employee_id');
            $branch_id = _post('branch_id');
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
                // $timesheet->branch_id = $branch_id;
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
                $insert->branch_id = $branch_id;
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

        case 'transactions_export':

        Event::trigger('contacts/transactions_export/');

        $cid = $routes['2'];; //_post('cid')
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        if($d){
            $tr = ORM::for_table('sys_transactions')
                ->where_raw('(`payerid` = ? OR `payeeid` = ?)', array($cid, $cid))
                ->order_by_desc('id')->find_many();

                $fp = fopen('storage/csv/generate_export.csv', 'w'); //open a csv file
            
                $res['sr_no']   = 'SR NO';
                $res['date']    = 'Date';
                //$res['account'] = 'Customer Name';
                //$res['company'] = 'Company Name';                
                //$res['account'] = 'Account'; 
                $res['type']    = 'Type';   
                $res['amount']  = 'Amount (In Rs.)';  
                $res['desc']    = 'Description';
                $res['dr']      = 'Dr. (In Rs.)';
                $res['cr']      = 'Cr. (In Rs.)';  
                //$res['balance']  = 'balance';
                
                //put Headings on csv file
                fputcsv($fp, $res, ',', '"', "\\");  
                
                //print all data
                $sr_no = 1;
                foreach ($tr as $ds) {

                    $cust = get_type_by_id_multi('crm_accounts', 'id', $cid, 'account,company');

                    $res['sr_no']   = $sr_no++;
                    //$res['account'] = $cust['account'];
                    //$res['company'] = $cust['company'];
                    $res['date']    = $ds['date'];
                    //$res['account'] = $ds['account']; 
                    $res['type']    = $ds['type'];   
                    $res['amount']  = $ds['amount'];  
                    $res['desc']    = $ds['description'];
                    $res['dr']      = $ds['dr'];
                    $res['cr']      = $ds['cr'];  
                    //$res['balance']  = $ds['bal'];           
            
                    //put data on csv file
                    fputcsv($fp, $res, ',', '"', "\\");
                }
                
                //close csv file
                fclose($fp);
                
                //download csv
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename='.$cust['account'].' '.date('dMY').'.csv');
                header('Pragma: no-cache');
                readfile("storage/csv/generate_export.csv");                



        }
        else{
            echo 'No data Found';exit;
        }

        break;


    case 'email':

        Event::trigger('contacts/email/');

        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        if($d){
            $e = ORM::for_table('sys_email_logs')
                ->where('userid',$cid)
                ->order_by_desc('id')->find_many();
            $ui->assign('d',$d);
            $ui->assign('e',$e);
            $ui->display('ajax.contact-emails.tpl');
        }
        else{

        }


        break;


    case 'edit':

        Event::trigger('contacts/edit/');

        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        if($d){
            $fs = ORM::for_table('crm_customfields')->where('ctype','crm')->order_by_asc('id')->find_many();
            $ui->assign('fs',$fs);
            $ui->assign('countries',Countries::all($d['country']));
            $ui->assign('d',$d);
            $tags = Tags::get_all('Contacts');
            $ui->assign('tags',$tags);
            $dtags = explode(',',$d['tags']);
            $ui->assign('dtags',$dtags);

            // find all groups

            $gs = ORM::for_table('crm_groups')->order_by_asc('sorder')->find_array();

            $ui->assign('gs',$gs);
            
            $categories = ORM::for_table('category_employee')->find_many();
            
            $categoryData = [];
            foreach ($categories as $category) {
                $categoryData[] = [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            }
            
            $ui->assign('categoryData', $categoryData);
            
            $g_selected_id = route(4);

            if($g_selected_id){
                $ui->assign('g_selected_id',$g_selected_id);
            }
            else{
                $ui->assign('g_selected_id','');
            }

            $currencies = Model::factory('Models_Currency')->find_array();

            // 🔹 Branch logic
            if($user->roleid == 0){
                // Admin → load all branches
                $branches = ORM::for_table('sys_accounts')
                    ->order_by_asc('account')
                    ->find_array();
            }
            else{
                // Staff → only their branch
                $branches = ORM::for_table('sys_accounts')
                    ->where('id', $user->branch_id)
                    ->find_array();
            }
            $ui->assign('branches', $branches);

            $ui->assign('currencies',$currencies);

            $ui->display('ajax.contact-edit.tpl');
        }
        else{

        }


        break;



    case 'add-activity-post':

        Event::trigger('contacts/add-activity-post/');

        $cid = _post('cid');
        $msg = $_POST['msg'];
        $icon = $_POST['icon'];
        $icon = trim($icon);
        //<a href="#"><i class="fa fa-camera"></i></a>

        $icon = str_replace('<a href="#"><i class="','',$icon);
        $icon = str_replace('"></i></a>','',$icon);
        if($icon == ''){
            $icon = 'fa fa-check';
        }

        if(Validator::Length($msg,1000,5) == false){
            echo $_L['Message Should be between 5 to 1000 characters'];
        }
        else{
            $d = ORM::for_table('sys_activity')->create();
            $d->cid = $cid;
            $d->msg = $msg;
            $d->icon = $icon;
            $d->stime = time();
            $d->sdate = date('Y-m-d');
            $d->o = $user['id'];
            $d->oname = $user['fullname'];
            $d->save();

            echo $cid;
        }

        break;


    case 'activity-delete':

        Event::trigger('contacts/activity-delete/');

        $id = $routes['3'];
        $d = ORM::for_table('sys_activity')->find_one($id);
        $d->delete();
        $cid = $routes['2'];
        r2(U.$myCtrl.'/view/'.$cid.'/','s',$_L['Deleted Successfully']);
        break;

    case 'view':

        if(!has_access($user->roleid, 'customers')) {
            r2(U."dashboard",'e',$_L['You do not have permission']);
        }

        Event::trigger('contacts/view/');

        $id  = $routes['2'];
        $d = ORM::for_table('crm_accounts')->find_one($id);
        if($d){

            $extra_tab = '';
            $extra_jq = '';

            $tab = route(3);

            if(!$tab){

                $tab = 'summary';

            }

            $ui->assign('tab',$tab);

            Event::trigger('contacts/view/_on_start');

            $ui->assign('extra_tab', $extra_tab);

            // invoice count

            $inv_count = ORM::for_table('sys_invoices')->where('userid',$id)->count();

            if($inv_count == ''){
                $inv_count = 0;
            }

            $ui->assign('inv_count',$inv_count);

            //find all activity for this user
//            $ac = ORM::for_table('sys_activity')->where('cid',$id)->limit(20)->order_by_desc('id')->find_many();
//            $ui->assign('ac',$ac);





            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','sn/summernote','sn/summernote-bs3','sn/summernote-application','imgcrop/assets/css/croppic')));




            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'sn/summernote.min','imgcrop/croppic','numeric','profile')));

            $ui->assign('xjq', '
 var cid = $(\'#cid\').val();
    var _url = $("#_url").val();
    var cb = function cb (){



            };




 '.
                $extra_jq);

            $ui->assign('d',$d);

            Event::trigger('contacts/view/_on_display');

            $ui->display('account-profile-alt.tpl');

        }
        else{
            r2(U . 'customers/list/', 'e', $_L['Account_Not_Found']);
        }

        break;

    case 'add-post':

        Event::trigger('contacts/add-post/');
        Event::trigger('contacts/add-post/_on_start');

        $account = _post('account');
        $company = _post('company');
        $email = _post('email');
        $phone = _post('phone');
        $branch_id  = _post('branch_id');

				if( _post('currency') != ''){
					 $currency = _post('currency');
				}else{
					$currency = 1;
				}

        if(isset($_POST['tags']) AND ($_POST['tags']) != ''){
            $tags = $_POST['tags'];
        }
        else{
            $tags = '';
        }

        $address = _post('address');
        $city = _post('city');
        $state = _post('state');
        $zip = _post('zip');				        
		$gst = _post('gst');
		$pan = _post('pan');
        $country = _post('country');
        $msg = '';

//check if tag is already exisit
        if($account == ''){
            $msg .= $_L['Account Name is required'].' <br>';
        }
        if($branch_id == ''){
            $msg .= 'Branch is required'.' <br>';
        }

//check account is already exist
//        $chk = ORM::for_table('crm_accounts')->where('account',$account)->find_one();
//        if($chk){
//            $msg .= 'Account already exist <br>';
//        }

        if($email != ''){
            if(Validator::Email($email) == false){
                $msg .= $_L['Invalid Email'].' <br>';
            }
            $f = ORM::for_table('crm_accounts')->where('email',$email)->find_one();

            if($f){
                $msg .= $_L['Email already exist'].' <br>';
            }
        }

        $gid = _post('group');

        if($gid != ''){
            $g = db_find_one('crm_groups',$gid);
            $gname = $g['gname'];
        }
        else{
            $gid = 0;
            $gname = '';
        }
        $categoryID = _post('categoryId');
        $password = _post('password');
        $cpassword = _post('cpassword');
        $u_password = '';

        if($password != ''){
            if(!Validator::Length($password,15,5)){
                $msg .= 'Password should be between 6 to 15 characters'. '<br>';

            }
            if($password != $cpassword){
                $msg .= 'Passwords does not match'. '<br>';
            }
            $u_password = $password;
            $password = Password::_crypt($password);
        }

        if($msg == ''){

            Tags::save($tags,'Contacts');
            $d = ORM::for_table('crm_accounts')->create();

            $d->account = $account;
            $d->email = $email;
            $d->phone = "91".$phone;
            $d->address = $address;
            $d->city = $city;
            $d->gst_no = $gst;						            
            $d->pan = $pan;						            
			$d->zip = $zip;
            $d->state = $state;
            $d->country = $country;
            $d->employee_category_id = $categoryID;
            $d->tags = Arr::arr_to_str($tags);

            // Save branch_id
            $d->branch_id = $branch_id;

            //others
            $d->fname = '';
            $d->lname = '';
            $d->company = $company;
            $d->jobtitle = '';
            $d->cid = '0';
            $d->o = '0';
            $d->balance = '0.00';
            $d->status = 'Active';
            $d->notes = '';
            $d->password = $password;
            $d->token = '';
            $d->ts = '';
            $d->img = '';
            $d->web = '';
            $d->facebook = '';
            $d->google = '';
            $d->linkedin = '';

            $d->gname = $gname;
            $d->gid = $gid;
            $d->currency = $currency;
            $d->currency = $currency;

            $d->save();
            $cid = $d->id();
            _log($_L['New Contact Added'].' '.$account.' [CID: '.$cid.']','Admin',$user['id']);

            //now add custom fields
            $fs = ORM::for_table('crm_customfields')->where('ctype','crm')->order_by_asc('id')->find_many();
            foreach($fs as $f){
                $fvalue = _post('cf'.$f['id']);
                $fc = ORM::for_table('crm_customfieldsvalues')->create();
                $fc->fieldid = $f['id'];
                $fc->relid = $cid;
                $fc->fvalue = $fvalue;
                $fc->save();
            }
            
            Event::trigger('contacts/add-post/_on_finished');

            // send welcome email if needed
            $send_client_signup_email = _post('send_client_signup_email');

            if(($email != '') && ($send_client_signup_email == 'Yes') && ($u_password != '')){

                $email_data = array();
                $email_data['account'] = $account;
                $email_data['company'] = $company;
                $email_data['password'] = $u_password;
                $email_data['email'] = $email;

                $send_email = Ib_Email::send_client_welcome_email($email_data);
            }
            echo $cid;
        }
        else{
            echo $msg;
        }
    break;

    /*case 'list':

        Event::trigger('contacts/list/');

        $grp = isset($_GET['group']) ? $_GET['group'] : 1;
        $all_groups = ORM::for_table('crm_groups')->find_many();

        $name = _post('name');
        //find all tags
        $t = ORM::for_table('sys_tags')->where('type','contacts')->find_many();
        $ui->assign('t',$t);

        $mode_css = '';
        $mode_js = '';

        if($config['contact_set_view_mode'] == 'search'){

            // Foo Table

            $mode_css = Asset::css('footable/css/footable.core.min');

            $mode_js = Asset::js(array('footable/js/footable.all.min','contacts/mode_search'));

            $d = ORM::for_table('crm_accounts')->where('gid', $grp)->order_by_desc('id')->find_many();

            $paginator['contents'] = '';


        }

        elseif($name != ''){
            $paginator = Paginator::bootstrap('crm_accounts','account','%'.$name.'%');
            $d = ORM::for_table('crm_accounts')->where_like('account','%'.$name.'%')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('id')->find_many();
        }
        elseif(isset($routes[2]) AND ($routes[2]) != '' AND (!is_numeric($routes[2]))){
        $tags = $routes[2];
            $paginator['contents'] = '';
            $d = ORM::for_table('crm_accounts')->where_like('tags','%'.$tags.'%')->order_by_desc('id')->find_many();
        }
        else{
            $paginator = Paginator::bootstrap('crm_accounts');
            $d = ORM::for_table('crm_accounts')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_desc('id')->find_many();
        }

        $ui->assign('d',$d);
        $ui->assign('grp', $grp);
        $ui->assign('all_groups', $all_groups);        
        $ui->assign('paginator',$paginator);
        $ui->assign('xheader', $mode_css);
        $ui->assign('xfooter', $mode_js.'<script type="text/javascript" src="' . $_theme . '/lib/list-contacts.js"></script>');
        $ui->assign('jsvar', '_L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';');
        $ui->display('list-contacts.tpl');

        break;
    */

    case 'list':

    if(!has_access($user->roleid, 'customers')) {
        r2(U."dashboard",'e',$_L['You do not have permission']);
    }

    Event::trigger('contacts/list/');

    // Page data
    $all_groups = ORM::for_table('crm_groups')->order_by_asc('sorder')->find_array();
    $branches   = ORM::for_table('sys_accounts')->order_by_asc('account')->find_array();

    // -------------------
    // tiny helpers (compact & reusable)
    // -------------------

    // find ids from a table where $col LIKE $term (returns numeric id array)
    $find_ids = function(string $table, string $col, string $term) {
        if ($term === '') return [];
        $rows = ORM::for_table($table)->select('id')->where_like($col, $term)->find_array();
        return array_map('intval', array_column($rows, 'id'));
    };

    // make id => name map for a table
    $make_map = function(string $table, string $name_col) {
        $map = [];
        $rows = ORM::for_table($table)->find_many();
        foreach ($rows as $r) $map[$r->id] = $r->{$name_col};
        return $map;
    };

    // build search_sql & params for global search (including group/branch name matching)
    $build_search = function(string $search) use ($find_ids) {
        $search_sql = '';
        $params = [];
        if ($search !== '') {
            $g = '%' . $search . '%';
            $params = [$g, $g, $g, $g]; // account, phone, email, company
            $clauses = ['account LIKE ?', 'phone LIKE ?', 'email LIKE ?', 'company LIKE ?'];

            // match groups/branches by name and add IN(...) clauses if any
            $mg = $find_ids('crm_groups', 'gname', $g);
            if (!empty($mg)) {
                $ph = implode(',', array_fill(0, count($mg), '?'));
                $clauses[] = 'gid IN (' . $ph . ')';
                $params = array_merge($params, $mg);
            }
            $mb = $find_ids('sys_accounts', 'account', $g);
            if (!empty($mb)) {
                $ph = implode(',', array_fill(0, count($mb), '?'));
                $clauses[] = 'branch_id IN (' . $ph . ')';
                $params = array_merge($params, $mb);
            }

            $search_sql = '(' . implode(' OR ', $clauses) . ')';
        }
        return [$search_sql, $params];
    };

    // -------------------
    // DataTables server-side
    // -------------------
    if (isset($_GET['draw'])) {

        // read DT params
        $draw  = intval($_GET['draw']);
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $len   = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $search = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';

        $orderIdx = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
        $orderDir = (isset($_GET['order'][0]['dir']) && $_GET['order'][0]['dir'] === 'asc') ? 'asc' : 'desc';

        // filters
        $filter_group  = isset($_GET['group']) ? $_GET['group'] : '';
        $filter_branch = isset($_GET['branch']) ? $_GET['branch'] : '';

        // column map (displayed): id, branch, name, phone, group
        $col_map = [0=>'id', 1=>'branch_id', 2=>'account', 3=>'phone', 4=>'gid'];

        // prepare search SQL & params
        list($search_sql, $search_params) = $build_search($search);

        // total records
        $recordsTotal = (int) ORM::for_table('crm_accounts')->count();

        // filtered count (apply dropdown filters + search)
        $count_q = ORM::for_table('crm_accounts');
        if ($filter_group !== '' && $filter_group !== 'all') $count_q->where('gid', $filter_group);
        if ($filter_branch !== '' && $filter_branch !== 'all') $count_q->where('branch_id', $filter_branch);
        if ($search_sql) $count_q->where_raw($search_sql, $search_params);
        $recordsFiltered = (int) $count_q->count();

        // fetch rows (same filters)
        $q = ORM::for_table('crm_accounts');
        if ($filter_group !== '' && $filter_group !== 'all') $q->where('gid', $filter_group);
        if ($filter_branch !== '' && $filter_branch !== 'all') $q->where('branch_id', $filter_branch);
        if ($search_sql) $q->where_raw($search_sql, $search_params);

        // ordering
        if (isset($col_map[$orderIdx])) {
            $orderCol = $col_map[$orderIdx];
            $orderDir === 'asc' ? $q->order_by_asc($orderCol) : $q->order_by_desc($orderCol);
        } else {
            $q->order_by_desc('id');
        }

        // pagination
        if ($len != -1) $q->offset($start)->limit($len);

        $rows = $q->find_many();

        // maps (single queries)
        $group_map  = $make_map('crm_groups', 'gname');
        $branch_map = $make_map('sys_accounts', 'alias');

        // assemble data in display order: id, branch, name, phone, group, manage
        $data = [];
        $baseUrl = rtrim(U, '/');

        foreach ($rows as $r) {
            $view = '<a href="'. $baseUrl . 'contacts/view/' . $r->id . '/" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> ' . $_L['View'] . '</a>';
            $del  = '<a href="'. $baseUrl . 'delete/crm-user/' . $r->id . '/" class="btn btn-danger btn-xs cdelete" id="uid' . $r->id . '"><i class="fa fa-trash"></i> ' . $_L['Delete'] . '</a>';
            $data[] = [
                intval($r->id),
                htmlspecialchars($branch_map[$r->branch_id] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($r->account, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($r->phone, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($group_map[$r->gid] ?? '', ENT_QUOTES, 'UTF-8'),
                $view . ' ' . $del
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
        exit;
    }

    // -------------------
    // Normal page render
    // -------------------
    $ui->assign('all_groups', $all_groups);
    $ui->assign('branches', $branches);
    $ui->assign('t', ORM::for_table('sys_tags')->where('type','contacts')->find_many());
    $ui->assign('xfooter', '<script type="text/javascript" src="' . $_theme . '/lib/list-contacts.js"></script>');
    $ui->assign('jsvar', '_L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';');
    $ui->display('list-contacts2.tpl');

    break;




    case 'edit-post':

        Event::trigger('contacts/edit-post/');


        $id = _post('fcid');
        $d = ORM::for_table('crm_accounts')->find_one($id);
        if($d){

            $old_account = $d->account;

            $account = _post('account');
            $company = _post('company');

            $email = _post('edit_email');
            $branch_id  = _post('branch_id');

            if(isset($_POST['tags'])){
                $tags = $_POST['tags'];
            }
            else{
                $tags = '';
            }

            $currency = _post('currency','0');

            $phone = _post('phone');
            $address = _post('address');
            $city = _post('city');
            $state = _post('state');
            $zip = _post('zip');						            
						$gst = _post('gst');
            $country = _post('country');
            $msg = '';

            if($account == ''){
                $msg .= $_L['Account Name is required']. ' <br>';
            }
//            if($tags != ''){
//                $pieces = explode(',', $tags);
//                foreach($pieces as $element)
//                {
//                    $tg = ORM::for_table('sys_tags')->where('text',$element)->where('type','Contacts')->find_one();
//                    if(!$tg){
//                        $tc = ORM::for_table('sys_tags')->create();
//                        $tc->text = $element;
//                        $tc->type = 'Contacts';
//                        $tc->save();
//                    }
//                }
//            }

            // Sadia ================= From V 2.4

            Tags::save($tags,'Contacts');


            //check email already exist




//            if($address == ''){
//                $msg .= 'Address is required <br>';
//            }
//            if($city == ''){
//                $msg .= 'City is required <br>';
//            }
//            if($state == ''){
//                $msg .= 'State is required <br>';
//            }
//            if($zip == ''){
//                $msg .= 'ZIP is required <br>';
//            }
//            if($country == ''){
//                $msg .= 'Country is required <br>';
//            }
                if($email != ''){

                if($email != ($d['email'])){
                    $f = ORM::for_table('crm_accounts')->where('email',$email)->find_one();

                    if($f){
                        $msg .= $_L['Email already exist'].' <br>';
                    }
                }
                if(Validator::Email($email) == false){
                    $msg .= $_L['Invalid Email'].' <br>';
                }
            }
//            if($phone != ''){
//                if(!is_numeric($phone)){
//                    $msg .= $_L['Invalid Phone'].' <br>';
//                }
//            }

            $gid = _post('group');

            if($gid != ''){
                $g = db_find_one('crm_groups',$gid);
                $gname = $g['gname'];
            }
            else{
                $gid = 0;
                $gname = '';
            }
            
            $categoryID = _post('categoryId');
            $password = _post('password');




            if($msg == ''){


                $d = ORM::for_table('crm_accounts')->find_one($id);
                $d->account = $account;
                $d->company = $company;


                $d->email = $email;
                $d->tags = Arr::arr_to_str($tags);
                $d->phone = $phone;
                $d->address = $address;
                $d->city = $city;
                $d->zip = $zip;								                
								$d->gst_no = $gst;
                $d->state = $state;
                $d->country = $country;

                // v 4.2

                $d->gname = $gname;
                $d->gid = $gid;
                
                $d->employee_category_id = $categoryID;
                
                // build 4550

                $d->currency = $currency;

                if($password != ''){

                    $d->password = Password::_crypt($password);

                }
                $d->branch_id = $branch_id;

                $d->save();


                //delete existing records
                $exf = ORM::for_table('crm_customfieldsvalues')->where('relid',$id)->delete_many();
                $fs = ORM::for_table('crm_customfields')->order_by_asc('id')->find_many();
                foreach($fs as $f){
                    $fvalue = _post('cf'.$f['id']);
                    $fc = ORM::for_table('crm_customfieldsvalues')->create();
                    $fc->fieldid = $f['id'];
                    $fc->relid = $id;
                    $fc->fvalue = $fvalue;
                    $fc->save();
                }

                // check account name changed

                if($account != $old_account){

                    // change invoice account

//                    $inv = ORM::for_table('sys_invoices')->where('account',$old_account);
//                    $inv->account = $account;
//                    $inv->save();

                    $sql = "update sys_invoices set account='$account' where account='$old_account'";

                    ORM::execute($sql);



                }

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


    case 'more':

        Event::trigger('contacts/more/');

        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        if($d){
            $ui->assign('countries',Countries::all($d['country']));
            $ui->assign('d',$d);
            $ui->display('ajax.contact-more.tpl');
        }
        else{

        }


        break;

    case 'edit-more':

        Event::trigger('contacts/edit-more/');

        $id = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($id);
        if($d){
            $img = _post('picture');
            $facebook = _post('facebook');
            $google = _post('google');
            $linkedin = _post('linkedin');

            $msg = '';



            //check email already exist





            if($msg == ''){


                $d = ORM::for_table('crm_accounts')->find_one($id);

                $d->img = $img;
                $d->facebook = $facebook;
                $d->google = $google;
                $d->linkedin = $linkedin;
                $d->save();
                echo $d->id();
            }
            else{
                echo $msg;
            }

        }
        else{
            r2(U.$myCtrl.'/list/', 'e', $_L['Account_Not_Found']);
        }


        break;


    case 'edit-notes':

        Event::trigger('contacts/edit-notes/');

        $id = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($id);
        if($d){

            $notes = _post('notes');

            $msg = '';



            //check email already exist





            if($msg == ''){


                $d = ORM::for_table('crm_accounts')->find_one($id);


                $d->notes = $notes;
                $d->save();
                echo $d->id();
            }
            else{
                echo $msg;
            }

        }
        else{
            r2(U.$myCtrl.'/list/', 'e', $_L['Account_Not_Found']);
        }


        break;

    case 'render-address':

        Event::trigger('contacts/render-address/');

        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        $address = $d['address'];
        $city = $d['city'];
        $state = $d['state'];
        $zip = $d['zip'];
        $country = $d['country'];
        echo "$address
$city
$state $zip
$country
";
        break;



        case 'render-contact':

            Event::trigger('contacts/render-contact/');

            $cid = _post('cid');
            $d = ORM::for_table('crm_accounts')->find_one($cid);

            $measurment = json_decode($d['measurements'], true);

            $contact_info = array('name' => $d['account'], 'contact' => $d['phone'], 'location' => $d['address'], 'length' => $measurment['length'], 'shoulder' => $measurment['shoulder'], 'sleeves' => $measurment['sleeves'], 'armole' => $measurment['armole'], 'cuff' => $measurment['cuff'], 'chest' => $measurment['chest'], 'waist' => $measurment['waist'], 'hipps' => $measurment['hipps']);

            echo json_encode($contact_info);
        
        break;



        case 'render-tax':

        Event::trigger('contacts/render-address/');

        $cid = _post('cid');
        $u = ORM::for_table('crm_accounts')->find_one($cid);

        if(trim(strtolower($u['country'])) == 'india')
        {
            $default_tax_id = (in_array(trim(strtolower($u['state'])), array('mh', 'maharashtra'))) ? 1 : 6;
        }
        else
        {
            $default_tax_id = 4;
        }

        echo $default_tax_id;
        break;
        

    case 'send_email':

        Event::trigger('contacts/send_email/');

        $msg = '';
        $cid = _post('cid');
        $d = ORM::for_table('crm_accounts')->find_one($cid);
        $email = $d['email'];
        $toname = $d['account'];
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
            Notify_Email::_send($toname,$email,$subject,$message,$cid);
            echo $cid;

        }
        else{
            echo $msg;
        }
        break;


    case 'modal_add':

        Event::trigger('contacts/modal_add/');

        $ui->assign('countries',Countries::all($config['country'])); // may add this $config['country_code']
        $ui->display('modal_add_contact.tpl');


        break;


    case 'set_view_mode':

        Event::trigger('contacts/set_view_mode/');

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

            update_option('contact_set_view_mode',$mode);

        }

        r2(U.'contacts/list/');

        break;



    case 'export_csv':


        $fileName = 'contacts_'.time().'.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen( 'php://output', 'w' );

        $headerDisplayed = false;

       // $results = ORM::for_table('crm_Accounts')->find_array();
        $results = db_find_array('crm_accounts',array('id','account','company','phone','email','address','city','state','zip','country','balance','tags','gst_no'));

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



    case 'dev_demo_data':


        // this only work with dev mode
        is_dev();





        break;

    case 'import_csv':


        $ui->assign('xheader', Asset::css(array('dropzone/dropzone')));


        $ui->assign('xfooter', Asset::js(array('dropzone/dropzone','contacts/import')));



        $ui->display('contacts_import.tpl');



        break;

    case 'csv_upload':

        $uploader   =   new Uploader();
				
        $uploader->setDir('application/storage/temp/');
       // $uploader->sameName(true);
        $uploader->setExtensions(array('csv'));  //allowed extensions list//
        if($uploader->uploadFile('file')){   //txtFile is the filebrowse element name //
            $uploaded  =   $uploader->getUploadName(); //get uploaded file name, renames on upload//

            $_SESSION['uploaded'] = $uploaded;

        }else{//upload failed
            _msglog('e',$uploader->getMessage()); //get upload error message
        }


        break;

    case 'csv_uploaded':


        if(isset($_SESSION['uploaded'])){

            $uploaded = $_SESSION['uploaded'];

          // _msglog('s',$uploaded);

//            $csvData = file_get_contents('application/storage/temp/'.$uploaded);
//            $lines = explode(PHP_EOL, $csvData);
//            $contacts = array();
//            foreach ($lines as $line) {
//                $contacts[] = str_getcsv($line);
//            }




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

//            ob_start();
//            var_dump($contacts);
//            $result = ob_get_clean();
//
//            _msglog('s',$result);



        }
        else{

            _msglog('e','An Error Occurred while uploading the files');

        }


        break;


    case 'groups':

        // find all groups

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

            //check same group already exist

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

            // update all gname in contacts

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

            // find group


            $ds = ORM::for_table('crm_accounts')->where('gid',$gid)->where_not_equal('email','')->select('account')->select('email')->order_by_desc('id')->find_array();

            $ui->assign('ds',$ds);

            $ui->assign('xheader', Asset::css(array('s2/css/select2.min','sn/summernote','sn/summernote-bs3','sn/summernote-application')));




            $ui->assign('xfooter', Asset::js(array('s2/js/select2.min','s2/js/i18n/'.lan(),'sn/summernote.min','contacts/group_email')));
            $ui->display('contacts_group_email.tpl');

        }


        break;


    case 'group_email_post':


//        $recipients = array(
//            'person1@domain.com' => 'Person One',
//            'person2@domain.com' => 'Person Two',
//            // ..
//        );
//        foreach($recipients as $email => $name)
//        {
//            $mail->AddAddress($email, $name);
//        }



        $emails = $_POST['emails'];
        $subject = $_POST['subject'];
        $msg = $_POST['msg'];


        Ib_Email::bulk_email($emails,$subject,$msg,$user->username);

        echo 'Mail Sent!';


//       if(Ib_Email::bulk_email($emails,$subject,$msg,$user->username)){
//
//           echo 'Mail Sent!';
//
//       }
//
//        else{
//
//            echo 'An Error Occurred while sending email.';
//
//        }




        break;


    case 'companies':

        $ui->assign('jsvar', '
_L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';
 ');

        $ui->assign('_application_menu', 'companies');
        $ui->assign('_st', $_L['Companies']);

// find all companies

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

//            $val[''] = $company->;
        }
        else{
            $f_type = 'create';
            $val['company_name'] = '';
            $val['url'] = 'http://';
            $val['email'] = '';
            $val['phone'] = '';
            $val['logo_url'] = '';
//            $val[''] = '';
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

    case 'ajax_search_contacts':
        $term = _get('q'); // use _get since Select2 sends q via GET
        $page = intval(_get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $gid = _get('gid'); // pass gid from frontend if needed
        $query = ORM::for_table('crm_accounts')
            ->table_alias('c')
            ->select('c.id')
            ->select('c.account')
            ->select('c.phone')
            ->select('b.alias', 'branch_alias')
            ->join('sys_accounts', ['c.branch_id', '=', 'b.id'], 'b');
        // apply gid filter only if provided
        if (!empty($gid)) {
            $query->where('c.gid', $gid);
        }
        if (!empty($term)) {
            $like = '%' . $term . '%';
            $query->where_raw(
                '(c.account LIKE ? OR c.phone LIKE ? OR c.email LIKE ? OR b.alias LIKE ?)',
                [$like, $like, $like, $like]
            );
        }

        // Clone for total count
        $totalQuery = clone $query;
        $totalCount = $totalQuery->count();

        $rows = $query
            ->order_by_desc('c.id')
            ->limit($perPage)
            ->offset($offset)
            ->find_array();

        $results = [];
        foreach ($rows as $r) {
            $text = $r['account'];
            if (!empty($r['phone'])) {
                $text .= ' - ' . $r['phone'];
            }
            if (!empty($r['branch_alias'])) {
                $text .= ' [' . $r['branch_alias'] . ']';
            }
            $results[] = [
                'id'   => $r['id'],
                'text' => $text
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'results' => $results,
            'total_count' => $totalCount
        ]);
        break;




    default:
        echo 'action not defined';
}