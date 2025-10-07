{include file="sections/header.tpl"}

<style>
.width_200
{
    width:160px;
    margin-top:5px;
}
.dataTables_wrapper .dataTables_length select {
    padding: 4px 20px 4px 10px ! Important;
}
button.dt-button.buttons-csv.buttons-html5.btn-sm.btn-secondary.btn-data-export {
    border: 0;
    background: #46c37b;
    color: #fff;
}
div#timesheet-list-table_length label {
    margin-right: 15px;
        margin-left: 2px;
}
table#timesheet-list-table {
    margin-top: 10px;
}
</style>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default border-radius-16px">
            <div class="panel-body border-radius-16px" id="timesheet-from-body300">
                <h2>Bulk Attendance</h2>
                <!--<h2>Employee Holiday days Form</h2>-->
                <div id="nonInsertedDataSection"></div>
                <form class="form-horizontal" method="post" id="edit-sale-form300">
                    <p> <b>Select Date :</b> <input type="date" class="selectdatefortime form-control width_200" id="datepicker"></p>
                    <div id="dynamic-inputs-container"></div>  
                    <div class="row">
                        <div class="col-md-12">
                            <!--<label style="visibility:hidden">---</label>-->
                            <button type="submit" class="width_200 btn btn-primary btn-block timesheet-entry-post300" style="background: #2196f3; float: right; margin-top: 50px;">Submit<i class="fa fa-send-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    // Bind click event to submit button
    $('#edit-sale-form300 .timesheet-entry-post300').on('click', function(e) {
        e.preventDefault(); // Prevent form submission

        // Flag to track if any field is empty
        var isEmptyField = false;

        $('.row-form').each(function(i) {
            var int = i + 1;
            console.log('Checking details of row ' + int);
            // Get remark textarea and employee_id3 select
            var remark = $(this).find('.remarks');
            var employeeIdSelect = $(this).find('.employee_id3');
        
            // Check if both remark and employeeIdSelect are empty
            if (remark.val().trim() === '' && employeeIdSelect.val() === null) {
                alert('Please fill in remarks and select an employee for row ' + int);
                // Set flag to true
                isEmptyField = true;
            } else if (remark.val().trim() === '') {
                alert('Please fill in remarks for row ' + int);
                // Set flag to true
                isEmptyField = true;
            } else if (employeeIdSelect.val() === null) {
                alert('Please select an employee for row ' + int);
                // Set flag to true
                isEmptyField = true;
            }
        });

        // If any field is empty, prevent form submission
        if (isEmptyField) {
            return false;
        }

        // If all fields are filled, submit the form
        // $('#edit-sale-form300').submit();
        
    });
    
    // Function to check if the submit button should be hidden or shown
    function toggleSubmitButton() {
        var dateValue = $("#datepicker").val();
        var inputRowsCount = $("#dynamic-inputs-container .row-form").length;

        // If there's no date set or no dynamic input rows, hide the submit button
        if (dateValue || inputRowsCount > 0) {
            $(".timesheet-entry-post300").show();  // Show submit button
        } else {
            $(".timesheet-entry-post300").hide();  // Hide submit button
        }
    }

    // Call toggleSubmitButton initially to hide the submit button if no date is set
    toggleSubmitButton();
    
    // Initialize Select2 for existing select elements
    $(".select2").select2();
    var baseUrl = "{$APP_URL}";
    let APP_URL = baseUrl+'/?ng=';

    var employeeIdIndex = 0;
    // global helper: fetch employees for branch
    function fetchBranchEmployees(branchId, cb) {
        $.post(APP_URL + "timesheet/timesheet-ajax-employees", { branch_id: branchId }, function(resp) {
            cb(resp || []);
        }, 'json');
    }

    function addInputRow(formattedDate2, dayName, defaultCheckIn, defaultCheckOut, dateOnly) {
        var message = "On " + formattedDate2 + ", all selected employees will be marked present.";

        var inputRow = `
            <div class="row row-form" data-row-index="` + employeeIdIndex + `">
                <div class="toaster" style="position: relative; margin: 10px 0;
                    background-color: #51A351; color: #fff; padding: 10px;">` + message + `</div>

                <div class="col-md-3">
                    <label>Branch</label>
                    <select name="branch_id" id="branch_id" class="branch-select form-control" required>
                        {if $user->roleid eq 0}
                            <option value="">Select Branch</option>
                            {foreach $branches as $branch}
                                <option value="{$branch.id}" {if $branch.id eq $user->branch_id}selected{/if}>{$branch.alias|default:$branch.account}</option>
                            {/foreach}
                        {else}
                            {foreach $branches as $branch}
                                {if $branch.id eq $user->branch_id}
                                    <option value="{$branch.id}" {if $branch.id eq $user->branch_id}selected{/if}>{$branch.alias|default:$branch.account}</option>
                                {/if}
                            {/foreach}
                        {/if}
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Date</label>
                    <input type="text" class="form-control" readonly value="` + dateOnly + `">
                    <input type="hidden" name="date[]" value="` + dateOnly + `">
                </div>

                <div class="col-md-2">
                    <label for="checkin">Check In</label>
                    <input class="checkin12 form-control" required type="time" name="checkin-holiday[]" value="` + defaultCheckIn + `">
                </div>

                <div class="col-md-2">
                    <label for="checkout">Check Out</label>
                    <input class="checkout12 form-control" required type="time" name="checkout-holiday[]" value="` + defaultCheckOut + `">
                </div>

                <div class="col-md-3">
                    <label>Employees</label>
                    <div class="row">
                        <div class="col-md-8">
                            <select required multiple name="employee_id_` + employeeIdIndex + `[]" class="employee_id3 select2 form-control"></select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary select-all-btn2">Select All</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-9" style="margin-top:10px;">
                    <label for="remarks">Remarks</label>
                    <textarea required class="form-control remarks" placeholder="Remarks" name="remarks[]">` + dayName + `</textarea>
                </div>

                <div class="col-md-3" style="margin-top:32px;">
                    <button type="button" class="btn btn-danger remove-input-row">-</button>
                </div>
            </div>
        `;

        $("#dynamic-inputs-container").append(inputRow);

        // Initialize Select2
        var $lastSelect = $("#dynamic-inputs-container").find(".employee_id3").last();
        $lastSelect.select2();
        
        // Fetch and populate employees for the selected branch
        var branchId = $lastSelect.closest('.row-form').find('.branch-select').val();

        fetchBranchEmployees(branchId, function(emps) {
            var options = '';
            $.each(emps, function(i, emp) {
                options += '<option value="' + emp.id + '">' + emp.account + '</option>';
            });
            $lastSelect.html(options);
            $lastSelect.find('option').prop('selected', true);
            $lastSelect.trigger('change');
        });

        employeeIdIndex++;
    }

    // EVENT: branch change inside row → load employees
    $("#dynamic-inputs-container").on('change', '.branch-select', function() {
        var $row = $(this).closest('.row-form');
        var branchId = $(this).val();
        var $empSelect = $row.find('.employee_id3');

        if (!branchId) {
            $empSelect.html('<option value="">No employees</option>').trigger('change');
            return;
        }

        fetchBranchEmployees(branchId, function(emps) {
            var options = '';
            $.each(emps, function(i, emp) {
                options += '<option value="' + emp.id + '">' + emp.account + '</option>';
            });
            $empSelect.html(options);
            // preselect all employees
            $empSelect.find('option').prop('selected', true);
            $empSelect.trigger('change');
        });
    });

    // EVENT: per-row select all
    $("#dynamic-inputs-container").on('click', '.select-all-btn2', function(e) {
        e.preventDefault();
        var $row = $(this).closest('.row-form');
        var $sel = $row.find('.employee_id3');
        $sel.find('option').prop('selected', true);
        $sel.trigger('change');
    });

    $("#dynamic-inputs-container").on("click", ".remove-input-row", function (e) {
        e.preventDefault();
        $(this).closest(".row-form").remove();
        toggleSubmitButton();
    });

    $("#datepicker").on("change", function() {
        toggleSubmitButton();
        firstSelectedDate = $(this).val();

        var inputDate = new Date($(this).val());
        var dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var dayName = dayNames[inputDate.getDay()];
        
        var selectedDate = new Date(inputDate);
        if (!isNaN(selectedDate.getTime())) {
            selectedDate.setHours(9, 0, 0);
            selectedDate.setMinutes(selectedDate.getMinutes() + 330);

            var endDate = new Date(selectedDate);
            endDate.setHours(18, 0, 0);
            endDate.setMinutes(endDate.getMinutes() + 330);

            var startDateTimeLocal = selectedDate.toISOString().slice(0, 16);
            var endDateTimeLocal = endDate.toISOString().slice(0, 16);

            var date = new Date(startDateTimeLocal);
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            var formattedDate2 = new Intl.DateTimeFormat('en-US', options).format(date);

            var defaultCheckIn = "09:00";
            var defaultCheckOut = "18:00";

            addInputRow(formattedDate2, dayName, defaultCheckIn, defaultCheckOut, firstSelectedDate);
            // addInputRow(formattedDate2, dayName, startDateTimeLocal, endDateTimeLocal);
            
            // Select all options for the newly added select element
            var newlyAddedSelect = $(".row-form").find(".employee_id3").last();
            newlyAddedSelect.find('option').prop('selected', true);
            newlyAddedSelect.trigger('change');
            
            $(this).val('');
        } else {
            console.error("Invalid Date");
        }
    });
});

</script>
<div class="row" id="application_ajaxrender">
	<div class="col-md-12">
		<div class="panel panel-default border-radius-16px">
		    <div class="panel-body border-radius-16px" id="timesheet-from-body">
                    <div class="row">
                        <input type="hidden" id="total_earn_amount" name="total_earn_amount" value="{$earnAmountSum}">
                        <input type="hidden" id="employee_id2" name="employee_id2" value="{$employee->id}">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="branch_label">Branch</label>
                                <select id="branch_select" name="branch_id" class="form-control">
                                    {if $user->roleid eq 0}
                                        <option value="">All</option>
                                        {foreach $branches as $branch}
                                            <option value="{$branch.id}" {if $branch.id eq $user->branch_id}selected{/if}>
                                                {$branch.alias|default:$branch.account}
                                            </option>
                                        {/foreach}
                                    {else}
                                        <option value="{$user->branch_id}" selected>
                                            {foreach $branches as $branch}
                                                {if $branch.id eq $user->branch_id}
                                                    {$branch.alias|default:$branch.account}
                                                {/if}
                                            {/foreach}
                                        </option>
                                    {/if}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label id="employee_name_label">Employee Name</label>
                            <input type="text" name="display_employee_name" id="display_employee_name" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label id="salery_type_label">Salary Type</label>
                            <select name="salery_type" id="salery_type" class="form-control">
                                <option value="">Select Salary Type</option>
                                <option value="per_piece">Per Piece</option>
                                <option value="per_hour">Per Hour</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label id="from_label">From Date</label>
                            <input type="date" name="fromdate" value="{$smarty.now|date_format:'%Y-%m-%d'}" class="form-control">
                        </div> 
                        <div class="col-md-2">
                            <label id="to_label">To Date</label>
                            <input type="date" name="todate" value="{$smarty.now|date_format:'%Y-%m-%d'}" class="form-control">
                        </div>  
                        <div class="col-md-2" style="align-self: flex-end;    display: flex; justify-content: flex-end;">
                            <div class="col-md-6">
                                <label style="visibility:hidden">---</label>
                                <button class="btn btn-primary btn-block" onclick="submit();"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>			
                            <div class="col-md-6">
                                <label style="visibility:hidden">---</label>
                                <button class="btn btn-danger btn-block" onclick="reset_form();"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                            </div>                        
                        </div>                        
                    </div>    
			</div>
			<div class="panel-body">
               <div style="overflow-x: auto;">
                <table class="table table-bordered table-striped dt-responsive nowrap" width="100%" id="timesheet-list-table">
                        <thead>
                            <tr>
                                <th>Sr.</th>
                                <th>Branch</th>
                                <th>Employee Name</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hour/Piece</th>
                                <th>Salery Amount</th>
                                <th>Earn Amount</th>
                                <th>Invoice</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th class="text-right" data-sort-ignore="true">Manage</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="7"></td>
                                <td id="sum-earn-amt" colspan="1"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
			</div>
		</div>
	</div>
</div>

<script>
var baseUrl = "{$APP_URL}";
let URL = baseUrl+'/?ng=';
// console.log("Loading timesheet data...");
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2();
 });

$(document).on('click', '.timesheet-entry-post300', function(e) {
    e.preventDefault();
    
    if ($("#edit-sale-form300").serialize() === '') {
        alert("Please Select Sate And Fill In The Required Fields.");
        return;
    }
    
    if (confirm("Are you sure you want to submit the form?")) {
        var formData = $("#edit-sale-form300").serialize();
        console.log("Serialized Form Data:", formData);
        $.post(URL + 'timesheet/holiday-timesheet-ajax', formData)
            .done(function(data) {
                if ($.isNumeric(data)) {
                    console.log("Success: Timesheet ID:", data);
                    $("#toaster").fadeIn().delay(3000).fadeOut();
                    setTimeout(function() { 
                        $("#edit-sale-form300")[0].reset();
                        location.reload(); 
                    }, 2000);
                } else {
                    console.log("Error Response:", data);
                    var response = JSON.parse(data);
                    var nonInsertedData = response.non_inserted_data;
                    var InsertedData = response.inserted_data;
                    
                    // Begin constructing the modal content
                    var tableHTML = '';

                    // // Inserted Data Table
                    // if (InsertedData.length > 0) {
                    //     tableHTML += `<div class="toaster" id="toaster" style="position: relative; margin: 30px 0 10px; background-color: green; color: #fff; padding: 10px;">
                    //     <p>This data inserted Successfully in the database:</p></div>
                    //     <table id="InsertedDataTable" class="display">
                    //         <thead>
                    //             <tr>
                    //                 <th>Employee Name</th>
                    //                 <th>Check-in</th>
                    //                 <th>Check-out</th>
                    //                 <th>Remarks</th>
                    //             </tr>
                    //         </thead>
                    //         <tbody>`;
                    //     for (var i = 0; i < InsertedData.length; i++) {
                    //         var row = InsertedData[i];
                    //         tableHTML += '<tr><td>' + row.employee_name + '</td><td>' + row.checkin + '</td><td>' + row.checkout + '</td><td>' + row.remarks + '</td></tr>';
                    //     }
                    //     tableHTML += '</tbody></table>';
                    // }
                    
                    // Inserted Data Table
                    if (InsertedData.length > 0) {
                        tableHTML += `<div class="toaster" id="toaster" style="position: relative; margin: 30px 0 10px; background-color: green; color: #fff; padding: 10px;">
                        <p>This data inserted Successfully in the database:</p></div>
                        <table id="InsertedDataTable" class="display">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>`;
                        for (var i = 0; i < InsertedData.length; i++) {
                            var row = InsertedData[i];
                            
                            // Format check-in and check-out dates using moment.js
                            var formattedCheckin = moment(row.checkin).format('DD-MM-YYYY HH:mm');
                            var formattedCheckout = moment(row.checkout).format('DD-MM-YYYY HH:mm');
                            
                            tableHTML += '<tr><td>' + row.employee_name + '</td><td>' + formattedCheckin + '</td><td>' + formattedCheckout + '</td><td>' + row.remarks + '</td></tr>';
                        }
                        tableHTML += '</tbody></table>';
                    }
                    
                    
                    // Non-inserted Data Table
                    if (nonInsertedData.length > 0) {
                        tableHTML += `<div class="toaster" id="toaster" style="position: relative; margin: 30px 0 10px; background-color: #cf4d4d; color: #fff; padding: 10px;">
                        <p>This data is not inserted because it already exists in the database:</p></div>
                        <table id="nonInsertedDataTable" class="display">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>`;
                        for (var i = 0; i < nonInsertedData.length; i++) {
                            var row = nonInsertedData[i];
                            
                            // Format check-in and check-out dates using moment.js
                            var formattedCheckin = moment(row.checkin).format('DD-MM-YYYY HH:mm');
                            var formattedCheckout = moment(row.checkout).format('DD-MM-YYYY HH:mm');
                            
                            tableHTML += '<tr><td>' + row.employee_name + '</td><td>' + formattedCheckin + '</td><td>' + formattedCheckout + '</td><td>' + row.remarks + '</td></tr>';
                        }
                        tableHTML += '</tbody></table>';
                    }

                    // // Non-inserted Data Table
                    // if (nonInsertedData.length > 0) {
                    //     tableHTML += `<div class="toaster" id="toaster" style="position: relative; margin: 30px 0 10px; background-color: #cf4d4d; color: #fff; padding: 10px;">
                    //     <p>This data is not inserted because it already exists in the database:</p></div>
                    //     <table id="nonInsertedDataTable" class="display">
                    //         <thead>
                    //             <tr>
                    //                 <th>Employee Name</th>
                    //                 <th>Check-in</th>
                    //                 <th>Check-out</th>
                    //                 <th>Remarks</th>
                    //             </tr>
                    //         </thead>
                    //         <tbody>`;
                    //     for (var i = 0; i < nonInsertedData.length; i++) {
                    //         var row = nonInsertedData[i];
                    //         tableHTML += '<tr><td>' + row.employee_name + '</td><td>' + row.checkin + '</td><td>' + row.checkout + '</td><td>' + row.remarks + '</td></tr>';
                    //     }
                    //     tableHTML += '</tbody></table>';
                    // }

                    // Combine everything into the modal content
                    $('#ajax-modal').html('<div class="modal-header">   <button type="button" id="closeModalButton" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3>Bulk Attandance</h3></div><div id="popupModal" title="Timesheet Data" style="padding: 20px;">' + tableHTML + '</div>');
                    
                    // Initialize DataTables
                    if (InsertedData.length > 0) {
                        $('#InsertedDataTable').DataTable();
                    }
                    if (nonInsertedData.length > 0) {
                        $('#nonInsertedDataTable').DataTable();
                    }

                    // Show the modal
                    $('#ajax-modal').modal('show');
                    console.log("Inserted and non-inserted data displayed.");
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Request Failed:");
                console.error("Status:", textStatus);
                console.error("Error Thrown:", errorThrown);
                console.error("Response Text:", jqXHR.responseText);
                console.error("Status Code:", jqXHR.status);
                console.error("Status Text:", jqXHR.statusText);
            });
    }
});

    // Attach event listener to the close button
    $(document).on('click', '#closeModalButton', function() {
        location.reload();
    });





    function load_timesheet_data(){
        //$("#timesheet-list-table").dataTable().fnDestroy();
        $('#timesheet-list-table').DataTable({
            dom: 'lBfrtip',
            buttons: [
                //{
                //    extend: 'csvHtml5',
                //    filename: 'timesheet-report',
                //    text: 'Export',
                //    className: 'btn-sm btn-secondary btn-data-export',
                //    exportOptions: {
                //        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                //    }
                //}
            ],
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            initComplete: function () {
                $('.dataTables_filter').hide();
            },
            pageLength: 10,
            responsive: true,
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            "ajax": {
                "url": URL + 'timesheet/timesheet-list-ajax-timesheet',
                "type": "POST",
                "data": function ( search_param ) {
                    search_param.display_employee_name = $('input[name="display_employee_name"]').val();
                    search_param.salery_type = $('select[name="salery_type"]').val();
                    search_param.employee_id = $('input[name="employee_id"]').val();
                    search_param.fromdate = $('input[name="fromdate"]').val();
                    search_param.todate = $('input[name="todate"]').val();
                    search_param.branch_id = $('select#branch_select').val() || '';
                }
            },
            drawCallback: function(settings) {
                var api = this.api();
                var json = api.ajax.json(); // Access the AJAX response JSON
                
                if (json && json.earnAmountSum !== undefined) {
                    var earnAmountSum = json.earnAmountSum;
                    
                    // Update the total earn amount
                    $('#total_earn_amount').val(earnAmountSum || 0);
                    
                    // Update the display div
                    $('#sum-earn-amt').html('Total :' + '<strong>' + (earnAmountSum || 0) + '</strong>');
                } else {
                    // Reset to 0 if no data
                    $('#total_earn_amount').val(0);
                    $('#sum-earn-amt').html('Total :<strong>0</strong>');
                }
            },
            "columns": [
                { "data": "sr" },
                { "data": "branch" },  
                { "data": "display_employee_name" },
                { "data": "checkin" },
                { "data": "checkout" },
                { "data": "qty" },
                { "data": "amount" },
                { "data": "earn_amount" },
                { "data": "invoicenum" },
                { "data": "remarks" },
                { "data": "date" },
                { "data": "action" }
            ],
            "columnDefs": [
                    { orderable: false, targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
                    {
                        targets: [5], // Column index where earn_amount is located
                        createdCell: function (td, cellData, rowData, row, col) {
                            // Extract earnAmountSum from the response
                            var earnAmountSum = rowData.earnAmountSum;
        
                            // Update the value of the hidden input field
                            // $('#total_earn_amount').val(earnAmountSum || 0); // Set 0 if no value is found
        
                            // Update the content of the div with the earnAmountSum, making it bold
                            if (earnAmountSum && earnAmountSum !== null && earnAmountSum != 0) {
                                $('#sum-earn-amt').html('Total :' + '<strong>' + earnAmountSum + '</strong>');
                            } else {
                               // Reset to 0 if no data
                                $('#total_earn_amount').val(0);
                                $('#sum-earn-amt').html('Total :<strong>0</strong>');
                            }
                            
                            // Update the content of the div with the earnAmountSum, making it bold
                            // $('#sum-earn-amt').html('Total :' + '<strong>' + earnAmountSum + '</strong>');
        
                        }
                    }
                ]
          
        });
    }

    $(document).on('click', '#timesheet-entry123', function() {
        
        var cid = $(this).data('id');
        console.log(cid);
        var Url = URL + 'timesheet/timesheet-timesheet-popup-form/' + cid;
        var $modal = $('#ajax-modal');
        $modal.load(Url, function() {
            $modal.modal();
        });
    });

    $(document).on('click', '.timesheet-entry-post', function(e) {
        e.preventDefault();
        var cid = $(this).data('id');
        var formData = $(this).closest('#timesheet-entry').serialize();
        console.log(formData);
        $.post(URL + 'timesheet/timesheet-timesheet-entry-post', formData)
            .done(function(data) {
                if ($.isNumeric(data)) {
                   
                    $('#ajax-modal').unblock();
                    // setTimeout(function() { $('.close').click(); location.reload(); }, 1000);
                } else {
                    $('#ajax-modal').unblock();
                    $("#emsgbody").html(data);
                    $("#emsg").show("slow");
                }
            });
    });

    function edit_timesheet_modal(id) {
		var _url = $("#_url").val();
		var $modal = $('#ajax-modal');
		$('body').modalmanager('loading');
		$modal.load(URL + 'timesheet/timesheet-edit-timesheet-modal/' + id, '', function() {
			$modal.modal();
		});
    } 
    
    $("body").on('click', '#edit_timesheet', function (e) {
        e.preventDefault();
        $('#ajax-modal').block({ message:block_msg });
        var _url = $("#_url").val();
        $.post(URL + 'timesheet/timesheet-edit-timesheet-post/', $( "#edit-sale-form" ).serialize())
            .done(function (data) {
                if ($.isNumeric(data)) {
                    $("#emsgbody-success").html('Timesheet Updated Successfully');
                    $("#emsg-success").show("slow");
                    $("#emsg").hide();
                    $('#ajax-modal').unblock();
                    // setTimeout(function() { $('.close').click(); location.reload(); }, 2000);
                }
                else {
                    $('#ajax-modal').unblock();
                    $("#emsgbody").html(data);
                    $("#emsg").show("slow");
                }
            });
    });    

    function reset_form(){
        $("#timesheet-from-body #salery_type").val("");
        $("#timesheet-from-body input").val("");
        
        if ($.fn.DataTable.isDataTable('#timesheet-list-table')) {
            $('#timesheet-list-table').DataTable().destroy();
        }
        
        load_timesheet_data();
    } 
    
    function submit() {
        if ($.fn.DataTable.isDataTable('#timesheet-list-table')) {
            $('#timesheet-list-table').DataTable().destroy();
        }
        load_timesheet_data();
    };

    $("#timesheet-from-body input[id='employee_id2']").val("");
  
    $(document).ready(function() {
        load_timesheet_data();
        // setTimeout(load_timesheet_data, 500); // Slight delay to ensure DOM readiness
    });
    // load_timesheet_data();
    // setTimeout(function(){ load_timesheet_data(); } , 300)
</script>

{include file="sections/footer.tpl"}