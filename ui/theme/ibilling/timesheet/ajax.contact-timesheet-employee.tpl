

{*{if $user->roleid == 0}
<a href="#" class="btn btn-info btn-xs salery_type_popup" data-id="{$cid}"><i class="fa fa-plus"></i> Salery Type </a>
{/if}*}
<a href="#" class="btn btn-info btn-xs salery_type_popup" data-id="{$cid}"><i class="fa fa-plus"></i> Salery Type </a>

{if !empty($employee->salery_type)}
    {if $employee->salery_type == 'per_hour'}
    <a href="#" class="btn btn-success btn-xs timesheet_checkin_popup" data-id="{$cid}"><i class="fa fa-plus"></i> Timesheet Entry </a>
    {/if}
{/if}

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body" id="timesheet-from-body">
                    <div class="row">
                         <input type="hidden" id="total_earn_amount" name="total_earn_amount" value="{$earnAmountSum}">
                         
                         
                        <input type="hidden" name="employee_id" value="{$employee->id}">
                        <div class="col-md-3">
                            <label id="from_label">From Date</label>
                            <input type="date" name="fromdate" value="{$smarty.now|date_format:'%Y-%m-%d'}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label id="to_label">To Date</label>
                            <input type="date" name="todate" value="{$smarty.now|date_format:'%Y-%m-%d'}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control">
                                <option value="">All</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Salary Type</label>
                            <select name="salary_type" class="form-control">
                                <option value="per_hour">Per Hour</option>
                                <option selected value="per_piece">Per Piece</option>
                            </select>
                        </div>
                                                                     
                        <div class="col-md-1">
                            <label style="visibility:hidden">---</label>
                            <button class="btn btn-primary btn-block" onclick="submit();"><i class="fa fa-search" aria-hidden="true"></i></button>
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
                                <th>Salary Type</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hours / Piece</th>
                                <!-- <th>{if $employee->salery_type == 'per_hour'} Hours {else} Piece {/if}</th> -->
                                <th>Salery Amount</th>
                                <th>Earn Amount</th>
                                <th>Invoice</th>
                                <th>Remarks</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Completed Date</th>
                                <th>Paid Date</th>

                                <th class="text-right" data-sort-ignore="true">Manage</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="6"></td>
                                <td id="sum-earn-amt" colspan="1"></td>
                                <td id="pay-now-btn" colspan="1"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
			</div>

		</div>

	</div>

</div>

<link rel="stylesheet" type="text/css" href="{$_theme}/css/modal.css"/>
<script type="text/javascript" src="{$_theme}/lib/modal.js"></script>


<script>
    function load_timesheet_data(){

		$('#timesheet-list-table').DataTable({
    		dom: 'lfrtip',
    		// dom: 'lBfrtip',
    		// buttons: [
    		//     {
            //         extend: 'csvHtml5',
            //         filename: 'timesheet-report', 
            //         text: 'Export',
            //         className: 'btn-sm btn-secondary btn-data-export',
            //         exportOptions: {
            //             columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
            //         }                    
            //     }
            // ],
    		"lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
            initComplete : function() {
                $('.dataTables_filter').hide();
            },
            "pageLength": 10,
            'responsive': true,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
             'url': $("#_url").val() + 'contacts/list-ajax-timesheet/',
            "data": function ( search_param ) {
                search_param.employee_id= $('input[name="employee_id"]').val();
                search_param.fromdate   = $('input[name="fromdate"]').val();
                search_param.todate     = $('input[name="todate"]').val();
                search_param.payment_status = $('select[name="payment_status"]').val();  // <-- Ensure it's being sent
                search_param.salary_type = $('select[name="salary_type"]').val();  // <-- Ensure it's being sent
            },
            "dataSrc": function (json) {
                // Hide total and pay now button if no records exist
                if (json.iTotalRecords === 0) {
                    $('#sum-earn-amt').hide();
                    $('#pay-now-btn').hide();
                } else {
                    $('#sum-earn-amt').show();
                    $('#pay-now-btn').show();
                }
                return json.aaData;
            }             
            },
            "order": [[ 0, "desc" ]],
            'columns': [
                { data: 'sr' },
                { data: 'employee_type' },
                { data: 'checkin' },
                { data: 'checkout' },
            	{ data: 'qty' },
            	{ data: 'amount' },
            	{ data: 'earn_amount' },
            	{ data: 'invoicenum' },
            	{ data: 'remarks' },
            	{ data: 'payment_status' },
            	{ data: 'date' },
            	{ data: 'completed_date' },
            	{ data: 'paid_date' },
            	{ data: 'action' },
            ], 
            "columnDefs": [
            { "orderable": false, "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ] },
            {
                "targets": [6], // Column index where earn_amount is located
                createdCell: function (td, cellData, rowData, row, col) {
                    
                    // Extract earnAmountSum from the response
                    var earnAmountSum = rowData.earnAmountSum;

                    // Update the value of the hidden input field
                    $('#total_earn_amount').val(earnAmountSum);

                    // Update the content of the div with the earnAmountSum, making it bold
                    $('#sum-earn-amt').html('Total :' + '<strong>' + earnAmountSum + '</strong>');
                    
                        

                }
            },
            {
                "targets": [7], // Footer button for payment
                createdCell: function (td, cellData, rowData, row, col) {
                   
                        $('#pay-now-btn').html(rowData.payNowButton);
                    
                    // $('#pay-now-action').on('click', function() {
                    //     window.location.href = $(this).data('url');
                    // });
                }
            }
        ]
		}); 
    } 

    function edit_timesheet_modal(id) {
		var _url = $("#_url").val();
		var $modal = $('#ajax-modal');
		$('body').modalmanager('loading');
		$modal.load(_url + 'contacts/edit-timesheet-modal/' + id, '', function() {
			$modal.modal();
		});
    } 
    
    $("body").on('click', '#edit_timesheet', function (e) {
        e.preventDefault();
        $('#ajax-modal').block({ message:block_msg });
        var _url = $("#_url").val();
        $.post(_url + 'contacts/edit-timesheet-post/', $( "#edit-sale-form" ).serialize())
            .done(function (data) {
                if ($.isNumeric(data)) {
                    $("#emsgbody-success").html('Timesheet Updated Successfully');
                    $("#emsg-success").show("slow");
                    $("#emsg").hide();
                    $('#ajax-modal').unblock();
                    setTimeout(function() { $('.close').click(); location.reload(); }, 1000);
                    
                    // setTimeout(function() { $('.close').click(); reset_form(); }, 2000);
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

    // $("#timesheet-from-body input").val("");
    $("#timesheet-from-body input:not([name='employee_id'])").val("");
    
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


$(document).ready(function(){     
    setTimeout(function(){ load_timesheet_data(); } , 300)
}); 

</script>