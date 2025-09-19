<script src="https://alabaya-v2.mbills.in/ui/theme/ibilling/js/jquery-1.10.2.js"></script>

<script src="https://alabaya-v2.mbills.in/ui/theme/ibilling/js/jquery-ui-1.10.4.min.js"></script>
{*{if $user->roleid == 0}
<a href="#" class="btn btn-info btn-xs salery_type_popup" data-id="{$cid}"><i class="fa fa-plus"></i> Salery Type </a>
{/if}*}
<a href="#" class="btn btn-info btn-xs salery_type_popup" data-id="{$cid}"><i class="fa fa-plus"></i> Salery Type </a>

{if !empty($employee->salery_type)}
<a href="#" class="btn btn-success btn-xs timesheet_checkin_popup" data-id="{$cid}"><i class="fa fa-plus"></i> Timesheet Entry </a>
{/if}

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body" id="timesheet-from-body">
                    <div class="row">
                        <input type="hidden" name="employee_id" value="{$employee->id}">
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
                            <button class="btn btn-primary btn-block" onclick="load_timesheet_data7();"><i class="fa fa-search" aria-hidden="true"></i></button>
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
                                <th>Employee Type</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hours / Piece</th>
                                <!-- <th>{if $employee->salery_type == 'per_hour'} Hours {else} Piece {/if}</th> -->
                                <th>Salery Amount</th>
                                <th>Earn Amount</th>
                                <th>Date</th>
                                <th class="text-right" data-sort-ignore="true">Manage</th>
                            </tr>
                        </thead>
                    </table>
                </div>
			</div>

		</div>

	</div>

</div>

<link rel="stylesheet" type="text/css" href="{$_theme}/css/modal.css"/>
<script type="text/javascript" src="{$_theme}/lib/modal.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<script>
    function load_timesheet_data7(){
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
                        columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                    }                    
                }
            ],
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
             'url': $("#_url").val() + '/client/list-ajax-timesheet/',
            "data": function ( search_param ) {
                search_param.employee_id= $('input[name="employee_id"]').val();
                search_param.fromdate   = $('input[name="fromdate"]').val();
                search_param.todate     = $('input[name="todate"]').val();
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
            	{ data: 'date' },
            	{ data: 'action' },
            ],
            "columnDefs": [
            { "orderable": false, "targets": [0, 1, 2, 3, 4, 5, 6, 7] },
            {
                targets: [6], // Column index where earn_amount is located
                createdCell: function (td, cellData, rowData, row, col) {
                    // Extract earnAmountSum from the response
                    var earnAmountSum = rowData.earnAmountSum;

                    // Update the value of the hidden input field
                    $('#total_earn_amount').val(earnAmountSum);

                    // Update the content of the div with the earnAmountSum, making it bold
                    $('#sum-earn-amt').html('Total :' + '<strong>' + earnAmountSum + '</strong>');

                }
            }
        ]          
		}); 
    } 

    function edit_timesheet_modal(cid) {
		var _url = $("#_url").val();
		var $modal = $('#ajax-modal');
		$('body').modalmanager('loading');
		$modal.load(_url + 'client/edit-timesheet-modal/' + cid, '', function() {
			$modal.modal();
		});
    } 
    
    $("body").on('click', '#edit_timesheet', function (e) {
        e.preventDefault();
        $('#ajax-modal').block({ message:block_msg });
        var _url = $("#_url").val();
        $.post(_url + 'client/edit-timesheet-post/', $( "#edit-sale-form" ).serialize())
            .done(function (data) {
                if ($.isNumeric(data)) {
                    $("#emsgbody-success").html('Timesheet Updated Successfully');
                    $("#emsg-success").show("slow");
                    $("#emsg").hide();
                    $('#ajax-modal').unblock();
                    setTimeout(function() { $('.close').click(); location.reload(); }, 1000);
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
    $("#timesheet-from-body input").val("");
    load_timesheet_data7();
} 


$(document).ready(function(){     
    load_timesheet_data7();
}); 

</script>