<script src="ui/lib/dp/dist/datepicker.min.js"></script>
<link href="ui/lib/dp/dist/datepicker.min.css" rel="stylesheet">
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Edit Sale</h3>
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
					<form class="form-horizontal" id="edit-sale-form">
						<div class="row">
                            <input type="hidden" value="{$sale['id']}" name="sale_id">
							<div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label class="col-md-4 control-label" for="agent_id">Agent
										<small class="red">*</small>
									</label>
									<div class="col-md-8">
										<select id="agent_id" name="agent_id" class="form-control select2" required>
											<option value="">Select Agent...</option>
											{foreach $agent as $ag}
											<option value="{$ag['id']}" {if $sale[ 'agent_id'] eq $ag[ 'id']}selected="selected" {/if}>{$ag['fullname']}</option>
											{/foreach}
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label class="col-md-4 control-label" for="customer_id">{$_L['Customer']}
										<small class="red">*</small>
									</label>
									<div class="col-md-8">
										<select id="customer_id" name="customer_id" class="form-control select2" required>
											<option value="">{$_L['Select Contact']}...</option>
											{foreach $c as $cs}
											<option value="{$cs['id']}" {if $sale[ 'customer_id'] eq $cs[ 'id']}selected="selected" {/if}>{$cs['account']} -- {$cs['company']} -- {$cs['email']}</option>
											{/foreach}
										</select>
									</div>
								</div>
							</div>
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
									<label class="col-md-4 control-label" for="domain_name">Domain Name
									</label>
									<div class="col-lg-8">
										<input type="text" id="domain_name" name="domain_name" class="form-control" value="{$sale['domain']}">
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label" for="amount">Amount
										<small class="red">*</small>
									</label>
									<div class="col-lg-8">
										<input type="number" id="amount" name="amount" class="form-control" value="{$sale['amount']}">
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12" id="ren_service_registered">
								<div class="form-group">
									<label class="col-md-4 control-label" for="register_date">Registration Date
										<small class="red">*</small>
									</label>
									<div class="col-lg-8">
										<input type="text" class="form-control" id="register_date" name="register_date" datepicker data-date-format="yyyy-mm-dd"
										 data-auto-close="true" value="{$sale['ragister_date']}">
									</div>
								</div>
							</div>
							<!--<div class="col-md-6 col-sm-12" id="ren_service_renewal">
								<div class="form-group">
									<label class="col-md-4 control-label" for="update_date">Update Date
										<small class="red">*</small>
									</label>
									<div class="col-lg-8">
										<input type="text" class="form-control" id="update_date" name="update_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true"
										 value="{$sale['update_date']}">
									</div>
								</div>
							</div>-->
							<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<label class="col-md-2 control-label" for="note">Note</label>
									<div class="col-md-10">
										<textarea name="note" class="form-control" rows="5">{$sale['note']}</textarea>
									</div>
								</div>
							</div>
						</div>
						<!--<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<div class="col-md-offset-2 col-lg-10">
											<button class="md-btn md-btn-primary waves-effect waves-light" type="submit" id="submit_sale">
												<i class="fa fa-check"></i> {$_L['Save']}</button> |
											<a href="{$_url}sales/list/">{$_L['Or Cancel']}</a>
										</div>
									</div>
								</div>
							</div>-->
					</form>
				</div>
			</div>
		</div>
	</div>
	<!--</div>-->
</div>
<div class="modal-footer">
	<button id="edit_sale" class="btn btn-primary">Update</button>

	<button type="button" data-dismiss="modal" class="btn">Close</button>
</div>

<script>
	jQuery(document).ready(function($) {
		$(".select2").select2();
	});
</script>