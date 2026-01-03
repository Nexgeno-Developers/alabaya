{include file="sections/header.tpl"}
<style>
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0; 
}
</style>
<div class="row" id="ibox_form">
	<form id="invform" method="post">
		<div class="col-md-12">
				<div class="alert alert-danger alert-dismissable" id="emsg">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<span id="emsgbody"></span>
				</div>
		</div>
			{*<input type="text" class="form-control item_name" name="desc[]" value="">*}
		<div class="col-md-9">

				<div class="panel panel-default">
						<div class="panel-body pdd_010">
							<div class="row">
<div class="ibox-title margbtm_20">
                <h5><i class="fa fa-user" aria-hidden="true"></i> Customer Information <span id="already_cust">(Existing)</span></h5>


            </div>


								<div class="col-md-4">
									<label for="cust_name">Customer Name</label>
									<input type="text" class="form-control" name="cust_name" required>
								</div>
								<div class="col-md-4">
									<label for="cust_phone">Contact Number</label>
									<input type="text" class="form-control" name="cust_phone" required>
								</div>	
								<div class="col-md-4">
									<label for="cust_location">Location</label>
									<input type="text" class="form-control" name="cust_location" required>
								</div>
								<div class="col-md-12">
									<br>
								</div>											
							</div>
							<div class="row">
								<div class="col-md-4 col_with_21">
									<h3 style="margin-top: 25px;"><i class="fa fa-tachometer" aria-hidden="true"></i>  Measurements - </h3>
								</div>
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="length">Length</label>
										<input type="text" class="form-control pad-2" name="cust_length">
									</div>
								</div>
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="shoulder">Shoulder</label>
										<input type="text" class="form-control pad-2" name="cust_shoulder">
									</div>
								</div>	
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="sleeves">Sleeves</label>
										<input type="text" class="form-control pad-2" name="cust_sleeves">
									</div>
								</div>
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="armole">Armole</label>
										<input type="text" class="form-control pad-2" name="cust_armole">
									</div>
								</div>
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="cuff">Cuff</label>
										<input type="text" class="form-control pad-2" name="cust_cuff">
									</div>
								</div>
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="chest">Chest</label>
										<input type="text" class="form-control pad-2" name="cust_chest">
									</div>
								</div> 
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="waist">Waist</label>
										<input type="text" class="form-control pad-2" name="cust_waist">
									</div>
								</div>
								<div class="col-md-1 col_with_9">
									<div class="form-group">
										<label for="hipps">Hipps</label>
										<input type="text" class="form-control pad-2" name="cust_hipps">
									</div>
								</div>					
							</div>
							<div class="row">
									<div class="col-md-4 col_with_21">
										<h3><i class="fa fa-user" aria-hidden="true"></i> Other - </h3>
									</div>						
									<div class="col-md-6 pdd_00">
										<input type="checkbox" name="is_pocket" value="yes" id="is_pocket"> 
										<label class="pd_15" for="is_pocket">Pocket</label>
										
										<input type="checkbox" name="is_zip" value="yes" id="is_zip"> 
										<label class="pd_15" for="is_zip">Zip</label>

										<input type="checkbox" name="is_beading" value="yes" id="is_beading"> 
										<label class="pd_15" for="is_beading">Beading</label>	
										
										<input type="checkbox" name="is_folding" value="yes" id="is_folding"> 
										<label class="pd_15" for="is_folding">Folding</label>
		
										<input type="checkbox" name="is_pico" value="yes" id="is_pico"> 
										<label class="pd_15" for="is_pico">Pico</label>

										<br>
										<input type="checkbox" name="is_umbrella" value="yes"> 
										<label class="pd_10" for="umbrella_size">Umbrella</label>
										<input style="width:100px;display: inline;" class="form-control size_width" type="text" name="umbrella_size" placeholder="Size" id="umbrella_size">	
										
								<input type="checkbox" name="is_dupatta" value="yes"> 
								<label class="pd_10" for="dupatta_size">Dupatta</label>
								<input style="width:100px;display: inline;" class="form-control size_width" type="text" name="dupatta_size" placeholder="Size" id="dupatta_size">
									</div>	
									<div class="col-md-2 pdd_00 text-center" style="margin-top: 3px;">
										<span><b>Display Measurement</b></span><br>
										<input type="radio" id="age1" name="d_measure" value="yes" {if $i['d_measure'] eq 'yes'} checked {/if}>
										<label for="age1">Yes</label><br>
										<input type="radio" id="age2" name="d_measure" value="no" {if $i['d_measure'] eq 'no'} checked {/if}>
										<label for="age2">No</label><br>
									</div>					
							</div>

							<div class="row">
									<div class="col-md-4 col_with_21">
										<h3><i class="fa fa-user" aria-hidden="true"></i> File Upload - </h3>
									</div>
									<div class="col-md-4 pdd_00">
										<input type="file" accept="image/*" name="additional_imgs[]" class="form-control" multiple>										
									</div>	
									<div class="col-md-4">
										{assign z 0}
										{foreach json_decode($d['additional_imgs']) as $val}
										<span id="add_img_{$z}">
											<span onclick="delete_img('{$d["id"]}', '{$val}', {$z})"><i class="fa fa-close text-danger"></i></span>
											<img data-img="{$val}" src="{$val}" width="50px" height="50px" class="img-popup">
											<input type="hidden" name="existing_additional_imgs[]" value="{$val}">
										</span>
										<div class="hidden">{$z++}</div>
										{/foreach}										
									</div>														
								</div>

						</div>
					</div>


			<div class="panel panel-default">
				<div class="panel-body">

						<div class="row">

								<div class="col-md-12" style="margin-bottom:10px;">
										<input class="form-control" type="text" name="device" id="device" placeholder="Device" autofocus="">
									</div>

								<div class="col-md-2">
									<h4 style="margin-top: 8px;"><i class="fa fa-pencil"></i> Customized</h4>
								</div>			
								<div class="col-md-1">
										<div class="design-image"></div>
								</div>	
								<div class="col-md-3">
								<select id="cloth_id" name="cloth_id" class="form-control drop-dn">
								<option value="">Select Product Type</option>
								{foreach $cloths as $cloth}
								<option value="{$cloth['id']}">{$cloth['name']}</option>
								{/foreach}
						  </select>					
								</div>
								<div class="col-md-3">
									<select id="design_id" class="form-control drop-dn" name="design_id">
										<option value="">Select Design</option>
										{*foreach $designs as $design}
										<option value="{$design['id']}">{$design['name']}</option>
										{/foreach*}
									</select>					
								</div>		
								<div class="col-md-3">
									<button id="get-customized-items" type="button" class="btn btn-block btn-primary"><i class="fa fa-pencil"></i> Customize</button>
								</div>						
							</div>

					<div class="table-responsive m-t">
						<table class="table invoice-table" id="invoice_items">
							<thead>
								<tr>
									<th width="5%">Item Image</th>
									<th width="40%">{$_L['Item Name']}</th>
									<th width="10%">{$_L['Qty']}</th>
									<th width="15%">{$_L['Price']}</th>
									<th width="15%">{$_L['Total']}</th>
								</tr>
							</thead>
							<tbody>{$idi = 0}
								{foreach $items as $item}
								    {if $item['item_type'] == 'product' OR $item['item_type'] == 'design'}
									<tr class="tr_clone"> 
										<td class="number">
										<!--{$item['itemcode']}-->
										{if !empty($item['item_img']) }
										<img data-img="ui/lib/imgs/invoice-contents/{$item['item_img']}" class="img-popup" src="ui/lib/imgs/invoice-contents/{$item['item_img']}" width="50px" height="50px">
											<input type="hidden" name="pimg[]" value="ui/lib/imgs/invoice-contents/{$item['item_img']}">
										{else}
										<input type="hidden" name="pimg[]" value="">
										-
										{/if}
										<input type="hidden" class="form-control item_id" name="i_id[]" value="{$item['id']}" id="i_id">
										<input type="hidden" class="form-control item_id" name="s_id[]" value="{$item['itemcode']}" id="s_id">
                                        <input type="hidden" class="form-control item_id" name="p_id[]" value="{if $item['item_type'] == 'design'}{$item['design_id']}{else}{$item['product_id']}{/if}" id="p_id">
										
										
                                    </td>
										<td><input type="text" class="form-control items" id="i_{$idi++}" name="desc[]" value="{$item['description']}" /> </td>
										<td><input type="number" class="form-control qty" value="{if ($_c['dec_point']) eq ','}{$item['qty']|replace:'.':','}{else}{$item['qty']}{/if}" name="qty[]"></td> 
										<td><input type="number" class="form-control item_price" name="amount[]" value="{if ($_c['dec_point']) eq ','}{$item['amount']|replace:'.':','}{else}{$item['amount']}{/if}"></td> 
										<td class="ltotal"><input type="number" class="form-control lvtotal" readonly="" name="total[]" value="{if ($_c['dec_point']) eq ','}{{$item['amount']*$item['qty']}|replace:'.':','}{else}{{$item['amount']*$item['qty']}}{/if}"></td>
										<td class="hide">
												<input type="hidden" name="item_type[]" value="{$item['item_type']}">
												
										</td> 
									</tr>
									{else}

									   {if $item['item_type'] == 'pocket'}
										   <script>
											   $('input[name="is_pocket"]').prop('checked', true);
										   </script>
									   {elseif $item['item_type'] == 'zip'}
									   <script>
											$('input[name="is_zip"]').prop('checked', true);
										</script>
									   {elseif $item['item_type'] == 'beading'}
									   <script>
											$('input[name="is_beading"]').prop('checked', true);
										</script>
									   {elseif $item['item_type'] == 'folding'}
									   <script>
											$('input[name="is_folding"]').prop('checked', true);
										</script>
									   {elseif $item['item_type'] == 'pico'}
									   <script>
											$('input[name="is_pico"]').prop('checked', true);
										</script>																														
									   {elseif $item['item_type'] == 'umbrella'}
									   {$size = str_replace('Umbrella (Size :', '', $item['description'])}
									   {$size = str_replace(')', '', $size)}
									   {$size = trim($size)}
									   <script>
											$('input[name="is_umbrella"]').prop('checked', true);
											$('input[name="umbrella_size"]').attr('value', '{$size}');
										</script>
									   {elseif $item['item_type'] == 'dupatta'}
									   {$size = str_replace('Dupatta (Size :', '', $item['description'])}
									   {$size = str_replace(')', '', $size)}
									   {$size = trim($size)}
									   <script>
											$('input[name="is_dupatta"]').prop('checked', true);
											$('input[name="dupatta_size"]').attr('value', '{$size}');
										</script>									   
									   {/if}

								    {/if}
								{/foreach}
								<input type="hidden" id="rowcount" value="{$idi}">
							</tbody>
            </table>
					</div>
					<!-- /table-responsive -->
					<div class="col-md-12">
						<div class="col-md-3">
							<button type="button" class="btn btn-primary" id="item-add"><i
												class="fa fa-search"></i> {$_L['Add Product OR Service']}</button>
						</div>
						{*<button type="button" class="btn btn-primary" id="item-add"><i
												class="fa fa-search"></i> {$_L['Add Product OR Service']}</button>*}
						<div class="col-md-3">						
							<button type="button" class="btn btn-danger" id="item-remove"><i
												class="fa fa-minus-circle"></i> {$_L['Delete']}</button>
						</div>
						<div class="col-md-3">						
						
						</div>
						<div class="col-md-3">
						<label for="add_discount"><a href="#" id="add_discount" class="btn btn-info pull-right" >
									<i class="fa fa-minus-circle"></i> {$_L['Set Discount']}</a>
							</label>
						</div>	
					</div>	
					<br>
					<br>
					<hr>
							<textarea class="form-control" name="notes" id="notes" rows="3" placeholder="{$_L['Invoice Terms']}...">{$i['notes']}</textarea>
					<br>
        </div>
      </div>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="text-right">
							<input type="hidden" name="iid" id="iid" value="{$i['id']}">
                                                        <input type="hidden" name="ciid" id="ciid" value="{$i['invoicenum']}">
							<input type="hidden" id="_dec_point" name="_dec_point" value="{$_c['dec_point']}">
							<!--<button class="btn btn-primary" id="submit" style="margin-bottom: 10px;"><i class="fa fa-save"></i> {$_L['Save Invoice']}</button>-->
							<button class="btn btn-info" id="save_n_close"><i class="fa fa-check"></i> {$_L['Save n Close']}</button>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
					<table class="table invoice-total">
						<tbody>
							<tr>
									<td><strong>{$_L['Sub Total']} :</strong></td>
									<td id="sub_total" class="amount">{$d['total']}</td>
							</tr>
					<tr>
									<td><strong>{$_L['Discount']} <span id="is_pt"></span> :</strong></td>
									<td id="discount_amount_total" class="amount">{$d['discount']}</td>
							</tr>
							<tr>
									<td><strong>{$_L['TAX']} :</strong></td>
									<td id="taxtotal" class="amount">{$d['taxamt']}</td>
							</tr>
							<tr>
									<td><strong>{$_L['TOTAL']} :</strong></td>
									<td name="total[]" id="total" class="amount">{$d['subtotal']}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
						<div class="form-group">
						<select class="form-control hide taxed selectpicker" name="taxed"> 
													<optgroup label="GST">
													{foreach $taxes as $tax} 
														{if $tax['taxtype'] eq ("GST")}
															<option value="{$tax['id']}" rate="{$tax['rate']}" {if $tax['id'] eq ($item['tax_id'])}selected="selected" {/if}>{$tax['name']} {$tax['rate']} %</option>
														{/if}
													{/foreach} 
													</optgroup>
													<optgroup label="IGST">
													{foreach $taxes as $tax} 
														{if $tax['taxtype'] eq ("IGST")}
															<option value="{$tax['id']}" rate="{$tax['rate']}" {if $tax['id'] eq ($item['tax_id'])}selected="selected" {/if}>{$tax['name']} {$tax['rate']} %</option>
														{/if}
													{/foreach} 
													</optgroup>
												</select>
												</div>
								<div class="form-group">
							<label for="company">{$_L['Company']}</label>
							{*<select id="company" name="company" class="form-control">
								<!--<option value="">{$_L['Select Company']}...</option>-->
								{foreach $comp as $cs}
								<option value="{$cs['id']}"
                					{if $d['company_id'] eq ($cs['id'])}selected="selected" {/if}>{$cs['account']}</option>
								{/foreach}
							</select>*}
							<select id="company" name="company" class="form-control" required>
								{if $user->roleid eq 0}
									{foreach $comp as $cs}
										<option value="{$cs.id}"
											data-branch="{$cs.id}"
											{if isset($d.company_id) && $d.company_id eq $cs.id}
												selected
											{elseif !isset($d.company_id) && $user->branch_id eq $cs.id}
												selected
											{/if}>
											{$cs.account}
										</option>
									{/foreach}
								{else}
									{foreach $comp as $cs}
										{if (isset($d.company_id) && $d.company_id eq $cs.id) || (!isset($d.company_id) && $user->branch_id eq $cs.id)}
											<option value="{$cs.id}"
												data-branch="{$cs.id}"
												selected>
												{$cs.account}
											</option>
										{/if}
									{/foreach}
								{/if}
							</select>
						</div>
						{*<div class="form-group">
							<label for="cid">{$_L['Customer']}</label>
							<select id="cid" name="cid" class="form-control">
									<option value="">{$_L['Select Contact']}...</option>
									{foreach $c as $cs}
											<option value="{$cs['id']}" {if $i['userid'] eq ($cs['id'])}selected="selected" {/if}>{$cs['account']} / {$cs['phone']} </option>
									{/foreach}
							</select>
							<!--<span class="help-block"><a href="#" id="contact_add">| {$_L['Or Add New Customer']}</a> </span>-->
						</div>*}
						<div class="form-group">
							<label for="cid">{$_L['Customer']}</label>
							<select id="cid" name="cid" class="form-control">
								{if $current_c}
									<option value="{$current_c.id}" selected="selected">
										{$current_c.account}
										{if $current_c.phone neq ''} - {$current_c.phone}{/if}
										{if $current_c.branch_alias neq ''} [{$current_c.branch_alias}]{/if}
									</option>
								{/if}
							</select>
						</div>
						<div class="form-group" style="display: none;">
							<label for="currency">{$_L['Currency']}</label>
							<select id="currency" name="currency" class="form-control">
									{foreach $currencies as $currency}
											<option value="{$currency['id']}" {if $i['currency'] eq ($currency['id'])}selected="selected" {/if}>{$currency['cname']}</option>
									{foreachelse}
											<option value="0">{$_c['home_currency']}</option>
									{/foreach}
							</select>
						</div>
						<div class="form-group hide">
								<label for="address">{$_L['Address']}</label>
								<textarea id="address" readonly class="form-control" rows="5"></textarea>
						</div>
						{*<div class="form-group">
								<label for="invoicenum">{$_L['Invoice Prefix']}</label>
								<input type="text" class="form-control" id="invoicenum" name="invoicenum" value="{$d['invoicenum']}">
						</div>*}
						<div class="form-group">
								<label for="cn">{$_L['Invoice']} #</label>
								<input type="text" class="form-control" id="cn" name="cn" value="{$d['cn']}">
								<span class="help-block">{$_L['invoice_number_help']}</span>
						</div>
						{if $i['r'] neq '0'}
							<div class="form-group">
								<label for="repeat">{$_L['Repeat Every']}</label>
								<select class="form-control" name="repeat" id="repeat">
									<option value="week1" {if $i['r'] eq '+1 week'} selected{/if}>{$_L['Week']}</option>
									<option value="weeks2" {if $i['r'] eq '+2 weeks'} selected{/if}>{$_L['Weeks_2']}</option>
									<option value="month1" {if $i['r'] eq '+1 month'} selected{/if}>{$_L['Month']}</option>
									<option value="months2" {if $i['r'] eq '+2 months'} selected{/if}>{$_L['Months_2']}</option>
									<option value="months3" {if $i['r'] eq '+3 months'} selected{/if}>{$_L['Months_3']}</option>
									<option value="months6" {if $i['r'] eq '+6 months'} selected{/if}>{$_L['Months_6']}</option>
									<option value="year1" {if $i['r'] eq '+1 year'} selected{/if}>{$_L['Year']}</option>
									<option value="years2" {if $i['r'] eq '+2 years'} selected{/if}>{$_L['Years_2']}</option>
									<option value="years3" {if $i['r'] eq '+3 years'} selected{/if}>{$_L['Years_3']}</option>
								</select>
							</div>
						{else}
								<input type="hidden" name="repeat" id="repeat" value="0">
						{/if}

						<div class="form-group">
							<label for="idate">{$_L['Invoice Date']}</label>
							<input type="text" class="form-control" id="idate" name="idate" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" value="{$idate}">
						</div>
						<div class="form-group">
							<label for="duedate">Delivery Date</label>
							<input type="text" class="form-control" id="duedate" name="duedate" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" value="{$i['duedate']}">
						</div>
						{*<div class="form-group">
							<label for="tid">{$_L['Sales TAX']}</label>
							<select id="tid" name="tid" class="form-control">
								<option value="">None</option>
								{foreach $t as $ts}
									<option value="{$ts['id']}" {if $ts['name'] eq $i['taxname']}selected="selected" {/if} >{$ts['name']}
											({{number_format($ts['rate'],2,$_c['dec_point'],$_c['thousands_sep'])}} %)</option>
								{/foreach}
							</select>
							<input type="hidden" id="stax" name="stax" value="{$i['taxrate']}">*}
							<input type="hidden" id="discount_amount" name="discount_amount" value="{$i['discount_value']}">
							<input type="hidden" id="discount_type" name="discount_type" value="{$i['discount_type']}">
							{*<input type="hidden" id="taxed_type" name="taxed_type" value="individual">
						</div>*}
						
          
        </div>
      </div>
    </div>
  </form>
</div>

{* lan variables *}

<input type="hidden" id="_lan_set_discount" value="{$_L['Set Discount']}">
<input type="hidden" id="_lan_discount" value="{$_L['Discount']}">
<input type="hidden" id="_lan_discount_type" value="{$_L['Discount Type']}">
<input type="hidden" id="_lan_percentage" value="{$_L['Percentage']}">
<input type="hidden" id="_lan_fixed_amount" value="{$_L['Fixed Amount']}">
<input type="hidden" id="_lan_btn_save" value="{$_L['Save']}">
<input type="hidden" id="_lan_no_results_found" value="{$_L['No results found']}">

{include file="sections/footer.tpl"}


<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
