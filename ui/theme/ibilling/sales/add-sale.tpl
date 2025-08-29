{include file="sections/header.tpl"}
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Add Sale</h5>
				</div>
				<div class="ibox-content" id="ibox_form">
					<div class="alert alert-danger" id="emsg">
						<span id="emsgbody"></span>
					</div>
					<form class="form-horizontal" id="rform">
						<div class="row">
            <div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label class="col-md-4 control-label" for="agent_id">Agent <small class="red">*</small></label>
									<div class="col-lg-8">
										<select id="agent_id" name="agent_id" class="form-control select2" required>
											<option value="">Select Agent...</option>
											{foreach $agent as $ag}
											<option value="{$ag['id']}">{$ag['fullname']}</option>
											{/foreach}
										</select>
									</div>
								</div>
							</div>            
							<div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label class="col-md-4 control-label" for="customer_id">{$_L['Customer']} <small class="red">*</small></label>
									<div class="col-lg-8">
										<select id="customer_id" name="customer_id" class="form-control select2" required>
											<option value="">{$_L['Select Contact']}...</option>
											{foreach $c as $cs}
											<option value="{$cs['id']}">{$cs['account']} / {$cs['company']} {if $cs['email'] neq ''} / {$cs['email']}{/if}</option>
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
											<option value="{$service['id']}">{$service['name']}</option>
											{/foreach}
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label" for="duration">Select Plan <small class="red">*</small></label>
									<div class="col-lg-8">
										<select id="duration" name="duration" class="form-control" required>
											<option value="">Plan & Packages</option>			
											<option value="1_month">1 Month</option>
                      <option value="3_month">3 Months</option>
                      <option value="6_month">6 Months</option>                     
                      <option value="1_year">1 Year</option>
                      <option value="2_year">2 Year</option>
                      <option value="3_year">3 Year</option>
                      <option value="4_year">4 Year</option>
                      <option value="5_year">5 Year</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label" for="domain_name">Domain Name										
									</label>
									<div class="col-lg-5">
										<input type="text" id="domain_name" name="domain_name" class="form-control" autofocus>
									</div>
									<div class="col-lg-3">
									   <button type="button" class="btn btn-warning btn-block" onclick="checkWhoIs();">WhoIs</button>
									</div>									
								</div>
							</div>              
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label class="col-md-4 control-label" for="amount">Amount <small class="red">*</small></label>
									<div class="col-lg-8">
										<input type="number" id="amount" name="amount" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12" id="ren_service_registered">
								<div class="form-group">
									<label class="col-md-4 control-label" for="register_date">Registration Date <small class="red">*</small></label>
									<div class="col-lg-8">
										<input type="text" class="form-control" id="register_date" name="register_date" datepicker data-date-format="yyyy-mm-dd"
										 data-auto-close="true" value="{$idate}">
									</div>
								</div>
							</div>
							<!--<div class="col-md-6 col-sm-12" id="ren_service_renewal">
								<div class="form-group">
									<label class="col-md-4 control-label" for="update_date">Update Date <small class="red">*</small></label>
									<div class="col-lg-8">
										<input type="text" class="form-control" id="update_date" name="update_date" datepicker data-date-format="yyyy-mm-dd"
										 data-auto-close="true" value="{$idate}">
									</div>
								</div>
							</div>-->
							<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<label class="col-md-2 control-label" for="note">Note</label>
									<div class="col-md-10">
										<textarea name="note" class="form-control" rows="5"></textarea>
									</div>
								</div>
							</div>                            
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="col-md-offset-2 col-lg-10">
										<button class="md-btn md-btn-primary waves-effect waves-light" type="submit" id="submit_sale">
											<i class="fa fa-check"></i> {$_L['Save']}</button> |
										<a href="{$_url}sales/list/">{$_L['Or Cancel']}</a>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
{include file="sections/footer.tpl"}
<script>
	jQuery(document).ready(function($) {
		$(".select2").select2();
	});

	function checkWhoIs()
	{
		const domain = $('#domain_name').val();
		if(domain == '' || domain == 'undefined')
		{
            alert('Domain name cannot be empty!');
		}
		else
		{
			window.open( 'https://www.whois.com/whois/' + domain, '_blank');
		}
		
	}
</script>