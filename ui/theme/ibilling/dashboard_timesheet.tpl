{include file="sections/header.tpl"}

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body" id="timesheet-from-body300">
                    <div class="row">
                         <form class="form-horizontal" method="post" id="edit-sale-form300">
                            <input type="hidden" name="type" value="{$salery_type}">
                            {{!--<input type="hidden" value="{$timesheet->id}" name="timesheet_id">--}}
                            <input type="hidden" name="employee_id" value="{$employee.id}">
                              <div class="row">
                                    <div class="col-md-2">
                                        <label id="employee_name_label">Employee Name</label>
                                        <select name="employee_name2" id="employee_name2" class="form-control">
                                            {foreach $hourly_employee_name as $employee}
                                                <option value="{$employee.account}">{$employee.account}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="">
                                            <label class="" for="checkin">Check In</label>
                                            <input min="" max="" type="datetime-local" name="checkin-holiday" class="form-control" value="{$timesheet->checkin}">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="">
                                            <label class="" for="checkout">Check Out</label>
                                            <input min="" max="" type="datetime-local" name="checkout-holiday" class="form-control" value="{$timesheet->checkout}">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <label style="visibility:hidden">---</label>
                                        <button type="submit" class="btn btn-primary btn-block timesheet-entry-post" ><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </form> 
                    </div>    
			</div>
		</div>
	</div>
</div>



<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body" id="timesheet-from-body">
                    <div class="row">
                         <input type="hidden" id="total_earn_amount" name="total_earn_amount" value="{$earnAmountSum}">
                         
                        <input type="hidden" name="employee_id" value="{$employee->id}">
                        <div class="col-md-3">
                            <label id="employee_name_label">Employee Name</label>
                            <input type="text" name="employee_name" id="employee_name" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label id="salary_type_label">Salary Type</label>
                            <select name="salery_type" id="salery_type" class="form-control">
                                <option value="">Select Salary Type</option>
                                <option value="per_piece">Per Piece</option>
                                <option value="per_hour">Per Hour</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label id="from_label">From Date</label>
                            <input type="date" name="fromdate" value="{date('Y-m-d')}" class="form-control">
                        </div> 
                        <div class="col-md-5">
                            <label id="to_label">To Date</label>
                            <input type="date" name="todate" value="{date('Y-m-d')}" class="form-control">
                        </div>  
                                                                     
                        <div class="col-md-1">
                            <label style="visibility:hidden">---</label>
                            <button class="btn btn-primary btn-block" onclick="load_timesheet_data();"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>			
                        <div class="col-md-1">
                            <label style="visibility:hidden">---</label>
                            <button class="btn btn-danger btn-block" onclick="reset_form();"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                        </div>                        
                    </div>    
			</div>
		</div>
	</div>
</div>


<div class="row" id="application_ajaxrender">

	<div class="col-md-12">

		<div class="panel panel-default">

			<div class="panel-body">
               <div style="overflow-x: auto;">
                <table class="table table-bordered table-striped dt-responsive nowrap" width="100%" id="timesheet-list-table">

                        <thead>
                            <tr>
                                <th>Sr.</th>
                                <th>Employee Name</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hour/Piece</th>
                                <th>Salery Amount</th>
                                <th>Earn Amount</th>
                                <th>Date</th>
                                <th class="text-right" data-sort-ignore="true">Manage</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="6"></td>
                                <td id="sum-earn-amt" colspan="1"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
			</div>

		</div>

	</div>

</div>

<link rel="stylesheet" type="text/css" href="{$_theme}/css/modal.css"/>
<script src="{$_theme}/js/jquery-1.10.2.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<script>
var baseUrl = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
let URL = baseUrl+'/?ng=';
console.log("Loading timesheet data...");


    
        $(document).on('click', '.timesheet-entry-post', function(e) {
        e.preventDefault();
        var cid = $(this).data('id');
        var formData = $(this).closest("#edit-sale-form300").serialize();
        var formData2 = $("#edit-sale-form300").serialize();
        console.log(formData);
        console.log(formData2);
        $.post(URL + 'dashboard/timesheet-list-ajax-timesheet-holiday', $("#edit-sale-form300").serialize())
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
    






    function load_timesheet_data(){
    $("#timesheet-list-table").dataTable().fnDestroy();
    $('#timesheet-list-table').DataTable({
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'csvHtml5',
                filename: 'timesheet-report',
                text: 'Export',
                className: 'btn-sm btn-secondary btn-data-export',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
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
            "url": URL + 'dashboard/timesheet-list-ajax-timesheet',
            "type": "POST",
            "data": function ( search_param ) {
                search_param.employee_name = $('input[name="employee_name"]').val();
                search_param.salery_type = $('select[name="salery_type"]').val();
                search_param.employee_id = $('input[name="employee_id"]').val();
                search_param.fromdate = $('input[name="fromdate"]').val();
                search_param.todate = $('input[name="todate"]').val();
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
            { "data": "employee_name" },
            { "data": "checkin" },
            { "data": "checkout" },
            { "data": "qty" },
            { "data": "amount" },
            { "data": "earn_amount" },
            { "data": "date" },
            { "data": "action" }
        ],
        "columnDefs": [
                { orderable: false, targets: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
                {
                    targets: [5], // Column index where earn_amount is located
                    createdCell: function (td, cellData, rowData, row, col) {
                        // Extract earnAmountSum from the response
                        var earnAmountSum = rowData.earnAmountSum;
    
                        // Update the value of the hidden input field
                        $('#total_earn_amount').val(earnAmountSum);
        
                        // Update the content of the div with the earnAmountSum, making it bold
                        if (earnAmountSum && earnAmountSum !== null) {
                            $('#sum-earn-amt').html('Total :' + '<strong>' + earnAmountSum + '</strong>');
                        } else {
                            // Hide or reset the content of #sum-earn-amt if no valid earnAmountSum is found
                            // Reset to 0 if no data
                            $('#total_earn_amount').val(0);
                            $('#sum-earn-amt').html('Total :<strong>0</strong>');
                        }
                        
                        // Update the content of the div with the earnAmountSum, making it bold
                        //$('#sum-earn-amt').html('Total :' + '<strong>' + earnAmountSum + '</strong>');
    
                    }
                }
            ]
      
    });
}

    $(document).on('click', '#timesheet-entry123', function() {
        
        var cid = $(this).data('id');
        console.log(cid);
        var Url = URL + 'dashboard/timesheet-timesheet-popup-form/' + cid;
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
        $.post(URL + 'dashboard/timesheet-timesheet-entry-post', formData)
            .done(function(data) {
                if ($.isNumeric(data)) {
                   
                    $('#ajax-modal').unblock();
                    setTimeout(function() { $('.close').click(); location.reload(); }, 1000);
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
		$modal.load(URL + 'dashboard/timesheet-edit-timesheet-modal/' + id, '', function() {
			$modal.modal();
		});
    } 
    
    $("body").on('click', '#edit_timesheet', function (e) {
        e.preventDefault();
        $('#ajax-modal').block({ message:block_msg });
        var _url = $("#_url").val();
        $.post(URL + 'dashboard/timesheet-edit-timesheet-post/', $( "#edit-sale-form" ).serialize())
            .done(function (data) {
                if ($.isNumeric(data)) {
                    $("#emsgbody-success").html('Timesheet Updated Successfully');
                    $("#emsg-success").show("slow");
                    $("#emsg").hide();
                    $('#ajax-modal').unblock();
                    setTimeout(function() { $('.close').click(); location.reload(); }, 2000);
                }
                else {
                    $('#ajax-modal').unblock();
                    $("#emsgbody").html(data);
                    $("#emsg").show("slow");
                }
            });
    });    
       

function reset_form()
{
    $("#timesheet-from-body #salery_type").val("");
    $("#timesheet-from-body input:not([name='employee_id'])").val("");
    load_timesheet_data();
} 
  
    load_timesheet_data();


</script>
{include file="sections/footer.tpl"}