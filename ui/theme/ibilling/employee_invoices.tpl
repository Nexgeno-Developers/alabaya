{include file="sections/header.tpl"}
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>{$_L['Total']} : {$total_invoice}</h5>
    </div>
    <div class="panel-body" style="padding-bottom: 0;">
        <div class="radio-button">
            <input type="radio" name="payment_status" onchange="filterInvoices(this.value);" id="group_1"
                value="" {if empty($smarty.get.payment_status)}checked{/if}>
            <label for="group_1">All</label>
        </div>
        <div class="radio-button">
            <input type="radio" name="payment_status" onchange="filterInvoices(this.value);" id="group_2"
                value="1" {if isset($smarty.get.payment_status) && $smarty.get.payment_status == '1'}checked{/if}>
            <label for="group_2">Paid</label>
        </div>
        <div class="radio-button">
            <input type="radio" name="payment_status" onchange="filterInvoices(this.value);" id="group_3"
                value="0" {if isset($smarty.get.payment_status) && $smarty.get.payment_status == '0'}checked{/if}>
            <label for="group_3">Unpaid</label>
        </div>                     
    </div>


    <div class="ibox-content">
        <table class="table table-bordered table-hover sys_table">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total Earn Amount</th>
                    <th>Order Status</th>
                    <th>Salary Status</th>
                    <th>Assign Date</th>
                </tr>
            </thead>
            <tbody>
                {foreach $employeeInvoices as $invoice}
                <tr>
                    <td>{$invoice.invoice_num}</td>
                    <td>{$invoice.qty}</td>
                    <td>{$invoice.amount}</td>
                    <td>{$invoice.total_earn_amount}</td>
                    <td>
                        {if $invoice.status == 1}
                            <span class="text-success">Complete</span>
                        {else}
                            <span class="text-danger">Assigned</span>
                        {/if}
                    </td>
                    <td>
                        {if $invoice.payment_status == 1}
                            <span class="badge badge-success">Paid</span>
                        {else}
                            <span class="badge badge-danger">Unpaid</span>
                        {/if}
                    </td>
                    <td>{$invoice.created_at}</td>
                </tr>
                {/foreach}
                {if empty($employeeInvoices)}
                <tr>
                    <td colspan="5">No employee invoices found.</td>
                </tr>
                {/if}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total Earn Amount:</strong></td>
                    <td><strong>{$totalEarnSum}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    // function psearch(val){
    //     const url = $('#_url').val();
    //     const group = val; 
        
    // }   
    
function filterInvoices(val) {
    const url = $('#_url').val();
    window.location.href = url + "client/employee_invoices/&payment_status=" + val;
}
</script>

{include file="sections/footer.tpl"}
