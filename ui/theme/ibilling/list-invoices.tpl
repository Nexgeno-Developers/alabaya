{include file="sections/header.tpl"}

{*<div class="row">*}
    {*<div class="col-md-12">*}
        {*<div class="ibox float-e-margins">*}
            {*<div class="ibox-title">*}
                {*<h5>{$_L['Summary']}</h5>*}
            {*</div>*}
            {*<div class="ibox-content">*}
            {*</div>*}
        {*</div>*}
    {*</div>*}
{*</div>*}

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                {if $view_type == 'filter'}
                    <h5>{$_L['Total']} : 
                        {if !empty($inv_count)}     
                            {$inv_count}
                        {else}
                            {$total_invoice}
                        {/if}
                    </h5>
                {else}
                    <h5>{$paginator['found']} {$_L['Records']}. {if $paginator['found'] > 0}{$_L['Page']} {$paginator['page']} {$_L['of']} {$paginator['lastpage']}.{/if}</h5>
                {/if}
                <div class="ibox-tools">
                        {if $user->roleid eq 0}
                    {if $view_type neq 'filter'}
                        <a href="{$_url}invoices/list/filter/" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> {$_L['Filter']}</a>
                    {else}
                        <a href="{$_url}invoices/list/" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> {$_L['Back']}</a>
                    {/if}
                    {*<a href="{$_url}invoices/list-recurring/" class="btn btn-success btn-xs"><i class="fa fa-repeat"></i> {$_L['Manage Recurring Invoices']}</a>*}
                    
                    <a href="{$_url}invoices/add/" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> {$_L['Add Invoice']}</a>
                    {/if}

                </div>
            </div>
            <div class="ibox-content">

                {if $view_type == 'filter1'}
                    <form class="form-horizontal" method="post" action="{$_url}customers/list/">
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <input type="text" name="name" id="foo_filter" class="form-control" placeholder="{$_L['Search']}..."/>

                                </div>
                            </div>

                        </div>
                    </form>
                {/if}
<div class="table-responsive tb"> 
                <table id="invoice-datatable" class="table table-bordered table-hover" {if $view_type == 'filter1'} data-filter="#foo_filter" data-page-size="50" {/if}>
                    <thead>
                    <tr>
                        <th>Invoice No</th>
                        <!--<th>Sale ID</th>
                        <th>{$_L['Account']}</th>-->
                        <th>Customer</th>
                        <!--<th>Email</th>-->
                        <th>Phone</th>
                        {if $user['roleid'] neq '1'}
                        <th>{$_L['Amount']}</th>
                        {/if}
                        <th>{$_L['Invoice Date']}</th>
                        <th>Delivery date</th>
                        <th>Reminder date</th>
                        <th>
                            Payment Status
                        </th>
                        <th>
                            Invoice Status
                        </th>                        
                        <th>Created By</th>
                        <th>Updated At</th>
                        <th>Created At</th>
                        <!--<th>{$_L['Type']}</th>-->
                        <th class="text-right">{$_L['Manage']}</th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach $d as $ds}
                    {$customer = get_type_by_id_multi('crm_accounts', 'id', $ds['userid'], 'phone,email,account,company')}
                        <tr>
                            <td  data-value="{$ds['id']}"><a href="{$_url}invoices/view/{$ds['id']}/">{if $ds['cn'] neq ''} {$ds['cn']} {else} {$ds['invoicenum']} {/if}</a> </td>
                            <!--<td>{SaleID($ds['sale_id'])}</td>
                            <td><a href="{$_url}contacts/view/{$ds['userid']}/">{$customer['company']}</a> </td>-->
                            <td>{$customer['account']}</td>
                            <!--<td>{$customer['email']}</td>-->
                            <td>{$customer['phone']}</td>

                            {if $user['roleid'] neq '1'}
                            <td class="amount" data-a-sign="{if $ds['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$ds['currency_symbol']}{/if} ">{$ds['subtotal']}</td>
                            {/if}

                            <td data-value="{strtotime($ds['date'])}">{date( $_c['df'], strtotime($ds['date']))}</td>
                            <td data-value="{strtotime($ds['duedate'])}">{date( $_c['df'], strtotime($ds['duedate']))}</td>
                            <td data-value="{strtotime($ds['reminder_date'])}">{$ds['reminder_date']}</td>
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
                            <!--<td>
                                {if $ds['r'] eq '0'}
                                    <span class="label label-success"><i class="fa fa-dot-circle-o"></i> {$_L['Onetime']}</span>
                                {else}
                                    <span class="label label-success"><i class="fa fa-repeat"></i> {$_L['Recurring']}</span>
                                {/if}
                            </td>-->
                            <td>{$ds['delivery_status']}</td>
                            <td>{get_type_by_id('sys_users', 'id', $ds['created_by'], 'fullname')}</td>
                            <td>{if !empty($ds['updated_at'])} {date('Y-m-d H:i A', $ds['updated_at'])} {/if}</td>
                            <td>{if !empty($ds['created_at'])} {date('Y-m-d H:i A', $ds['created_at'])} {/if}</td>
                            <td class="text-right">
                            {if $user['roleid'] neq '1'}
                                <a href="{$_url}invoices/view/{$ds['id']}/" class="btn btn-primary btn-xs"><i class="fa fa-check"></i> {$_L['View']}</a>
																{if $ds['status'] eq 'Unpaid'}
                                <a href="{$_url}invoices/edit/{$ds['id']}/" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> {$_L['Edit']}</a>
                                                                {/if}
                                {if empty($ds['sale_trans_code'])}                                
                                <a href="#" class="btn btn-danger btn-xs cdelete" id="iid{$ds['id']}"><i class="fa fa-trash"></i> {$_L['Delete']}</a>
                                {/if}
                            {else}
                            <a target="_blank" href="{$_url}client/iview-tailor/{$ds['id']}/token_{$ds['vtoken']}" class="btn btn-primary btn-xs"><i class="fa fa-check"></i> View bill</a>
                            {/if}
                            </td>
                        </tr>
                    {/foreach}

                    </tbody>

                    {if $view_type == 'filter1'}
                        <tfoot>
                        <tr>
                            <td colspan="8">
                                <ul class="pagination">
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    {/if}

                </table>
                </div>
                {$paginator['contents']}
            </div>
        </div>
    </div>
</div>
{include file="sections/footer.tpl"}

<style>
    .tb th {
    font-size: 12px;
}
</style>

<script>
    $(document).ready(function(){
        $('#invoice-datatable').DataTable({
            "order": [[10, "desc"]]
        });
    });
</script>