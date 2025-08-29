<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Salery Setup</h3>
</div>
<div class="modal-body">
	<form class="form-horizontal" role="form" id="setup_salery_form" method="post">
		<div class="form-group">
			<label class="col-lg-2 control-label" for="product_stock">Salery Type</label>
			<div class="col-lg-10">
				<select id="salary_type" name="salery_type" class="form-control" autocomplete="off">
				    {if $d['salery_type'] == 'per_hour'}
				    	<option value="per_hour" {if $d['salery_type'] == 'per_hour'} selected{/if}>Per Hour</option>
					{elseif $d['salery_type'] == 'per_piece'}
				    	<option value="per_piece" {if $d['salery_type'] == 'per_piece'} selected{/if}>Per Piece</option>
					{else}
						<option value="per_hour" {if $d['salery_type'] == 'per_hour'} selected{/if}>Per Hour</option>
						<option value="per_piece" {if $d['salery_type'] == 'per_piece'} selected{/if}>Per Piece</option>
					{/if}
				</select>
			</div>
		</div>
		<div id="empamount_field" class="form-group {if $d['salery_type'] != 'per_hour'}hide{/if}">
			<label class="col-lg-2 control-label" for="product_stock">Salery Amount</label>
			<div class="col-lg-10">
				<input type="number" id="salery_amt" name="salery_amt" class="form-control" value="{if $d['salery_type'] == 'per_hour'}{$d['salery_amt']}{/if}" autocomplete="off">
			</div>
		</div>		
        <div id="empcode_field" class="form-group {if $d['salery_type'] != 'per_hour'}hide{/if}">
            <label class="col-lg-2 control-label" for="empcode">Employee Code</label>
            <div class="col-lg-10">
                <input type="text" id="empcode" name="empcode" class="form-control" value="{$d['emp_code']}">
            </div>
        </div>		
		<input type="hidden" name="id" value="{$d['id']}">
	</form>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn">{$_L['Close']}</button>
	<button id="setup_salery_button" class="btn btn-primary">{$_L['Update']}</button>
</div>

<script>
$(document).ready(function() {
    const $salaryTypeSelect = $('#salary_type');
    const $empcodeField = $('#empcode_field');
    const $empamountField = $('#empamount_field');
    const $empcode = $('#empcode');
    const $salery_amt = $('#salery_amt');

    // Function to toggle visibility of Employee Code field based on selected salary type
    const toggleEmpCodeField = () => {
        const isHourly = $salaryTypeSelect.val() === 'per_hour';
        if (isHourly) {
            $empcodeField.removeClass('hide'); // Show the field if hourly
            $empamountField.removeClass('hide'); // Show the field if hourly
        } else {
            $empamountField.addClass('hide'); // Hide the field if piece rate
            $salery_amt.val(''); // Clear the input text
            $empcodeField.addClass('hide'); // Hide the field if piece rate
            $empcode.val(''); // Clear the input text
        }
        // console.log(isHourly ? "Hourly rate selected." : "Piece rate selected.");
    };

    // Attach change event listener and execute on page load
    $salaryTypeSelect.change(toggleEmpCodeField);
    toggleEmpCodeField(); // Initial visibility setup
});
</script>