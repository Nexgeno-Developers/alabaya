<?php
_auth();

$ui->assign('_title', 'Timesheet - '. $config['CompanyName']);
$ui->assign('_st', 'Timesheet');
$user = User::_info();
$ui->assign('user',$user);

$action = route(1);

if($action == ''){

    if(isset($config['timesheet'])){
        $action = $config['timesheet'];
    }
    else{
        $action = 'index';
    }

}

if(!has_access($user->roleid, 'timesheet')) {
    r2(U."dashboard",'e',$_L['You do not have permission']);
}

switch($action){
   
    case 'index':
        
        Event::trigger('index');
        $_L = 'Timesheet';
        $ui->assign('_application_menu', 'timesheet');
        $ui->assign('_st', $_L);
        $ui->assign('_title', $_L .' - '. $config['CompanyName']);

        // Load branches for branch select / filter
        $branches = ORM::for_table('sys_accounts')
            ->select('id')
            ->select('account')
            ->select('alias')
            ->order_by_asc('account')
            ->find_array();

        $ui->assign('branches', $branches);
                
        // Pick initial branch for employee load:
        // If super-admin show all per-hour employees initially (or first branch if you prefer),
        // otherwise restrict to user's branch only.
        if ($user->roleid == 0) {
            // Super admin: show all per_hour employees (optional: you can limit to first branch instead)
            $employees = ORM::for_table('crm_accounts')
                ->select('id')
                ->select('account')
                ->where('salery_type', 'per_hour')
                ->order_by_asc('account')
                ->find_array();
        } else {
            // Non-admin: only employees for that branch
            $employees = ORM::for_table('crm_accounts')
                ->select('id')
                ->select('account')
                ->where('salery_type', 'per_hour')
                ->where('branch_id', $user->branch_id)
                ->order_by_asc('account')
                ->find_array();
        }
        
        $ui->assign('APP_URL', APP_URL);
        $ui->assign('hourly_employee_name', $employees);
        $ui->assign('employees', $employees);
        $ui->assign('xheader',Asset::css(array('modal')));
        $ui->assign('xfooter',Asset::js(array('modal')));
        $ui->assign('xfooter2',Asset::js(array('jquery.dataTables')));
        // $ui->display('dashboard_timesheet.tpl');
        $ui->display('dashboard_all_employee_timesheet.tpl');
    break;
    
            
    // AJAX endpoint: return employees JSON for a branch
    case 'timesheet-ajax-employees':
        $user = User::_info();
        $branch_id = _post('branch_id');

        // If non-admin and no branch posted, force user branch
        if (empty($branch_id) && $user->roleid != 0) {
            $branch_id = $user->branch_id;
        }

        if (!empty($branch_id)) {
            $emps = ORM::for_table('crm_accounts')
                ->select('id')
                ->select('account')
                ->where('gid', 3)
                ->where('salery_type', 'per_hour')
                ->where('branch_id', $branch_id)
                ->order_by_asc('account')
                ->find_array();
        } else {
            // If branch not provided and superadmin, return all per_hour employees
            $emps = ORM::for_table('crm_accounts')
                ->select('id')
                ->select('account')
                ->where('gid', 3)
                ->where('salery_type', 'per_hour')
                ->order_by_asc('account')
                ->find_array();
        }

        header('Content-Type: application/json');
        echo json_encode($emps);

    break;

    // case 'holiday-timesheet-ajax':
    //     Event::trigger('timesheet/holiday-timesheet-ajax');
    
    //     $employee_ids = isset($_POST['employee_id']) ? $_POST['employee_id'] : [];
    //     $checkins = isset($_POST['checkin-holiday']) ? $_POST['checkin-holiday'] : [];
    //     $checkouts = isset($_POST['checkout-holiday']) ? $_POST['checkout-holiday'] : [];
    //     $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : [];
        
    //     $createdAt = date('Y-m-d H:i:s');
    //     $updatedAt = date('Y-m-d H:i:s');
        
    //     $nonInsertedData = [];
        
    //     foreach ($employee_ids as $employee_id) {
    //         $employees = ORM::for_table('crm_accounts')
    //             ->select('id')
    //             ->select('account')
    //             ->select('salery_type')
    //             ->select('salery_amt')
    //             ->where('id', $employee_id)
    //             ->find_many();
            
    //         $type = null;
    //         $amount = null;
    //         $qty = null;
            
    //         foreach ($employees as $employee) {
    //             $type = $employee->salery_type;
    //             $amount = $employee->salery_amt; // Fetching the salary amount
    //         }
      
    //         if (!empty($checkins) && !empty($checkouts)) {  
    //             // var_dump($checkins);
    //             // var_dump($checkouts);
    //             for ($i = 0; $i < count($checkins); $i++) {
    //             // Check if a record exists for today's date and the given employee ID
    //             $checkinDate = date('Y-m-d', strtotime($checkins[$i]));
    //             $timesheet = ORM::for_table('crm_timesheet')
    //                 ->where('employee_id', $employee_id)
    //                 ->where_raw('DATE(`checkin`) = ?', [$checkinDate])
    //                 ->find_one();
    //                 // Loop through each pair of check-in and check-out times
                    
    //                 if ($timesheet) {
    //                     // If a record already exists, add it to the non-inserted data list
    //                     $nonInsertedData[] = [
    //                         'employee_id' => $employee_id,
    //                         'checkin' => $checkins[$i],
    //                         'checkout' => $checkouts[$i],
    //                         'remarks' => $remarks[$i]
    //                     ];
    //                     $response = [
    //                         'non_inserted_data' => $nonInsertedData
    //                     ];

    //                     echo json_encode($response);
    //                     // echo 'update successfully';
    //                 } else {
    //                     // Insert new record
    //                     $date = date('Y-m-d', strtotime($checkins[$i]));
    //                     $insert = ORM::for_table('crm_timesheet')->create();
    //                     $insert->employee_id = $employee_id;
    //                     $insert->checkin = date('Y-m-d H:i:s', strtotime($checkins[$i]));
    //                     $insert->checkout = date('Y-m-d H:i:s', strtotime($checkouts[$i]));
    //                     $insert->remarks = $remarks[$i];
                        
    //                     $checkIn = strtotime($insert->checkin);
    //                     $checkOut = strtotime($insert->checkout);
    //                     $seconds_diff = $checkOut - $checkIn;
    //                     $hours = $seconds_diff / (60 * 60); // Convert seconds to hours
            
    //                     $insert->amount = $amount;
    //                     $insert->date = $date;
    //                     $insert->qty = ($type == 'per_hour') ? $hours : $qty;
    //                     $insert->earn_amount = $insert->qty * $insert->amount;
    //                     $insert->created_at = $createdAt;
    //                     $insert->updated_at = $updatedAt;
            
    //                     $insert->save();
    //                     _msglog('s', $_L['CheckIn_successfully!']);
    //                     echo $insert->id();
    //                     echo 'insert successfully';
    //                 }
    //             }
    //         }
    //     }
    // break;
    
    
    case 'holiday-timesheet-ajax':
    Event::trigger('timesheet/holiday-timesheet-ajax');

    // $dates = isset($_POST['date']) ? $_POST['date'] : [];
    // $checkins = isset($_POST['checkin-holiday']) ? $_POST['checkin-holiday'] : [];
    // $checkouts = isset($_POST['checkout-holiday']) ? $_POST['checkout-holiday'] : [];
    $dates = $_POST['date'] ?? [];
    $checkin_times = $_POST['checkin-holiday'] ?? [];
    $checkout_times = $_POST['checkout-holiday'] ?? [];

    $checkins = [];
    $checkouts = [];

    foreach ($dates as $i => $date) {
        $checkins[] = $date . ' ' . ($checkin_times[$i] ?? '00:00') . ':00';
        $checkouts[] = $date . ' ' . ($checkout_times[$i] ?? '00:00') . ':00';
    }

    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : [];

    $inserted_data = [];
    $nonInsertedData = [];

    foreach ($remarks as $i => $remark) {
        $employee_ids = $_POST['employee_id_' . $i] ?? [];

        foreach ($employee_ids as $employee_id) {
            
            // Fetch employee account name using the employee_id
            $employee_name = ORM::for_table('crm_accounts')->where('id', $employee_id)->find_one()->account ?? 'N/A';

            // Check if entry already exists for the same date
            $checkinDate = date('Y-m-d', strtotime($checkins[$i]));
            $timesheet = ORM::for_table('crm_timesheet')
                ->where('employee_id', $employee_id)
                ->where_raw('DATE(`checkin`) = ?', [$checkinDate])
                ->find_one();

            if ($timesheet) {
                $nonInsertedData[] = [
                    'employee_name' => $employee_name,
                    'employee_id' => $employee_id,
                    'checkin' => $checkins[$i],
                    'checkout' => $checkouts[$i],
                    'remarks' => $remark
                ];
            } else {
                // Insert new record
                $insert = ORM::for_table('crm_timesheet')->create();
                $insert->employee_id = $employee_id;
                $insert->checkin = date('Y-m-d H:i:s', strtotime($checkins[$i]));
                $insert->checkout = date('Y-m-d H:i:s', strtotime($checkouts[$i]));
                $insert->remarks = $remark;

                // Calculate work duration and earnings
                $hours = (strtotime($insert->checkout) - strtotime($insert->checkin)) / 3600;

                // Fetch salary details
                $employee = ORM::for_table('crm_accounts')
                    ->select('salery_type')
                    ->select('salery_amt')
                    ->select('branch_id')
                    ->find_one($employee_id);

                $insert->branch_id = $employee->branch_id ?? null;
                $insert->amount = $employee->salery_amt ?? 0;
                $insert->qty = ($employee->salery_type == 'per_hour') ? $hours : 1; // Default 1 for full day
                $insert->earn_amount = $insert->qty * $insert->amount;
                $insert->date = date('Y-m-d', strtotime($checkins[$i]));
                $insert->created_at = date('Y-m-d H:i:s');
                $insert->updated_at = date('Y-m-d H:i:s');

                $insert->save();

                $inserted_data[] = [
                    'id' => $insert->id(),
                    'employee_name' => $employee_name,
                    'employee_id' => $employee_id,
                    'checkin' => $insert->checkin,
                    'checkout' => $insert->checkout,
                    'remarks' => $remark
                ];
            }
        }
    }

    // Return the response
    echo json_encode([
        'non_inserted_data' => $nonInsertedData,
        'inserted_data' => $inserted_data
    ]);
    break;


    case 'timesheet-list-ajax-timesheet':  
        Event::trigger('timesheet/timesheet-list-ajax-timesheet');
        $response = array();
            
        try {
            ## Read value
            $draw = $_POST['draw'];
            $start = $_POST['start'];
            $rowperpage = $_POST['length'];
            $columnIndex = $_POST['order'][0]['column'];
            $columnName = $_POST['columns'][$columnIndex]['data'];
            $columnSortOrder = $_POST['order'][0]['dir'];
            
            $employee_name = $_POST['display_employee_name'] ?? '';
            $salery_type = $_POST['salery_type'] ?? '';
            $employee_id = $_POST['employee_id2'] ?? '';
            $todate = $_POST['todate'] ?? '';
            $fromdate = $_POST['fromdate'] ?? '';
            
            // Initialize query for counting total records
            $totalRecordQuery = ORM::for_table('crm_timesheet')->select('crm_timesheet.id');
            
            // Join crm_accounts table once
            $totalRecordQuery->left_outer_join('crm_accounts', ['crm_timesheet.employee_id', '=', 'crm_accounts.id']);
            
            // Apply filters based on inputs
            if ($employee_id) {
                $totalRecordQuery->where('crm_timesheet.employee_id', $employee_id);
            }
            
            if ($fromdate && $todate) {
                $totalRecordQuery->where_gte('crm_timesheet.date', $fromdate)->where_lte('crm_timesheet.date', $todate);
            }
            
            if ($employee_name) {
                $totalRecordQuery->where_like('crm_accounts.account', "%$employee_name%");
            }
            
            if ($salery_type) {
                $totalRecordQuery->where('crm_accounts.salery_type', $salery_type);
            }
            
            if (!empty($_POST['branch_id'])) {
                $branch_id = (int) $_POST['branch_id']; // cast to int for safety
                if ($branch_id > 0) {
                    $totalRecordQuery->where('crm_timesheet.branch_id', $branch_id);
                }
            }

            // Count total records
            $totalRecordCount = $totalRecordQuery->count();
                        
            // Query 1: Calculate total earnings without pagination
            $earnAmountSumTotal = ORM::for_table('crm_timesheet')
                ->select_expr('SUM(earn_amount)', 'total_earnings')  // Calculate sum of earn_amount
                ->left_outer_join('crm_accounts', ['crm_timesheet.employee_id', '=', 'crm_accounts.id'])  // Join with crm_accounts
                ->left_outer_join('sys_accounts', ['crm_timesheet.branch_id', '=', 'sys_accounts.id']); // join branch using crm_timesheet.branch_id
            
            // Apply conditions (same conditions as the second query)
            if ($employee_id) {
                $earnAmountSumTotal->where('crm_timesheet.employee_id', $employee_id);
            }
            
            if ($fromdate && $todate) {
                $earnAmountSumTotal->where_gte('crm_timesheet.date', $fromdate)->where_lte('crm_timesheet.date', $todate);
            }
            
            if ($employee_name) {
                $earnAmountSumTotal->where_like('crm_accounts.account', "%$employee_name%");
            }
            
            if ($salery_type) {
                $earnAmountSumTotal->where('crm_accounts.salery_type', $salery_type);
            }
            
            if (!empty($_POST['branch_id'])) {
                $branch_id = (int) $_POST['branch_id'];
                if ($branch_id > 0) {
                    $earnAmountSumTotal->where('crm_timesheet.branch_id', $branch_id);
                }
            }

            // Fetch the total earnings sum
            $earnAmountSumTotalResult = $earnAmountSumTotal->find_one();
            $earnAmountSumTotal = $earnAmountSumTotalResult ? $earnAmountSumTotalResult->total_earnings : 0;
            
           // Query 2: Fetch the paginated records
            $recordQuery = ORM::for_table('crm_timesheet')
                ->select('crm_timesheet.*')  // Select all fields from crm_timesheet
                ->select('crm_accounts.account')  // Select crm_accounts.account
                ->select('crm_accounts.salery_type')  // Select crm_accounts.salery_type
                ->selectExpr("CASE WHEN crm_timesheet.invoice_alocation_id IS NOT NULL THEN 'per_piece' ELSE crm_accounts.salery_type END AS salery_type")  // Set salery_type to per_piece if invoice_allocation_id is present
                ->selectExpr('sys_accounts.alias', 'branch') // branch name
                ->left_outer_join('crm_accounts', ['crm_timesheet.employee_id', '=', 'crm_accounts.id'])  // Join with crm_accounts
                ->left_outer_join('sys_accounts', ['crm_timesheet.branch_id', '=', 'sys_accounts.id']);

            // Apply the same conditions to fetch paginated records
            if ($employee_id) {
                $recordQuery->where('crm_timesheet.employee_id', $employee_id);
            }

            if ($fromdate && $todate) {
                $recordQuery->where_gte('crm_timesheet.date', $fromdate)->where_lte('crm_timesheet.date', $todate);
            }

            if ($employee_name) {
                $recordQuery->where_like('crm_accounts.account', "%$employee_name%");
            }

            if ($salery_type) {
                $recordQuery->where('crm_accounts.salery_type', $salery_type);
            }
            
            if (!empty($_POST['branch_id'])) {
                $branch_id = (int) $_POST['branch_id'];
                if ($branch_id > 0) {
                    $recordQuery->where('crm_timesheet.branch_id', $branch_id);
                }
            }

            // Fetch the paginated records
            $records = $recordQuery->offset($start)->limit($rowperpage)->find_many();
            
            // Calculate the earnings sum for the displayed (paginated) records
            $earnAmountSum = 0;
            foreach ($records as $record) {
                $earnAmountSum += $record->earn_amount;
            }

            // Prepare data for DataTables
            $data = array();
            $sr = $start + 1;
            foreach($records as $record){
                // echo'<pre>';
                // var_dump($record);
                // echo'</pre>';
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
                        $invoicenum = '<a href="' . APP_URL . '/?ng=invoices/view/' . $invoice_id . '/" target="_blank">' . $invoice->invoicenum . '</a>';
                    }
                }
                $employee_name = $record->account ?? '';
                $salery_type = $record->salery_type ?? '';
                $branch_name = $record->branch ?? '';
                $branch_id_val = $record->branch_id ?? $record->branch_id;
                // $employee = ORM::for_table('crm_accounts')->where('id', $record->employee_id)->find_one();
                // if ($employee) {
                // $employee_name = $employee->account;
                // $salery_type = $employee->salery_type;
                // }  else {
                //     // If employee record is not found, set default values or handle the case as needed
                //     $employee_name = '';
                //     $salery_type = '';
                // }
                
                $event1 = "edit_timesheet_modal('".$record->id."')";
                $edit = '<a href="javascript:void(0);" class="btn btn-primary btn-xs" onclick="'.$event1.'"><i class="fa fa-edit"></i> edit</a>';  
                // $invoicenum = $record->invoicenum ? '<a href="'.APP_URL.'/?ng=invoices/view/'.$record->invoice_id.'/" target="_blank">'.$record->invoicenum.'</a>' : 'N/A';
                $data[] = array( 
                "sr"                => $sr,
                "salery_type"       => $salery_type,
                "branch"                => $branch_name,   // branch name (from sys_accounts.account via crm_timesheet.branch_id)
                "branch_id"             => $branch_id_val, // branch id (from crm_timesheet.branch_id)
                "display_employee_name"     => $employee_name,
                "employee_id"       => $record->employee_id,
                "checkin"           => $record->checkin,
                "checkout"          => $record->checkout,
                "qty"               => $record->qty,
                "amount"            => $record->amount,
                "earn_amount"       => $record->earn_amount,
                "earnAmountSum"     => $totalRecordCount < 10 ? $earnAmountSum : $earnAmountSumTotal,
                "invoicenum"        => $invoicenum,
                "remarks"           => $record->remarks,
                "date"              => $record->date,
                "action"            => $edit,
                ); 
                $sr++;	
            }

            // Prepare response for DataTables
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecordCount,
                "iTotalDisplayRecords" => $totalRecordCount,
                "aaData" => $data,
                "earnAmountSum" => $totalRecordCount < 10 ? $earnAmountSum : $earnAmountSumTotal,
                "salery_type"     => $salery_type,
                "display_employee_name"     => $employee_name
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
    break;


    case 'timesheet-edit-timesheet-modal': //in use
            Event::trigger('timesheet/timesheet-edit-timesheet-modal');
            $timesheet_id = $routes['2'];
            $timesheet = ORM::for_table('crm_timesheet')->find_one($timesheet_id);
            if ($timesheet) {
                $employee_id = $timesheet->employee_id;
                $employee = ORM::for_table('crm_accounts')->where('id', $employee_id)->find_one();
                if ($employee) {
                    $salery_type = $employee->salery_type;
                    
                } else {
                    // Handle the case where employee record is not found
                    $salery_type = 'no type';
                }
            } else {
                    $salery_type = 'no type and timesheet';
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
        
    case 'timesheet-edit-timesheet-post':
        Event::trigger('timesheet/timesheet-edit-timesheet-post');
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
        
        $timesheet = ORM::for_table('crm_timesheet')->find_one($record_id);

        if($timesheet){
            $timesheet_allocation = $timesheet->invoice_alocation_id;
            // Prevent updates if the checkout date is today and no allocation exists
            if ($timesheet->date === $today && (!$timesheet_allocation || $timesheet_allocation == '')) {
                echo 'Timesheet for today cannot be modified!';
                break;
            }
    
            // If the salary type is 'per_hour' and has an invoice allocation ID, update qty and amount instead of checkin/checkout
            if ($type == 'per_hour' && $timesheet_allocation != '' && $timesheet_allocation != null) {
                // When the timesheet has an allocation, just update qty and amount
                $timesheet->checkin = null;
                $timesheet->checkout = null;
                $timesheet->qty = $qty;
                $timesheet->amount = $amount;
            } else {
                // If 'per_hour' type and no invoice allocation, calculate based on checkin/checkout times
                $timesheet->checkin = date('Y-m-d H:i:s', strtotime($checkIn));
                $timesheet->checkout = date('Y-m-d H:i:s', strtotime($checkOut));
    
                // Calculate hours worked
                $checkInTimestamp = strtotime($timesheet->checkin);
                $checkOutTimestamp = strtotime($timesheet->checkout);
                $seconds_diff = $checkOutTimestamp - $checkInTimestamp;
                $hours = $seconds_diff / (60 * 60); // Convert seconds to hours
                $timesheet->qty = $hours;
            }
            $timesheet->earn_amount = $timesheet->qty * $timesheet->amount;
            // Update other fields
            $timesheet->remarks = $remarks;
            $timesheet->updated_at = $updatedAt;
    
            // Save the updated timesheet
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
        Event::trigger('timesheet/timesheet-timesheet-popup-form');
        
        $id = $routes['2'];
        $employee = ORM::for_table('crm_accounts')->find_one($id);
        $employee_timesheet = ORM::for_table('crm_timesheet')->where('date', date('Y-m-d'))->where('employee_id', $id)->find_one();
        $ui->assign('employee',$employee);
        $ui->assign('timesheet',$employee_timesheet);
        $ui->assign('_theme',$_theme);
        $ui->display('timesheet/timesheet-popup-form.tpl');
    break;   
    
    case 'timesheet-entry-post':
        Event::trigger('timesheet/timesheet-timesheet-entry-post');
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
  
        if($timesheet){
            //get hours between checkin & out
            $checkIn  = strtotime($timesheet->checkin);
            $checkOut = strtotime('Y-m-d H:i:s');
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
        
        
default:
        echo 'action not defined';


}