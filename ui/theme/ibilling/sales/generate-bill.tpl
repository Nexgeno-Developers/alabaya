<script src="ui/lib/dp/dist/datepicker.min.js"></script>
<link href="ui/lib/dp/dist/datepicker.min.css" rel="stylesheet">
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Generate Bill For <b>{SaleID($sale['id'])}</b></h3>
</div>
<div class="modal-body">
	<!--<div class="wrapper wrapper-content">-->
	<div class="row">
		<div class="col-md-12">
			<div class="">
				<div class="" id="">
					<div class="alert alert-danger" id="emsg" style="display:none;">
						<span id="emsgbody"></span>
					</div>
					<div class="alert alert-success" id="emsg-success" style="display:none;">
						<span id="emsgbody-success"></span>
					</div>
					<form class="form-horizontal" id="generate-bill-form">
						<div class="row">
							<input type="hidden" value="{$sale['id']}" name="sale_id">

							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label" for="service_id">{$_L['Services']}
										<small class="red">*</small>
									</label>
									<div class="col-lg-8">
										<select id="rental_service" name="service_id" class="form-control" required>
											<option value="">Select Services</option>
											{foreach $services as $service}
											<option value="{$service['id']}" {if $sale[ 'service_id'] eq $service[ 'id']}selected="selected" {/if}>{$service['name']}</option>
											{/foreach}
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label" for="duration">Select Plan
										<small class="red">*</small>
									</label>
									<div class="col-lg-8">
										<select id="duration" name="duration" class="form-control" required>
											<option value="">Plan & Packages</option>
											<option value="1_month" {if $dur eq '1_month'}selected="selected" {/if}>1 Month</option>
											<option value="3_month" {if $dur eq '3_month'}selected="selected" {/if}>3 Months</option>
											<option value="6_month" {if $dur eq '6_month'}selected="selected" {/if}>6 Months</option>
											<option value="1_year" {if $dur eq '1_year'}selected="selected" {/if}>1 Year</option>
											<option value="2_year" {if $dur eq '2_year'}selected="selected" {/if}>2 Year</option>
											<option value="3_year" {if $dur eq '3_year'}selected="selected" {/if}>3 Year</option>
											<option value="4_year" {if $dur eq '4_year'}selected="selected" {/if}>4 Year</option>
											<option value="5_year" {if $dur eq '5_year'}selected="selected" {/if}>5 Year</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label">Qty / Amt
										<small class="red">*</small>
									</label>
									<div class="col-lg-4">
										<input placeholder="Quantity" type="number" name="qty" class="form-control" value="1">
									</div>
									<div class="col-lg-4">
										<input placeholder="Amount" type="number" name="amount" class="form-control" value="{$sale['amount']}">
									</div>									
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label">
										Total Amount :
									</label>
                                    <div class="total-amt" style="padding-top: 4px;font-size: 18px;">{$sale['amount']}</div>
								</div>
							</div>
							<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<label class="col-md-2 control-label" for="note">Description</label>
									<div class="col-md-10">
										<textarea id="notes" name="description" class="form-control" rows="5"></textarea>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<label class="col-md-2 control-label" for="update_date">Update/Bill Genr. Date
										<small class="red">*</small>
									</label>
									<div class="col-lg-10">
										<input type="text" class="form-control" id="update_date" name="update_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true"
										 value="{date('Y-m-d')}" readonly>
									</div>
								</div>
							</div>							
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!--</div>-->
</div>
<div class="modal-footer">
	<button id="generate_bill" class="btn btn-primary">Generate</button>

	<button type="button" data-dismiss="modal" class="btn">Close</button>
</div>
<link rel="stylesheet" type="text/css" href="{$app_url}ui/lib/redactor/redactor.css" />
<script type="text/javascript" src="{$app_url}ui/lib/redactor/redactor.min.js"></script>
<script>
	jQuery(document).ready(function($) {
		$(".select2").select2();

		$('#notes').redactor({
			minHeight: 200, // pixels
			plugins: ['fontcolor']
		});

		$('body').on('keyup', '#generate-bill-form input[name="qty"], #generate-bill-form input[name="amount"]', function(){
             const amount = $('#generate-bill-form input[name="amount"]').val();
			 const qty    = $('#generate-bill-form input[name="qty"]').val();
			 console.log(amount);
			 console.log(qty);
			 $('#generate-bill-form .total-amt').html(qty*amount);
		});
	});
</script>