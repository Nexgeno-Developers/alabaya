{include file="sections/header.tpl"}

{$stock = json_decode(product_stock_info($id), true)}
<div class="row">
	<div class="col-lg-12"  id="application_ajaxrender">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">		
                        <h1>Product Name : <b>{$p_name}</b></h1>	
                        <h3>Current Stock : {$stock['current_stock_count']} {ucfirst($item['product_stock_type'])}</h3>			
                    </div>
                </div>

		<div class="ibox float-e-margins">
			<div class="ibox-content">			
				<h3>Credited Stocks</h3>
				<table class="table table-bordered sys_table">
                    <th>#</th>
                    <th>Stock</th>
                    <th>Date</th>
                    {$i = 1}{foreach $credited_stock as $row}
                    <tr>
                        <td>{$i++}</td>
                        <td>{$row['stock']}</td>
                        <td>{date('Y-m-d H:i:s', strtotime($row['timestamp']))}</td>
                    </tr> 
                    {/foreach}
				</table>			
			</div>
        </div>

		<div class="ibox float-e-margins">
			<div class="ibox-content">			
				<h3>Debited Stocks</h3>
				<table class="table table-bordered sys_table">
                    <th>#</th>
                    <th>Stock</th>
                    <th>Ready Product Name</th>
                    <th>Invoice ID</th>
                    <th>Date</th>
                    {$i = 1}{foreach $debited_stock as $row}
                    <tr>
                        <td>{$i++}</td>
                        <td>{$row['stock']}</td>
                        <td>
                            {if !empty($row['parent_item_id'])}
                                {get_type_by_id('sys_items', 'id', $row['parent_item_id'], 'name')}
                            {else}
                                -
                            {/if}
                        </td>
                        <td>
                            {if !empty($row['invoice_id'])}
                                {get_type_by_id('sys_invoices', 'id', $row['invoice_id'], 'invoicenum')}
                            {else}
                                -
                            {/if}
                        </td>
                        <td>{date('Y-m-d H:i:s', strtotime($row['timestamp']))}</td>
                    </tr> 
                    {/foreach}
				</table>			
			</div>
        </div>        
        

		{*<div class="ibox float-e-margins">
            <div class="ibox-content">			
                <h3>Debited Stocks From Invoice</h3>
                <table class="table table-bordered sys_table">
                    <th>#</th>
                    <th>Invoice ID</th>
                    <th>Stock</th>
                    <th>Date</th>
                    {$i = 1}{foreach $sys_invoiceitems as $row}
                    {if $row['invoice_id'] != 0}
                    <tr>
                        <td>{$i++} {$row['id']}</td>
                        <td>{get_type_by_id('sys_invoices', 'id', $row['invoice_id'], 'invoicenum')}</td>
                        <td>{$row['stock']}</td>
                        <td>{get_type_by_id('sys_invoices', 'id', $row['invoice_id'], 'duedate')}</td>
                    </tr> 
                    {/if}
                    {/foreach}
                </table>			
            </div>
        </div> 
        
        
		<div class="ibox float-e-margins">
            <div class="ibox-content">			
                <h3>Debited Stocks From Ready Product</h3>
                <table class="table table-bordered sys_table">
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Stock</th>
                    <th>Date</th>
                    {$i = 1}{foreach $sys_invoiceitems as $row}
                    {if !empty($row['parent_item_id'])}
                    <tr>
                        <td>{$i++} {$row['id']}</td>
                        <td>{get_type_by_id('sys_items', 'id', $row['parent_item_id'], 'name')}</td>
                        <td>{$row['stock']}</td>
                        <td>{$row['timestamp']}</td>
                    </tr> 
                    {/if}
                    {/foreach}
                </table>			
            </div>
        </div>        
	</div>
</div>*}

{include file="sections/footer.tpl"}