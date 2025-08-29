<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Salery Setup</h3>
</div>
<div class="modal-body">
	<form class="form-horizontal" role="form" id="setup_salery_form" method="post">
		<div class="form-group">
			<label class="col-lg-2 control-label" for="product_stock">Stock</label>
			<div class="col-lg-10">
				<input type="number" name="product_stock" class="form-control" value="" autocomplete="off" placeholder="e.g : 10">
			</div>
		</div>
		<input type="hidden" name="id" value="{$d['id']}">
	</form>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn">{$_L['Close']}</button>
	<button id="setup_salery_button" class="btn btn-primary">{$_L['Update']}</button>
</div>