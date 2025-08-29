{include file="sections/header.tpl"}
<script>
$(document).on('change','#file,#stamp' , function(){ CheckFileName(); });	
function CheckFileName() {     
   var fileName = document.getElementById("file").value;		
	 var ext=fileName.split(".")[1].toUpperCase();  
	 if (fileName == "") {           
	 alert("Browse to upload a valid Image File");         
   return false;      
	 }     
   else if (ext == "PNG" || ext == "JPEG" || ext == "JPG" || ext == "GIF")          
	 return true;      
	 else {          
	 alert("File with " + fileName.split(".")[1] + " is invalid. Upload a validfile with png, jpeg, gif extensions");						document.getElementById("file").value=null;      
	 return false;     
   }   
	 return true;  
	 }
	 </script>
<div class="row">
  <div class="col-md-6">
    <div class="ibox float-e-margins">
			<div class="ibox-title">
					<h5>{$_L['Add_New_Company']}</h5>
			</div>
			<div class="ibox-content">
				<form role="form" name="accadd" method="post" enctype="multipart/form-data" action="{$_url}accounts/add-post">
						<div class="form-group">
								<label for="account">{$_L['Company']} {$_L['Title']}*</label>
								<input type="text" class="form-control" id="account" name="account">
						</div>
						<div class="form-group">
								<label for="description">{$_L['Description']}</label>
								<input type="text" class="form-control" id="description" name="description">
						</div>
						<div class="form-group">
								<label for="balance">{$_L['Initial Balance']}</label>
								<input type="text" class="form-control amount" id="balance" name="balance" data-a-sign="{$_c['currency_code']} "  data-a-dec="{$_c['dec_point']}" data-a-sep="{$_c['thousands_sep']}" data-d-group="2">
						</div>
						
						<div class="form-group">
								<label for="contact_person">{$_L['Contact Person']}</label>
								<input type="text" class="form-control" id="contact_person" name="contact_person">
						</div>
						<div class="form-group">
								<label for="contact_phone">{$_L['Phone']}</label>
								<input type="text" class="form-control" id="contact_phone" name="contact_phone">
						</div>
						<div class="form-group">
								<label for="email">{$_L['Email']}</label>
								<input type="text" class="form-control" id="email" name="email">
						</div>
						<div class="form-group">
                <label for="address">{$_L['Address']}</label>
                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
								<span class="help-block">{$_L['Type address as you want to print in invoice']}</span>
								<span class="help-block">{$_L['You can use html tag']}</span>
						</div>
						<div class="form-group">
								<label class="control-label" for="pan">{$_L['pan']}</label>
								<input type="text" id="pan" name="pan" class="form-control">
						</div>
						<div class="form-group">
								<label class="control-label" for="gst">{$_L['gst']}</label>
								<input type="text" id="gst" name="gst" class="form-control">
						</div>
						<div class="form-group">
								<label class="control-label" for="bank_name">Bank Name</label>
								<input type="text" id="bank_name" name="bank_name" class="form-control">
						</div>
						<div class="form-group">
								<label class="control-label" for="account_number">Account Number</label>
								<input type="text" id="account_number" name="account_number" class="form-control">
						</div>
						<div class="form-group">
								<label class="control-label" for="account_type">Account Type</label>
								<input type="text" id="account_type" name="account_type" class="form-control">
						</div>
						<div class="form-group">
								<label class="control-label" for="ifsc_code">IFSC Code</label>
								<input type="text" id="ifsc_code" name="ifsc_code" class="form-control">
						</div>
						<div class="form-group">
									<label for="file">Upload Logo</label>
									<input type="file" id="file" name="file" accept="image/x-png,image/gif,image/jpeg" onchange="CheckFileName();">
						</div>
						<!--<div class="form-group">
									<label for="file">Upload Stamp</label>
									<input type="file" id="stamp" name="stamp" accept="image/x-png,image/gif,image/jpeg" onchange="CheckFileName();">
						</div>-->
						<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {$_L['Submit']}</button>
				</form>
      </div>
    </div>
  </div>
</div>
{include file="sections/footer.tpl"}