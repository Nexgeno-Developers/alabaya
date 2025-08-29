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
						</tr>
					</thead>
					<tbody>{$amt = 0} {$cgst = 0} {$sgst = 0} {$igst = 0} {$gst_total = 0} {$currency_symbol = ''} {$invtotal= 0}
						{foreach $invoices as $inv}
							<tr> 
								{$amt 	= $amt  + $inv['total']}
								{$cgst	= $cgst + $inv['CGST']}
								{$sgst	= $sgst + $inv['SGST']}
								{$igst	= $igst + $inv['IGST']}
								{$invtotal	= $invtotal + $inv['subtotal']}
								{$currency_symbol = $inv['currency_symbol']}
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
								<td align="center"><a href="{$_url}client/ipdf/{$inv['id']}/token_{$inv['vtoken']}/view" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print">  </i> {$_L['Print']}</a></td>
							</tr>
						{/foreach}
					</tbody>
					<tfoot>
						<tr>{$totaltax = $cgst+$sgst+$igst}
							<td colspan="4" align="right"><h3>Total</h3></td>
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