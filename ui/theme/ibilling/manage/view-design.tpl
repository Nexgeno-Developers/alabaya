{include file="sections/header.tpl"}

<div class="row">
	<div class="col-lg-12"  id="application_ajaxrender">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">		
                        <h1>Product Name : <b>{$p_name}</b></h1>	
                    </div>
                </div>
                
        {assign var="invoiceIds" value=[]}
        {foreach $invoiceItems as $row}
           {if !in_array($row.invoiceid, $invoiceIds)}
               {assign var="invoiceIds" value=array_merge($invoiceIds, [$row.invoiceid])}
           {/if}
        {/foreach}               

		<div class="ibox float-e-margins">
			<div class="ibox-content">			
				<h3>Designs used in Invoices</h3>
				<table class="table table-bordered sys_table">
                    <th>#</th>
                    <th>Invoice</th>
                    <th>Date</th>
                    {$i = 1}{foreach $invoiceIds as $invoiceId}
                    {$invDetail = get_type_by_id_multi('sys_invoices', 'id', $invoiceId, 'invoicenum,date')}
                    <tr>
                        <td>{$i++}</td>
                        <td>{$invDetail['invoicenum']}</td>
                        <td>{date( $_c['df'], strtotime($invDetail['date']))}</td>
                    </tr> 
                    {/foreach}
				</table>			
			</div>
        </div>

{include file="sections/footer.tpl"}