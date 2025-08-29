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

                    <h5>{$_L['Total']} : {$total_invoice}</h5>

                {else}

                    <h5>{$paginator['found']} {$_L['Records']}. {if $paginator['found'] > 0}{$_L['Page']} {$paginator['page']} {$_L['of']} {$paginator['lastpage']}.{/if}</h5>

                {/if}

                <div class="ibox-tools">

                    {if $view_type neq 'filter'}

                        <a href="{$_url}invoices/list/filter/" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> {$_L['Filter']}</a>

                    {else}

                        <a href="{$_url}invoices/list/" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> {$_L['Back']}</a>

                    {/if}

                    {*<a href="{$_url}invoices/list-recurring/" class="btn btn-success btn-xs"><i class="fa fa-repeat"></i> {$_L['Manage Recurring Invoices']}</a>*}

                   



                </div>

            </div>

            <div class="ibox-content">



                {if $view_type == 'filter'}

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



                <table class="table table-bordered table-hover sys_table footable" {if $view_type == 'filter'} data-filter="#foo_filter" data-page-size="50" {/if}>

                    <thead>

                    <tr>

                        <th>#</th>

                        <th>Name</th>                        <th>Phone</th>
                        <th>{$_L['Amount']}</th>
                        <th>{$_L['Invoice Date']}</th>
                        <th>{$_L['Due Date']}</th>
                        <th>
                            {$_L['Status']}
                        </th>



                    </tr>

                    </thead>

                    <tbody>



                    {foreach $d as $ds}

                        <tr>

                            <td  data-value="{$ds['id']}"><a href="{$_url}invoices/view/{$ds['id']}/">{$ds['invoicenum']}{if $ds['cn'] neq ''} {$ds['cn']} {else} {$ds['id']} {/if}</a> </td>

                            <td><a href="{$_url}contacts/view/{$ds['userid']}/">{$ds['account']}</a> </td>                            <td>{$ds['phone']}</a> </td>

                            <td class="amount" data-a-sign="{if $ds['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$ds['currency_symbol']}{/if} ">{$ds['subtotal']}</td>

                            <td data-value="{strtotime($ds['date'])}">{date( $_c['df'], strtotime($ds['date']))}</td>

                            <td data-value="{strtotime($ds['duedate'])}">{date( $_c['df'], strtotime($ds['duedate']))}</td>

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

                        </tr>

                    {/foreach}



                    </tbody>



                    {if $view_type == 'filter'}

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

                {$paginator['contents']}

            </div>

        </div>

    </div>

</div>

{include file="sections/footer.tpl"}