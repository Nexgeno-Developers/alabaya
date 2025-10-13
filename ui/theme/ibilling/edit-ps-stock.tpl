<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Stock</h3>
</div>
<div class="modal-body">
	<form class="form-horizontal" role="form" id="edit_form_stock" method="post">
	
		<div class="form-group">
			<label class="col-lg-2 control-label" for="branch_id">Branch</label>
			<div class="col-lg-10">
				<select name="branch_id" id="branch_id" class="form-control" required>
					<option value="">Select Branch</option>
					{foreach $branches as $branch}
						<option value="{$branch.id}">{$branch.alias|default:$branch.account}</option>
					{/foreach}
				</select>
			</div>
		</div>

	    {if $d['product_type'] eq 'customize'}
		<div class="form-group">
			<label class="col-lg-2 control-label" for="product_stock">Vendor</label>
			<div class="col-lg-10">
				<select name="vendor_id" class="form-control">
				    <option value="">--Select--</option>
				    {foreach $vendorList as $row}
				        <option value="{$row['id']}">{$row['account']}</option>
				    {/foreach}
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label" for="product_stock">Purchase Price (per stock)</label>
			<div class="col-lg-10">
				<input type="number" name="purchase_price" class="form-control" value="{$d['purchase_price']}">
			</div>
		</div>		
	    {/if}
		<div class="form-group">
			<label class="col-lg-2 control-label" for="product_stock">Stock</label>
			<div class="col-lg-10">
				<input type="number" name="product_stock" id="product_stock" class="form-control" value="" autocomplete="off" placeholder="e.g : 10" required min="0.1" step="0.1">
			</div>
		</div>

		<input type="hidden" name="id" value="{$d['id']}">
		<input type="hidden" name="product_type" value="{$d['product_type']}">
	</form>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn">{$_L['Close']}</button>
	<button id="update_stock" class="btn btn-primary">{$_L['Update']}</button>
</div>