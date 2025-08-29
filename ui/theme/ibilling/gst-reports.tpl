{include file="sections/header.tpl"}
<style>
.wrapper-content {
    font-size: 12px !important;
}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title col-xs-12">
				<h5>GST Reports</h5>
				<div class="col-md-4">
					<div class="input-group">
						<!--<div class="input-group-addon">
							<span>Filter By Account</span>
						</div>-->
						<select id="filter" name="filter" style="height: 32px !important;">
							<option value="">Select Account</option>
							{foreach $ac as $row }
							<option value={$row['id']}>{$row['account']} / {$row['company']} {if $row['email'] neq ''} / {$row['email']}{/if}</option>
							{/foreach}
						</select>
					</div>
				</div>	
				<!--<input type="hidden" name="invoice_id" id="invoice_id" value="123">
				<input type="button" name="send_pdf" id="send_pdf" class="btn btn-primary btn-xs" value="Send PDF">-->
				<div class="ibox-tools">
					<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
						<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
						<span></span>
						<b class="caret"></b>
					</div>
				</div>
			</div>
			<div class="ibox-content ret_bal">
			      <div class="table-responsive">
				<table class="table table-bordered table-hover sys_table footable" id="projectSpreadsheet">
					<thead>
						<tr>
							<th>#</th>
							<th style="width: 10%;">Date</th>
							<th>{$_L['Account']}</th>
							<th>GST No</th>
							<th>Taxable {$_L['Amount']}</th>
							<th>CGST</th>
							<th>SGST</th>
							<th>IGST</th>
							<th>GST Total</th>
							<th>Invoice Amount</th>
							<th align="center">Print</th>
							<!--<th align="center"><input type="checkbox" id="checkAll"></th>-->
						</tr>
					</thead>
					<tbody>{$amt = 0} {$cgst = 0} {$sgst = 0} {$igst = 0} {$gst_total = 0} {$currency_symbol = ''} {$invtotal= 0} {foreach $invoices as $inv}
						<tr>
							{$amt = $amt + $inv['total']} {$cgst = $cgst + $inv['CGST']} {$sgst = $sgst + $inv['SGST']} {$igst = $igst + $inv['IGST']}
							{$invtotal = $invtotal + $inv['subtotal']} {$currency_symbol = $inv['currency_symbol']}
							{$gst_total = $gst_total + $inv['taxamt']}
							<td>{$inv['invoicenum']}</td>
							<td>{$inv['date']}</td>
							<td>{get_type_by_id('crm_accounts','id', $inv['userid'], 'company')}</td>
							<td align="right">{get_type_by_id('crm_accounts','id', $inv['userid'], 'gst_no')}</td>
							<td align="right">{number_format($inv['total'],2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($inv['CGST'],2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($inv['SGST'],2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($inv['IGST'],2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($inv['taxamt'],2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($inv['subtotal'],2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="center">
								<a href="{$_url}client/ipdf/{$inv['id']}/token_{$inv['vtoken']}/view" target="_blank" class="btn btn-primary btn-xs">
									<i class="fa fa-print"> </i> {$_L['Print']}</a>
							</td>
							<!--<td align="center">
								<input type="checkbox" name="invoice_pdf[]" class="check_invoice" value="{$inv['invoicenum']}">
							</td>-->
						</tr>
						{/foreach}
					</tbody>
					<tfoot>
						<tr>{$totaltax = $cgst+$sgst+$igst}
							<td colspan="4" align="right">
								<h3>Total</h3>
							</td>
							<td align="right">{number_format($amt,2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($cgst,2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($sgst,2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($igst,2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($gst_total,2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							<td align="right">{number_format($invtotal,2,$_c['dec_point'],$_c['thousands_sep'])}</td>
							
						</tr>
					</tfoot>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>
{include file="sections/footer.tpl"}