{include file="sections/header.tpl"}
<div class="row">
  <div class="widget-1 col-md-6 col-sm-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{$_L['Edit_Account']}</h3>
      </div>
      <div class="panel-body">
        <form role="form" name="accadd" method="post" enctype="multipart/form-data" action="{$_url}accounts/edit-post">
					<div class="ibox-content">
						{if $d->company_logo neq ''}
						<img class="logo"  src="{$app_url}application/storage/system/{$d->company_logo}" alt="Logo">
							<br><br>
						{/if}	
						<div class="form-group">
								<label for="file">Upload New Logo</label>
								<input type="file" id="file" name="file">
						</div>
					</div>
					<div class="form-group">
							<label for="account">{$_L['Company']} {$_L['Title']}*</label>
							<input type="text" class="form-control" id="account" name="account" value="{$d->account}">
					</div>
					<div class="form-group">
							<label for="description">{$_L['Description']}</label>
							<input type="text" class="form-control" id="description" name="description" value="{$d->description}">
					</div>
					<div class="form-group">
							<label for="contact_person">{$_L['Contact Person']}</label>
							<input type="text" class="form-control" id="contact_person" name="contact_person" value="{$d->contact_person}">
					</div>
					<div class="form-group">
							<label for="contact_phone">{$_L['Phone']}</label>
							<input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{$d->contact_phone}">
					</div>
					<div class="form-group">
							<label for="email">{$_L['Email']}</label>
							<input type="text" class="form-control" id="email" name="email" value="{$d->email}">
					</div>
					<div class="form-group">
							<label for="caddress">{$_L['Address']}</label>
							<textarea class="form-control" id="address" name="address" rows="3">{$d->address}</textarea>
							<span class="help-block">{$_L['Type address as you want to print in invoice']}.</span>
							<span class="help-block">{$_L['You can use html tag']}</span>
					</div>
					<div class="form-group">
						<label class="control-label" for="pan">{$_L['pan']}</label>
						<input type="text" id="pan" name="pan" class="form-control" value="{$d->pan}">
					</div>
					<div class="form-group">
						<label class="control-label" for="gst">{$_L['gst']}</label>
						<input type="text" id="gst" name="gst" class="form-control" value="{$d->gstin}">
					</div>					<div class="form-group">								<label class="control-label" for="bank_name">Bank Name</label>								<input type="text" id="bank_name" name="bank_name" class="form-control" value="{$d->bank_name}">						</div>						<div class="form-group">								<label class="control-label" for="account_number">Account Number</label>								<input type="text" id="account_number" name="account_number" class="form-control" value="{$d->account_number}">						</div>						<div class="form-group">								<label class="control-label" for="account_type">Account Type</label>								<input type="text" id="account_type" name="account_type" class="form-control" value="{$d->account_type}">						</div>						<div class="form-group">								<label class="control-label" for="ifsc_code">IFSC Code</label>								<input type="text" id="ifsc_code" name="ifsc_code" class="form-control" value="{$d->ifsc}">						</div>
						
					<input type="hidden" name="id" value="{$d['id']}">
					<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {$_L['Submit']}</button>
        </form>
      </div>
    </div>
  </div>
</div>

{include file="sections/footer.tpl"}