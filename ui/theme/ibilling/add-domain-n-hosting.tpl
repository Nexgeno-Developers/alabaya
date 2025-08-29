{include file="sections/header.tpl"}
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-md-12">
      <div class="ibox float-e-margins">
        <div class="ibox-title">
          <h5>Add Domain and Hosting</h5>
        </div>
        <div class="ibox-content" id="ibox_form">
          <div class="alert alert-danger" id="emsg">
            <span id="emsgbody"></span>
          </div>
          <form class="form-horizontal" id="rform">
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="form-group">
                  <label class="col-md-2 control-label" for="cid">{$_L['Customer']}</label>
                  <div class="col-lg-8">
                    <select id="cid" name="cid" class="form-control select2" required>
                      <option value="">{$_L['Select Contact']}...</option>
                      {foreach $c as $cs}
                      <option value="{$cs['id']}">{$cs['account']} -- {$cs['company']} -- {$cs['email']}</option>
                      {/foreach}
                    </select>
                  </div>
                  <div class="col-lg-2">
                    <span class="help-block"><a href="#" id="contact_add">Add New</a> </span>
                  </div>
                </div>
							</div>
							<div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label class="col-md-4 control-label" for="service">{$_L['Services']}</label>
                  <div class="col-lg-8">
                    <select id="rental_service" name="service" class="form-control" required>
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
                  <label class="col-md-4 control-label" for="domain_name">Domain Name<small class="red">*</small> </label>
                  <div class="col-lg-8"><input type="text" id="domain_name" name="domain_name" class="form-control" autofocus>
                  </div>
                </div>
							</div>
							<div class="col-md-6 col-sm-12 cstm-hide" id="ren_domain_plan">
                <div class="form-group">
                  <label class="col-md-4 control-label" for="plan">Select Plan</label>
                  <div class="col-lg-8">
										<select id="plan" name="domain_host_plan" class="form-control" required>
                      <option value="">Plan & Packages</option>
                      <option value="1">1 Year</option>
											{for $i=2 ; $i<=10 ; $i++ }
                      <option value="{$i}">{$i} Years</option>
											{/for}
                    </select>
                  </div>
                </div>
							</div>
							<div class="col-md-6 col-sm-12 cstm-hide" id="ren_service_plan">
                <div class="form-group">
                  <label class="col-md-4 control-label" for="service_plan">Select Plan</label>
                  <div class="col-lg-8">
										<select id="service_plan" name="service_plan" class="form-control" required>
                      <option value="">Plan & Packages</option>
                      <option value="12">1 Year</option>
                      <option value="6">6 Months</option>
                      <option value="1">1 Month</option>
                    </select>
                  </div>
                </div>
							</div>
							<div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label class="col-md-4 control-label" for="amount">Amount</label>
                  <div class="col-lg-8"><input type="number" id="amount" name="amount" class="form-control">
                  </div>
                </div>
							</div>
							<div class="col-md-6 col-sm-12" id="ren_service_registered">
                <div class="form-group">
                  <label class="col-md-4 control-label" for="register_date">Registration Date</label>
                  <div class="col-lg-8"><input type="text" class="form-control" id="register_date" name="register_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" value="{$idate}">
                  </div>
                </div>
							</div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="col-md-offset-2 col-lg-10">
                    <button class="md-btn md-btn-primary waves-effect waves-light" type="submit_domain" id="submit_domain"><i class="fa fa-check"></i> {$_L['Save']}</button> | <a href="{$_url}domain_n_hosting/list/">{$_L['Or Cancel']}</a>
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
</script>
