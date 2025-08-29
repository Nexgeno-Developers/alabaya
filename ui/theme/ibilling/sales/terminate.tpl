
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Terminate <b>{SaleID($sale_id)}</b></h3>
</div>
<div class="modal-body">
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
					<form class="form-horizontal" id="terminate-sale-form">
						<div class="row">
                            <input type="hidden" value="{$sale_id}" name="sale_id">
							<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<label class="col-md-2 control-label" for="reason">Reason</label>
									<div class="col-md-10">
										<textarea name="reason" class="form-control" rows="5"></textarea>
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
<div class="modal-footer">
	<button id="terminate_sale" class="btn btn-primary">Terminate</button>

	<button type="button" data-dismiss="modal" class="btn">Close</button>
</div>