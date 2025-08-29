<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Detail View <b>{SaleID($sale['id'])}</b></h3>
</div>
<div class="modal-body">
	<div class="row">
        <div class="col-md-4">
            <p><b>Customer :</b> <a href="{$_url}contacts/view/{$customer['id']}/summary/" target="_blank">{$customer['account']} ({$customer['company']}) </a> </p>
        </div>
        <div class="col-md-4">
            <p><b>Agent :</b> {$agent['fullname']} </p>
        </div>
        <div class="col-md-4">
            <p>
            <b>Domain :</b> 
                {if $sale['domain'] == NULL}
                     -
                {else}
                    <a href="http://{clean_url($sale['domain'])}" target="_blank">{clean_url($sale['domain'])}</a> 
                    <a target="_blank" class="btn-xs btn-warning" href="https://www.whois.com/whois/{clean_url($sale['domain'])}">WhoIs</a>
                {/if}

            </p>
            
        </div> 
        <div class="col-md-4">
            <p><b>Service :</b> {$service} ({$sale['service_type']})</p>
        </div>        
        <div class="col-md-4">
            <p><b>Duration :</b> {$sale['duration']} {$sale['duration_type']} </p>
        </div> 
        <div class="col-md-4">
            <p><b>Amount :</b> {$sale['amount']} </p>
        </div> 
        <div class="col-md-4">
            <p><b>Registration Date :</b>{date('d M Y', strtotime($sale['ragister_date']))}</p>
        </div>
        <div class="col-md-4">
            <p><b>Updated Date :</b> {date('d M Y', strtotime($sale['update_date']))} </p>
        </div>
        <div class="col-md-4">
            <p><b>Expiry Date :</b> {date('d M Y', strtotime($sale['expire_date']))} </p>
        </div>                                                     
		<div class="col-md-12">
            <p><b>Note :</b> {$sale['note']} </p>       
		</div>
        <div class="col-md-12">
            <hr>
            <h3>All Bills</h3>        
            <div style="overflow-x: auto;">
                <table id="sale-transaction" class="table table-bordered table-striped dt-responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>Sr.</th>
                            <th>service</th>
                            <th>Type</th>
                            <!--<th>Domain</th>-->
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Updated Date</th>
                            <th>Expiry Date</th>                            
                                                       
                            <!--<th>Notes</th>-->
                            <th>Proforma</th>
                            <th>Invoice</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$i = 1}
                        {foreach $sale_transaction as $tran}
                        {$s_data = json_decode($tran['sale_data'], true)}
                        {$proforma = get_type_by_id_multi('sys_performa', 'sale_trans_code', $tran['sale_trans_code'], 'id,status,invoicenum')}
                        {$invoice = get_type_by_id_multi('sys_invoices', 'sale_trans_code', $tran['sale_trans_code'], 'id,status')}
                            <tr>
                                <td>{$i++}</td>
                                <td>{get_type_by_id('sys_items', 'id', $s_data['service_id'], 'name')}</td>
                                <td>{$s_data['service_type']}</td>
                                <!--<td>{$s_data['domain']}</td>-->
                                <td>{$s_data['duration']} {$s_data['duration_type']}</td>
                                <td>{$s_data['amount']}</td>
                                <td>{date('d M Y', strtotime($s_data['update_date']))}</td>
                                <td>{date('d M Y', strtotime($s_data['expire_date']))}</td>        
                                
                                <!--<td>{$s_data['note']}</td>-->
                                <td>
                                    {if $invoice['id'] == ''}
                                        {if $proforma['id'] == ''}
                                            Not generated 
                                        {else}
                                            {$proforma['status']}
                                        {/if} 
                                    {else}
                                        <a href="{$_url}invoices/performa-view/{$proforma['id']}" target="_blank">{$proforma['invoicenum']}</a>
                                    {/if}       
                                </td>
                                <td>
                                    {if $invoice['id'] == ''}
                                        Not generated 
                                    {else}
                                        {$invoice['status']}
                                    {/if} 
                                </td>
                                <td>
                                {if $invoice['id'] == ''}
                                    <a class="btn btn-xs btn-primary" href="{$_url}invoices/performa-view/{$proforma['id']}" target="_blank">View Proforma</a>
                                {else}
                                    <a class="btn btn-xs btn-info" href="{$_url}invoices/view/{$invoice['id']}" target="_blank">View Invoice</a>
                                {/if}
                                <a onclick="return confirm_box();" class="btn btn-xs btn-danger" href="{$_url}sales/delete-bill/{$tran['sale_trans_code']}">Delete Bill</a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>        
            </div>
        </div>


        <div class="col-md-12">
            <hr>
            <h3>All Logs</h3>        
            <div style="overflow-x: auto;">
                <table id="sale-logs" class="table table-bordered table-striped dt-responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>Sr.</th>
                            <th>action</th>
                            <th>timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$x = 1}
                        {foreach $sale_logs as $log}
                            <tr>
                                <td>{$x++}</td>
                                <td>{$log['action']}</td>
                                <td>{$log['timestamp']}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>        
            </div>
        </div>


	</div>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn">Close</button>
</div>

<script>
$(document).ready(function() {
    $('#sale-transaction').DataTable();
    $('#sale-logs').DataTable();
} );

function confirm_box() 
{
    if (confirm("Proforma & invoice of this bill will be deleted permanently.\r\nAre you sure?") == true) 
    {
        return true;
    } 
    else 
    {
        return false;
    }
}
</script>