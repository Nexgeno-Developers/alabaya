{include file="sections/header.tpl"}

{if !empty($employee->salery_type)}
    {if $employee->salery_type == 'per_hour'}
    <!-- 
    <a href="#" class="btn btn-success btn-xs timesheet_checkin_popup" id="timesheet-entry123" data-id="{$cid}"><i class="fa fa-plus"></i> Timesheet Entry </a>
    -->
    {/if}
{/if}

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body" id="timesheet-from-body">
                    <div class="row">
                        <input type="hidden" id="total_earn_amount" name="total_earn_amount" value="{$earnAmountSum}">

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
                                <th>Date</th>
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
<script type="text/javascript" src="{$_theme}/lib/modal.js"></script>


<script>
// var baseUrl = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var baseUrl = "{$APP_URL}";
let URL = baseUrl+'/?ng=';
// console.log(baseUrl+'/?ng=');
// Ensure the DOM is loaded before accessing elements
// document.addEventListener("DOMContentLoaded", function() {
//     // Retrieve the value of the _url element
//     var baseUrl = document.getElementById('_url').value;
//     console.log(baseUrl);
// });
    // console.log(URL);

   function load_timesheet_data() {
    //$("#timesheet-list-table").dataTable().fnDestroy();
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
        ajax: {
            url: URL + 'client/list-ajax-timesheet/',
            data: function (search_param) {
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
        order: [[0, "desc"]],
        columns: [
            { data: 'sr' },
            { data: 'employee_type' },
            { data: 'checkin' },
            { data: 'checkout' },
            { data: 'qty' },
            { data: 'amount' },
            { data: 'earn_amount' },
            { data: 'invoicenum' },
            { data: 'date' }
        ],
            columnDefs: [
                { orderable: false, targets: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
                {
                    targets: [6], // Column index where earn_amount is located
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
                            $('#sum-earn-amt').html('');  // Or use $('#sum-earn-amt').hide(); to hide it completely
                        }
                    }
                }
            ]
    });
}

    


    $(document).on('click', '#timesheet-entry123', function() {
        
        var cid = $(this).data('id');
        console.log(cid);
        var Url = URL + 'client/timesheet-popup-form/' + cid;
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
        $.post(URL + 'client/timesheet-entry-post', formData)
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
		$modal.load(URL + 'client/edit-timesheet-modal/' + id, '', function() {
			$modal.modal();
		});
    } 
    
    $("body").on('click', '#edit_timesheet', function (e) {
        e.preventDefault();
        $('#ajax-modal').block({ message:block_msg });
        var _url = $("#_url").val();
        $.post(URL + 'client/edit-timesheet-post/', $( "#edit-sale-form" ).serialize())
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
  
setTimeout(function(){ load_timesheet_data(); } , 300)


</script>

<div class="row">

    <div class="col-md-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">


                <h5>{$user->account}</h5>


            </div>
            <div class="ibox-content">


                <address>
                    {if $user->company neq ''}
                        {$user->company}
                        <br>
                        {$user->account}
                        <br>
                    {else}
                        {$user->account}
                        <br>
                    {/if}
                    {$user->address} <br>
                    {$user->city} <br>
                    {$user->state} - {$user->zip} <br>
                    {$user->country}
                    <br>
                    <strong>{$_L['Phone']}:</strong> {$user->phone}
                    <br>
                    <strong>{$_L['Email']}:</strong> {$user->email}
                    {foreach $cf as $cfs}
                        <br>
                        <strong>{$cfs['fieldname']}: </strong> {get_custom_field_value($cfs['id'],$user->id)}
                    {/foreach}

                </address>

                <a href="{$_url}client/profile/" class="btn btn-primary"><i class="icon-user-1"></i> Edit Profile</a>

                {$dashboard_summary_extras}



            </div>
        </div>
    </div>

    <div class="col-md-8">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Recent Transactions']}</h5>

            </div>
            <div class="ibox-content">

                <table class="table table-bordered sys_table">
                    <th>{$_L['Date']}</th>
                    <th>{$_L['Account']}</th>
                    <th>{$_L['Type']}</th>

                    <th class="text-right">{$_L['Amount']}</th>

                    <th>{$_L['Description']}</th>
                    <th class="text-right">{$_L['Dr']}</th>
                    <th class="text-right">{$_L['Cr']}</th>
                    {*<th class="text-right">{$_L['Balance']}</th>*}
                    {*<th>{$_L['Manage']}</th>*}
                    {foreach $t as $ds}
                        <tr class="{if $ds['cr'] eq '0.00'}warning {else}info{/if}">
                            <td>{date( $_c['df'], strtotime($ds['date']))}</td>
                            <td>{$ds['account']}</td>
                            {*<td>{$ds['type']}</td>*}
                            {* From v 2.4 Sadia Sharmin *}

                            <td>
                                {if $ds['type'] eq 'Income'}
                                    {$_L['Paid']}
                                {elseif $ds['type'] eq 'Expense'}
                                    {$_L['Received']}
                                {elseif $ds['type'] eq 'Transfer'}
                                    {$_L['Transfer']}
                                {else}
                                    {$ds['type']}
                                {/if}
                            </td>

                            <td class="text-right amount">{$ds['amount']}</td>
                            <td>{$ds['description']}</td>
                            <td class="text-right amount">{$ds['dr']}</td>
                            <td class="text-right amount">{$ds['cr']}</td>
                            {*<td class="text-right"><span {if $ds['bal'] < 0}class="text-red"{/if}>{$_c['currency_code']} {number_format($ds['bal'],2,$_c['dec_point'],$_c['thousands_sep'])}</span></td>*}
                            {*<td><a href="{$_url}transactions/manage/{$ds['id']}">{$_L['Manage']}</a></td>*}
                        </tr>
                    {/foreach}



                </table>

            </div>
        </div>

    </div>


</div>

{$dashboard_extra_row_1}

<!--<div class="row">

    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">


                <h5>{$_L['Recent Invoices']}</h5>


            </div>
            <div class="ibox-content">



                <table class="table table-bordered table-hover sys_table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{$_L['Account']}</th>
                        <th>{$_L['Amount']}</th>
                        <th>{$_L['Invoice Date']}</th>
                        <th>{$_L['Due Date']}</th>
                        <th>
                            {$_L['Status']}
                        </th>

                        <th class="text-right">{$_L['Manage']}</th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach $d as $ds}
                        <tr>
                            <td><a target="_blank" href="{$_url}client/iview/{$ds['id']}/token_{$ds['vtoken']}/">{$ds['invoicenum']}{if $ds['cn'] neq ''} {$ds['cn']} {else} {$ds['id']} {/if}</a> </td>
                            <td>{$ds['account']} </td>
                            <td class="amount" data-a-sign="{if $ds['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$ds['currency_symbol']}{/if} ">{$ds['total']}</td>
                            <td>{date( $_c['df'], strtotime($ds['date']))}</td>
                            <td>{date( $_c['df'], strtotime($ds['duedate']))}</td>
                            <td>

                                {if $ds['status'] eq 'Unpaid'}
                                    <span class="label label-danger">{ib_lan_get_line($ds['status'])}</span>
                                {elseif $ds['status'] eq 'Paid'}
                                    <span class="label label-success">{ib_lan_get_line($ds['status'])}</span>
                                {elseif $ds['status'] eq 'Partially Paid'}
                                    <span class="label label-info">{ib_lan_get_line($ds['status'])}</span>
                                {elseif $ds['status'] eq 'Cancelled'}
                                    <span class="label">{ib_lan_get_line($ds['status'])}</span>
                                {else}
                                    {ib_lan_get_line($ds['status'])}
                                {/if}



                            </td>

                            <td class="text-right">
                                <a target="_blank" href="{$_url}client/iview/{$ds['id']}/token_{$ds['vtoken']}/" class="btn btn-primary btn-xs"><i class="fa fa-check"></i> {$_L['View']}</a>
                                <a href="{$_url}client/ipdf/{$ds['id']}/token_{$ds['vtoken']}/dl/" class="btn btn-primary btn-xs"><i class="fa fa-file-pdf-o"></i> {$_L['Download']}</a>
                                <a target="_blank" href="{$_url}iview/print/{$ds['id']}/token_{$ds['vtoken']}/" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> {$_L['Print']}</a>

                            </td>
                        </tr>
                    {/foreach}

                    </tbody>



                </table>

            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-12">


        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><h5>{$_L['Recent Quotes']}</h5></h5>
            </div>
            <div class="ibox-content">

                <table class="table table-bordered table-hover sys_table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{$_L['Account']}</th>
                        <th width="30%">{$_L['Subject']}</th>
                        <th>{$_L['Amount']}</th>
                        <th>{$_L['Date Created']}</th>
                        <th>{$_L['Expiry Date']}</th>
                        {*<th>{$_L['Stage']}</th>*}

                        <th class="text-right">{$_L['Manage']}</th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach $q as $ds}
                        <tr>
                            <td><a href="{$_url}quotes/view/{$ds['id']}/">{$ds['id']}</a> </td>
                            <td><a href="{$_url}contacts/view/{$ds['userid']}/">{$ds['account']}</a> </td>
                            <td><a href="{$_url}quotes/view/{$ds['id']}/">{$ds['subject']}</a> </td>
                            <td class="amount">{$ds['total']}</td>
                            <td>{date( $_c['df'], strtotime($ds['datecreated']))}</td>
                            <td>{date( $_c['df'], strtotime($ds['validuntil']))}</td>


                            <td class="text-right">
                                <a href="{$_url}quotes/view/{$ds['id']}/" class="btn btn-primary btn-xs"><i class="fa fa-check"></i> {$_L['View']}</a>

                                <a href="{$_url}client/qpdf/{$ds['id']}/token_{$ds['vtoken']}/dl/" class="btn btn-primary btn-xs" ><i class="fa fa-file-pdf-o"></i> {$_L['Download']}</a>
                                <a href="{$_url}client/qpdf/{$ds['id']}/token_{$ds['vtoken']}/" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> {$_L['Print']}</a>
                            </td>
                        </tr>
                    {/foreach}

                    </tbody>
                </table>

            </div>
        </div>

    </div>

</div> -->


{include file="sections/footer.tpl"}