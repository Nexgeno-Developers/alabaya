{include file="sections/header.tpl"}
<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<label for="exampleInputEmail1">Unique Proforma URL:</label>
			<input type="text" class="form-control" id="invoice_public_url" onClick="this.setSelectionRange(0, this.value.length)" value="{$_url}client/proforma-iview/{$d['id']}/token_{$d['vtoken']}">
		</div>
	</div>
	<div class="col-lg-12"  id="application_ajaxrender">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Proforma - {if $d['cn'] neq ''} {$d['cn']} {else} {$d['invoicenum']} {/if}</h5>
				<input type="hidden" name="iid" value="{$d['id']}" id="iid">
				<div class="btn-group  pull-right" role="group" aria-label="...">
					<div class="btn-group" role="group">
						<button type="button" class="btn  btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-envelope-o"></i>  {$_L['Send Email']}
								<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
								<li><a href="#" id="mail_performa_created">Proforma Created</a></li>
								<li><a href="#" id="mail_performa_reminder">Proforma Payment Reminder</a></li>
						</ul>
					</div>
						<div class="btn-group" role="group">
						{if $d['invoice_status'] eq '1'}
						<button type="button" id="generated_invoice" class="btn btn-success btn-sm" style="background-color: green;" aria-expanded="false">
								<i class="fa fa-ellipsis-v"></i> Generated
						</button>
						{else}
						<button type="button" id="generate_invoice" class="btn btn-primary btn-sm" aria-expanded="false">
								<i class="fa fa-ellipsis-v"></i> Generate Invoice
						</button>
						{/if}
					</div>
					{if $d['invoice_status'] eq '0'}
										{if $_c['accounting'] eq '1'}
							<button type="button" class="btn  btn-danger btn-sm" id="add_proforma_payment"><i class="fa fa-plus"></i> {$_L['Add Payment']}</button>
					{/if}
					{/if}
						{if $d['status'] eq 'Unpaid'}
					<a href="{$_url}invoices/proforma-edit/{$d['id']}" class="btn btn-warning  btn-sm"><i class="fa fa-pencil"></i> {$_L['Edit']}</a>
					{/if}
					<div class="btn-group" role="group">
						<button type="button" class="btn  btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-file-pdf-o"></i>
								{$_L['PDF']}
								<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{$_url}client/performapdf/{$d['id']}/token_{$d['vtoken']}/view/" target="_blank">{$_L['View PDF']}</a></li>
							<li><a href="{$_url}client/performapdf/{$d['id']}/token_{$d['vtoken']}/dl/">{$_L['Download PDF']}</a></li>
						</ul>
					</div>
					<!-- <a href="{$_url}iview/print/{$d['id']}/token_{$d['vtoken']}" target="_blank" class="btn btn-primary  btn-sm"><i class="fa fa-print"></i> {$_L['Print']}</a> -->
				</div>
			</div>
			<div class="ibox-content">
				<div class="invoice">
					<header class="clearfix">
						<div class="row">
							<div class="col-sm-6 mt-md">
									<h2 class="h2 mt-none mb-sm text-dark text-bold">Proforma</h2>
									<h4 class="h4 m-none text-dark text-bold">#{if $d['cn'] neq ''} {$d['cn']} {else} {$d['invoicenum']} {/if}</h4>
									{if $d['status'] eq 'Unpaid'}
											<h3 class="alert alert-danger">{$_L['Unpaid']}</h3>
									{elseif $d['status'] eq 'Paid'}
											<h3 class="alert alert-success">{$_L['Paid']}</h3>
									{elseif $d['status'] eq 'Partially Paid'}
											<h3 class="alert alert-info">{$_L['Partially Paid']}</h3>
									{else}
											<h3 class="alert alert-info">{$d['status']}</h3>
									{/if}
								<div class="bill-to">
									<p class="h5 mb-xs text-dark text-semibold"><strong>Proforma To:</strong></p>
									<address>  
											{if $a['company'] neq ''}
													{$a['company']}
													<br>
												 {$_L['ATTN']}: {$d['account']}
													<br>
													{else}
													{$d['account']}
													<br>
											{/if}

											{$a['address']} <br>
											{$a['city']} <br>
											{$a['state']} - {$a['zip']} <br>
											{$a['country']}
											<br>
											<strong>{$_L['Phone']}:</strong> {$a['phone']}
											<br>
											<strong>{$_L['Email']}:</strong> {$a['email']}
											{foreach $cf as $cfs}
													<br>
													<strong>{$cfs['fieldname']}: </strong> {get_custom_field_value($cfs['id'],$a['id'])}
											{/foreach}
											<br>
											{$x_html}
									</address>
								</div>
							</div>
							<div class="col-sm-6 text-right mt-md mb-md">
								<div class="ib">
									<img width="200px" src="{$app_url}application/storage/system/{$comp['company_logo']}" alt="Logo">
								</div>  
								<address class="ib mr-xlg">
									<strong>{$comp['account']}</strong><br>
									{$comp['address']}<br>
									<strong>{$_L['Email']}:</strong> {$comp['email']}<br>
									<strong>{$_L['Phone']}:</strong> {$comp['contact_phone']}<br>
									<!-- {$comp|@var_dump} -->
									<!-- {$_c['caddress']} -->
								</address>	
							</div>
						</div>
					</header>
					<div class="bill-info">
						<div class="row">
							<div class="col-md-6">
									
							</div>
							<div class="col-md-6">
								<div class="bill-data text-right">
										<p class="mb-none">
												<span class="text-dark">Proforma Date:</span>
												<span class="value">{date( $_c['df'], strtotime($d['date']))}</span>
										</p>
										<p class="mb-none">
												<span class="text-dark">{$_L['Due Date']}:</span>
												<span class="value">{date( $_c['df'], strtotime($d['duedate']))}</span>
										</p>
										<h2> Proforma Total: <span class="amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$d['subtotal']}</span> </h2>
										{if ($d['credit']) neq '0.00'}
												<h2> {$_L['Total Paid']}:  <span class="amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$d['credit']}</span> </h2>
												{*<h2> {$_L['Amount Due']}: {$_c['currency_code']} {number_format($i_due,2,$_c['dec_point'],$_c['thousands_sep'])} </h2>*}
												<h2> {$_L['Amount Due']}: <span class="amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$i_due}</span> </h2>
										{/if}
								</div>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table invoice-items">
							<thead>
								<tr class="h4 text-dark">
									<th id="cell-id" class="text-semibold">S.No</th>
									<th id="cell-item" class="text-semibold">{$_L['Item']}</th>
									<th id="cell-qty" class="text-center text-semibold">{$_L['Quantity']}</th>
									<th id="cell-price" class="text-center text-semibold">{$_L['Rate']}</th>
									<th id="cell-gst_per" class="text-center text-semibold">{$_L['gst_per']}</th>
									<th id="cell-gst_amt" class="text-center text-semibold">{$_L['gst_amt']}</th>
									<th id="cell-total" class="text-center text-semibold">{$_L['Total']}</th>
								</tr>
							</thead>
							<tbody>
								{$i = 1} 
								{foreach $items as $item}
								<tr>
									<td>{$i++}</td>
									<td class="text-semibold text-dark">{$item['description']}</td>
									<td class="text-center">{$item['qty']}</td>
									<td class="text-center amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$item['amount']}</td>
									<td class="text-center amount" >{$item['taxrate']}</td>
									<td class="text-center amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$item['taxamount']}</td>
									
									<td class="text-center amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$item['total']}</td>
								</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
					<div class="invoice-summary">
						<div class="row">
							<div class="col-sm-4 col-sm-offset-8">
								<table class="table h5 text-dark">
									<tbody>
									<tr class="b-top-none">
											<td colspan="2">{$_L['Subtotal']}</td>
											<td class="text-right amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$d['total']}</td>
									</tr>
								
									<tr>
											<td colspan="2">{$_L['TAX']}</td>
											<td class="text-right amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$d['taxamt']}</td>
									</tr>
									 {if ($d['discount']) neq '0.00'}
										 <tr>
												 <td colspan="2">{$_L['Discount']}</td>
												 <td class="text-right amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$d['discount']}</td>
										 </tr>
								 {/if}
									{if ($d['credit']) neq '0.00'}
											<tr>
													<td colspan="2">Invoice {$_L['Total']}</td>
													<td class="text-right">{ib_money_format($d['subtotal'],$_c,$d['currency_symbol'])}</td>
											</tr>
											<tr>
													<td colspan="2">{$_L['Total Paid']}</td>
													<td class="text-right amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$d['credit']}</td>
											</tr>
											<tr class="h4">
													<td colspan="2">{$_L['Amount Due']}</td>
													{*<td class="text-left">{$_c['currency_code']} {number_format($i_due,2,$_c['dec_point'],$_c['thousands_sep'])}</td>*}
													<td class="text-right amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$i_due}</td>
											</tr>
											{else}
											<tr class="h4">
													<td colspan="2">{$_L['Grand Total']}</td>
													<td class="text-right amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$d['subtotal']}</td>
											</tr>
									{/if}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
								{if ($trs_c neq '')}
				<h3>{$_L['Related Transactions']}</h3>
				<table class="table table-bordered sys_table">
					 <th>{$_L['Date']}</th>
					 <th>{$_L['Account']}</th>
					 <th>{$_L['Method']}</th>
					 <th class="text-right">{$_L['Amount']}</th>
					 <th>{$_L['Description']}</th>
					 <th>{$_L['Manage']}</th>
					 {foreach $trs as $tr}
							 <tr class="{if $tr['cr'] eq '0.00'}warning {else}info{/if}">
									 <td>{date( $_c['df'], strtotime($tr['date']))}</td>
									 <td>{$tr['account']}</td>
									 <td>{$tr['method']}</td>
									 <td class="text-right amount" data-a-sign="{if $d['currency_symbol'] eq ''} {$_c['currency_code']} {else} {$d['currency_symbol']}{/if} ">{$tr['amount']}</td>
									 <td>{$tr['description']}</td>
									 <td><a href="{$_url}transactions/proforma-manage/{$tr['id']}">{$_L['Manage']}</a></td>
							 </tr>
					 {/foreach}
				</table>
								{/if}
<h5>Notes</h5>
				{if ($d['notes']) neq ''}
						<div class="well m-t">
								{$d['notes']}
						</div>
				{/if}
				{if ($d['notes']) eq ''}
						<div class="well m-t">
								{$app_config['value']}
						</div>
				{/if}
				
				{if ($emls_c neq '')}
						<hr>
						<h3>{$_L['Related Emails']}</h3>
						<table class="table table-bordered sys_table">
								<th width="20%">{$_L['Date']}</th>
								<th>{$_L['Subject']}</th>

								{foreach $emls as $eml}
										<tr>
												<td>{date( $_c['df'], strtotime($eml['date']))}</td>
												<td>{$eml['subject']}</td>
										</tr>
								{/foreach}
						</table>
				{/if}
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="_lan_msg_confirm" value="{$_L['are_you_sure']}">
<input type="hidden" id="admin_email" value="{$user->username}">


{include file="sections/footer.tpl"}