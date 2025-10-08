<?php 
// Retrieve the action from the routes array
$action = isset($routes['1']) ? $routes['1'] : null;

// Trigger the 'invoices' event if necessary
Event::trigger('invoices');

// Switch based on the action
switch ($action) {
    case 'complete_order_notification':
        // Get today's date in the format used in your reminder_date field
        $today_date = date('Y-m-d');
        
        // Query invoices with delivery_status 'completed' and reminder_date as today
        $completed_invoices = ORM::for_table('sys_invoices')
            ->join('crm_accounts', array('sys_invoices.userid', '=', 'crm_accounts.id')) // Join with crm_accounts on userid
            ->where('sys_invoices.delivery_status', 'completed')
            ->where('sys_invoices.reminder_date', $today_date)
            ->select('sys_invoices.*')  // Select all fields from sys_invoices
            ->select('crm_accounts.phone', 'phone')  // Select the phone number from crm_accounts
            ->find_many();

        // $completed_invoices = ORM::for_table('sys_invoices')
        //     ->where('delivery_status', 'completed')
        //     ->where('reminder_date', $today_date)
        //     ->find_many();
            
        if (count($completed_invoices) > 0) {
            foreach ($completed_invoices as $invoice) {
                $name = ucwords($invoice->account);
                $invoicenumber = $invoice->invoicenum;
                $paymentstatus = ucwords($invoice->status); // Use the status directly from $invoice
                $orderstatus = ucwords($invoice->delivery_status);
                $ordertrack_url = U . "client/iview/{$invoice->id}/token_{$invoice->vtoken}";
                $phone = $invoice->phone; // Assuming phone number is stored in the invoice record
    
                // Send Wati notification
                wati_notifiation($name, $invoicenumber, $paymentstatus, $orderstatus, $ordertrack_url, $phone);
    
                // Add 7 days to the current reminder_date
                $new_reminder_date = date('Y-m-d', strtotime('+7 days', strtotime($invoice->reminder_date)));
                $invoice->reminder_date = $new_reminder_date;
                $invoice->save();
                
                echo 'Notification sent for invoice '.$invoicenumber.' and reminder_date updated.';
                // Optionally log success message
                //_msglog('s', 'Notification sent for invoice '.$invoicenumber.' and reminder_date updated.');
            }
                error_log('Order Completed Reminder run with data: ' . $today_date);
            echo 'Notifications sent for completed orders with reminder date of today.';
            
        } else {
                error_log('Order Completed Reminder run with no data: ' . $today_date);
            echo 'No completed orders with reminder date of today.';
        }
        break;

    // Case to fetch data from the API check in/out
    case 'fetch_punch_data':
        $empcode = 'ALL';
        $fromDate = date('d/m/Y');
        $toDate = date('d/m/Y');
    
        // Initialize cURL
        $curl = curl_init();
    
        // Set up the API URL and credentials for Authorization
        $corporateId = 'ABAYADESIGNER';
        $username = 'ABAYADESIGNER';
        $password = 'Allah@786:true';
        $credentials = base64_encode("$corporateId:$username:$password");
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.etimeoffice.com/api/DownloadInOutPunchData?Empcode=$empcode&FromDate=$fromDate&ToDate=$toDate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $credentials,
            ),
        ));
    
        $response = curl_exec($curl);
    
        // Check for cURL errors
        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
            error_log('fetch punch API check in/out has run with error: '. curl_error($curl));
        } else {
            // Output the raw response for debugging
            echo "Raw API Response: " . $response . "\n";
            // error_log('fetch punch API check in/out has run with error: '. $response);
        
            // Decode the JSON response
            $data = json_decode($response, true);
    
            // Check if the data is valid
            if (isset($data['Error']) && $data['Error']) {
                echo "Error: " . $data['Msg'];
                return;
            }
    
            if (!isset($data['InOutPunchData']) || empty($data['InOutPunchData'])) {
                echo "No InOutPunchData found.";
                return;
            }
            if ($data['InOutPunchData']) {
                $qty = 0;
                $date = date('Y-m-d');
                $createdAt = date('Y-m-d H:i:s');
                $updatedAt = date('Y-m-d H:i:s');
            
                foreach ($data['InOutPunchData'] as $punch) {
                    $empCode = $punch['Empcode'];
                    $inTime = $punch['INTime'];
                    $outTime = $punch['OUTTime'];
            
                    // Skip entries with invalid INTime and OUTTime
                    if ($inTime === '--:--' && $outTime === '--:--') {
                        error_log("Skipping punch data for EmpCode: $empCode due to invalid INTime and OUTTime.");
                        continue;
                    }
            
                    // Find the corresponding account in crm_accounts
                    $account = ORM::for_table('crm_accounts')
                        ->where('emp_code', $empCode)
                        ->find_one();

                    if ($account) {
                        $type = $account->salery_type;
                        $amount = $account->salery_amt;
                        $employee_id = $account->id;
                        $branch_id = $account->branch_id;
                        
                        // Validate necessary fields
                        if (!empty($type) && $type === 'per_hour' && !empty($amount) && !empty($employee_id)) {
                            // Find timesheet entry for today's date for the employee
                            $timesheet = ORM::for_table('crm_timesheet')
                                ->where('employee_id', $employee_id)
                                ->where('date', $date)
                                ->where_null('invoice_alocation_id')
                                ->find_one();
                
                            if ($timesheet) {
                                // Update checkout time if OUTTime is valid
                                if (!empty($outTime) && $outTime !== '--:--') {
                                    $checkIn = strtotime($timesheet->checkin);
                                    $checkOut = strtotime($outTime);
                                    
                                    if ($checkOut > $checkIn) {
                                        
                                        $seconds_diff = $checkOut - $checkIn;
                                        $hours = $seconds_diff / (60 * 60); // Convert seconds to hours
                        
                                        $timesheet->checkout = date('Y-m-d H:i:s', $checkOut);
                                        $timesheet->qty = ($type == 'per_hour') ? $hours : $qty;
                                        $timesheet->earn_amount = $timesheet->qty * $amount;
                                        $timesheet->updated_at = $updatedAt;
                                        $timesheet->save();
                        
                                        _msglog('s', 'CheckOut successfully updated!');
                                        echo "Updated Timesheet ID: " . $timesheet->id() . "\n";
                                    } 
                                    else {
                                        error_log("Invalid OUTTime for EmpCode: $empCode. CheckOut is earlier than CheckIn.");
                                    }
                                } else {
                                    error_log("OUTTime missing or invalid for EmpCode: $empCode.");
                                }
                            } else {
                                // If no record exists, create a new check-in entry
                                if ($inTime !== '--:--') {
                                    $insert = ORM::for_table('crm_timesheet')->create();
                                    $insert->branch_id = $branch_id;
                                    $insert->employee_id = $employee_id;
                                    $insert->emp_code = $empCode;
                                    $insert->checkin = date('Y-m-d H:i:s', strtotime($inTime));
                                    $insert->amount = $amount;
                                    $insert->date = $date;
                                    $insert->qty = ($type == 'per_hour') ? 0 : $qty;
                                    $insert->earn_amount = $insert->qty * $amount;
                                    $insert->created_at = $createdAt;
                                    $insert->updated_at = $updatedAt;
                                    $insert->save();
                    
                                    _msglog('s', 'CheckIn successfully created!');
                                    echo "Created Timesheet ID: " . $insert->id() . "\n";
                                } else {
                                    error_log("CheckIn missing or invalid for EmpCode: $empCode.");
                                }
                            }
                        } else {
                            error_log("Invalid employee details for EmpCode: $empCode.");
                        }
                    } else {
                        error_log("No matching employee found for EmpCode: $empCode.");
                    }
                }
                
                error_log('fetch punch API check in/out processing completed.');
            } else {
                echo "No data found or invalid response.";
                // Print the entire decoded data for further inspection
                // print_r($data);
            }
        }
        curl_close($curl);
    break;
    
    // Add additional cases if needed

    default:
        echo 'Invalid action.';
        break;
}
?>