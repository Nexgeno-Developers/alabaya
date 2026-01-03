{include file="sections/header.tpl"}
<style>
.alert-dismissable .close, .alert-dismissible .close {
    top: -8px;
    right: 0px;
}
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0; 
}
.pad-2 {
    padding: 2px;
}
</style>

<div class="row" id="ibox_form">
  <form id="invform" method="post" enctype="multipart/form-data" parsley-validate novalidate>
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissable" id="emsg" >
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
                <h5><i class="fa fa-user" aria-hidden="true"></i> Customer Information <span id="already_cust">(New)</span></h5>


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
						<div class="col-md-12 col-sm-12 col-xs-12 col-12">
							<h3 style="margin-top: 25px; "><i class="fa fa-tachometer" aria-hidden="true"></i> Measurements - </h3>
						</div>
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="length">Length</label>
								<input type="text" class="form-control pad-2" name="cust_length">
							</div>
						</div>
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="shoulder">Shoulder</label>
								<input type="text" class="form-control pad-2" name="cust_shoulder">
							</div>
						</div>	
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="sleeves">Sleeves</label>
								<input type="text" class="form-control pad-2" name="cust_sleeves">
							</div>
						</div>
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="armole">Armole</label>
								<input type="text" class="form-control pad-2" name="cust_armole">
							</div>
						</div>
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="cuff">Cuff</label>
								<input type="text" class="form-control pad-2" name="cust_cuff">
							</div>
						</div>
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="chest">Chest</label>
								<input type="text" class="form-control pad-2" name="cust_chest">
							</div>
						</div>
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="waist">Waist</label>
								<input type="text" class="form-control pad-2" name="cust_waist">
							</div>
						</div>
						<div class="col-md-1 col-sm-6 col-xs-12 col_with_9 col-measurement">
							<div class="form-group">
								<label for="hipps">Hipps</label>
								<input type="text" class="form-control pad-2" name="cust_hipps">
							</div>
						</div>					
					</div>
					<div class="row">
							<div class="col-md-12">
								<h3><i class="fa fa-user" aria-hidden="true"></i> Other - </h3>
							</div>						
							<div class="col-md-12" style="display: flex; flex-wrap: wrap; align-items: baseline;">
								<div class="margin-right-10px">
									<input type="checkbox" name="is_pocket" value="yes" id="is_pocket"> 
									<label class="pd_15" for="is_pocket">Pocket</label>
								</div>
								
								<div class="margin-right-10px">
									<input type="checkbox" name="is_zip" value="yes" id="is_zip"> 
									<label class="pd_15" for="is_zip">Zip</label>
								</div>

								<div class="margin-right-10px">
									<input type="checkbox" name="is_beading" value="yes" id="is_beading"> 
									<label class="pd_15" for="is_beading">Beading</label>	
								</div>
								
								<div class="margin-right-10px">
									<input type="checkbox" name="is_folding" value="yes" id="is_folding"> 
									<label class="pd_15" for="is_folding">Folding</label>
								</div>
								
								<div class="margin-right-10px">
									<input type="checkbox" name="is_pico" value="yes" id="is_pico"> 
									<label class="pd_15" for="is_pico">Pico</label>
								</div>
								
								<div class="margin-right-10px">
									<input type="checkbox" name="is_umbrella" value="yes"> 
									<label class="pd_10" for="umbrella_size">Umbrella</label>
									<input style="width:100px;display: inline;" class="form-control invoice-size_width" type="text" name="umbrella_size" placeholder="Size" id="umbrella_size">	
								</div>

								<div class="margin-right-10px">
									<input type="checkbox" name="is_dupatta" value="yes"> 
									<label class="pd_10" for="dupatta_size">Dupatta</label>
									<input style="width:100px;display: inline;" class="form-control invoice-size_width" type="text" name="dupatta_size" placeholder="Size" id="dupatta_size">
								</div>
							</div>
							<div class="col-md-12 "style="margin-top: 12px; margin-bottom: 6px;">
								 <h3>Display Measurement</h3>
								 <input type="radio" id="age1" name="d_measure" value="yes" checked>
								 <label for="age1">Yes</label>
								 <input type="radio" id="age2" name="d_measure" value="no">
								 <label for="age2">No</label>
							</div>						
					</div>
					<div class="row" >
						<div class="col-md-12 file-upload-main" style="display: flex; justify-content:start; align-items-center; flex-wrap: wrap;">
							<div class="">
								<h3><i class="fa fa-user" aria-hidden="true"></i> File Upload - </h3>
							</div>
							<div class="">
								<input type="file" accept="image/*" name="additional_imgs[]" class="form-control" multiple>
							</div>	
						</div>					
					</div>
				</div>
			</div>

			<div class="panel panel-default">
        <div class="panel-body">
			<div class="row">

			    <div class="col-md-12" style="margin-bottom:10px;">
					<input class="form-control" type="text" name="device" id="device" placeholder="Device" autofocus>
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
									<th width="15%">Item Image</th>
									<th width="40%">{$_L['Item Name']}</th>
									<th width="10%">{$_L['Qty']}</th>
									<th width="15%">{$_L['Price']}</th>
									<th width="15%">{$_L['Total']}</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
          </div>
					<!-- /table-responsive -->
					<div class="col-md-12">
						<div class="col-md-3">
							<button type="button" class="btn btn-primary" id="item-add"><i
												class="fa fa-search"></i> Add Product {*$_L['Add Product OR Service']*}</button>
						</div>
						{*<button type="button" class="btn btn-primary" id="item-add"><i
												class="fa fa-search"></i> {$_L['Add Product OR Service']}</button>*}
						<div class="col-md-3">						
							<!-- <button type="button" class="btn btn-danger" id="item-remove"><i
												class="fa fa-minus-circle"></i> {$_L['Delete']}</button>-->
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
							<textarea class="form-control" name="notes" id="notes" rows="3"
												placeholder="{$_L['Invoice Terms']}...">{$_c['invoice_terms']}</textarea>
					<br>
					{if $recurring}
							<input type="hidden" id="is_recurring" value="yes">
					{else}
							<input type="hidden" id="is_recurring" value="no">
					{/if}
					
				</div>
      </div>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="text-right">
						<input type="hidden" id="_dec_point" name="_dec_point" value="{$_c['dec_point']}">
						<!--<button class="btn btn-primary" id="submit" style="margin-bottom: 10px;"><i class="fa fa-save"></i> {$_L['Save Invoice']}</button>-->
						<!--<button class="btn btn-info" id="save_n_close"><i class="fa fa-check"></i> {$_L['Save n Close']}</button>-->
						<button class="btn btn-info" id="save_n_close"><i class="fa fa-check"></i> Save & Print</button>
					</div>
				</div>
      </div>
			<div class="panel panel-default">
				<div class="panel-body">
					<table class="table invoice-total" id="me">
						<tbody>
							<tr>
								<td><strong>{$_L['Sub Total']} :</strong></td>
								<td id="sub_total" name="sub_total" class="amount">0.00</td>
							</tr>
							<tr>
									<td><strong>{$_L['Discount']} <span id="is_pt"></span> :</strong></td>
									<td id="discount_amount_total" class="amount">0.00</td>
							</tr>
							<tr>
									<td><strong>{$_L['TAX']} :</strong></td>
									<td id="taxtotal" class="amount">0.00</td>
							</tr>
							<tr>
									<td><strong>{$_L['TOTAL']} :</strong></td>
									<td name="total[]" id="total" class="amount">0.00</td>
							</tr>
						</tbody>
					</table>
				</div>
      </div>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="form-group hide">
								<label for="company">Tax</label>
								<select class="form-control taxed selectpicker" name="taxed"> 
												{getTax_opt()}
											</select>
						<!--<label for="company">{$_L['Company']}</label>
						<select id="company" name="company" class="form-control" required>
							<option value="">{$_L['Select Company']}...</option>
							{foreach $comp as $cs}
							<option value="{$cs['id']}" {if $cs['id'] eq (2)}selected="selected" {/if} >{$cs['account']}</option>
							{/foreach}
						</select>-->
					</div>
					
					<div class="form-group">
						<label for="company">{$_L['Company']}</label>
						{*<select id="company" name="company" class="form-control" required>
							<!--<option value="">{$_L['Select Company']}...</option>-->
							{foreach $comp as $cs}
							<option value="{$cs['id']}" {if $cs['id'] eq (2)}selected="selected" {/if} >{$cs['account']}</option>
							{/foreach}
						</select>*}
						<select id="company" name="company" class="form-control" required>
							{if $user->roleid eq 0}
								{foreach $comp as $cs}
									<option value="{$cs['id']}" data-branch="{$cs['id']}" {if $cs.id eq $user.branch_id}selected{/if}>
										{$cs['account']}
									</option>
								{/foreach}
							{else}
								{foreach $comp as $cs}
									{if $cs['id'] eq $user->branch_id}
										<option value="{$cs['id']}" 
												data-branch="{$cs['id']}" 
												{if $cs.id eq $user.branch_id}selected{/if}>
											{$cs['account']}
										</option>
									{/if}
								{/foreach}
							{/if}
						</select>
											
						<!-- hidden branch id -->
						<input type="hidden" name="company_branch_id" id="company_branch_id" value="">
					</div>		
					
					<div class="form-group">
						<label for="advance_amount">Advance Amount</label>
						<input type="text" pattern="[0-9]+" class="form-control" name="advance_amount" id="advance_amount">
					</div>
					<div class="form-group">
						<label for="advance_method">Advance Method</label>
						<select class="form-control" name="advance_method" id="advance_method">
							<option value="Cash">Cash</option>
							<option value="QR">QR</option>
						</select>
					</div>					
					<div class="form-group">
						<label for="cid">{$_L['Customer']}</label>
						<select id="cid" name="cid" class="form-control">
							<option value="">{$_L['Select Contact']}...</option>
						</select>
						<!--<span class="help-block"><a href="#" id="contact_add">| {$_L['Or Add New Customer']}</a> </span>-->
					</div>
					<div class="form-group hide">
						<label for="currency">{$_L['Currency']}</label>
						<select id="currency" name="currency" class="form-control">
							{foreach $currencies as $currency}
									<option value="{$currency['id']}"
													{if $_c['home_currency'] eq ($currency['cname'])}selected="selected" {/if}>{$currency['cname']}</option>
									{foreachelse}
									<option value="0">{$_c['home_currency']}</option>
							{/foreach}
						</select>
					</div>
					{$extra_fields}
					<div class="form-group hide">
							<label for="address">{$_L['Address']}</label>
							<textarea id="address" readonly class="form-control" rows="5"></textarea>
					</div>
					{*<div class="form-group">
							<label for="invoicenum">{$_L['Invoice Prefix']}</label>
							<input type="text" class="form-control" id="invoicenum" name="invoicenum">
					</div>*}
					<div class="form-group">
							<label for="cn">{$_L['Invoice']} #</label>
							<input type="text" class="form-control" id="cn" name="cn">
							<span class="help-block">{$_L['invoice_number_help']}</span>
					</div>
					{if $recurring}
					<div class="form-group">
						<label for="repeat">{$_L['Repeat Every']}</label>

						<select class="form-control" name="repeat" id="repeat">
							<option value="week1">{$_L['Week']}</option>
							<option value="weeks2">{$_L['Weeks_2']}</option>
							<option value="month1" selected>{$_L['Month']}</option>
							<option value="months2">{$_L['Months_2']}</option>
							<option value="months3">{$_L['Months_3']}</option>
							<option value="months6">{$_L['Months_6']}</option>
							<option value="year1">{$_L['Year']}</option>
							<option value="years2">{$_L['Years_2']}</option>
							<option value="years3">{$_L['Years_3']}</option>
						</select>
					</div>
					{else}
							<input type="hidden" name="repeat" id="repeat" value="0">
					{/if}

					<div class="form-group">
						<label for="idate">{$_L['Invoice Date']}</label>
						<input type="text" class="form-control" id="idate" name="idate" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" value="{$idate}">
					</div>
					<div class="form-group hide">
						<label for="duedate">Delivery Date</label>
						<select class="form-control" name="duedate1" id="duedate">
								<option value="due_on_receipt" selected>{$_L['Due On Receipt']}</option>
								<option value="days3">{$_L['days_3']}</option>
								<option value="days5">{$_L['days_5']}</option>
								<option value="days7">{$_L['days_7']}</option>
								<option value="days10">{$_L['days_10']}</option>
								<option value="days15">{$_L['days_15']}</option>
								<option value="days30">{$_L['days_30']}</option>
								<option value="days45">{$_L['days_45']}</option>
								<option value="days60">{$_L['days_60']}</option>
						</select>
					</div>
					<div class="form-group">
						<label for="duedate">Delivery Date</label>
						<input type="text" class="form-control" id="duedate" name="duedate" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" value="{$idate}">
					</div>					
					<div class="form-group">
							{*<input type="hidden" id="stax" name="stax" value="0.00">*}
							<input type="hidden" id="discount_amount" name="discount_amount" value="">
							<input type="hidden" id="discount_type" name="discount_type" value="p">
							{*<input type="hidden" id="taxed_type" name="taxed_type" value="individual">*}
					</div>
					
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


<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">

{include file="sections/footer.tpl"}
