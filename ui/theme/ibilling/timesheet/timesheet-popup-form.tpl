<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Timesheet</h3>
</div>
<div class="modal-body text-center">
    {assign var='salery_type' value=$employee->salery_type}

    {if $salery_type == 'per_hour'}
        <div>
            <i class="fa fa-clock-o" style="font-size:20rem;color:green"></i>
        </div>    
        <form id="timesheet-entry" method="post">
            <input type="hidden" name="type" value="{$salery_type}">
            <input type="hidden" name="record_id" value="{(isset($timesheet->id)) ? $timesheet->id : null}">
            <input type="hidden" name="employee_id" value="{$employee->id}">
            <input type="hidden" name="amount" value="{$employee->salery_amt}">
            <input type="hidden" name="branch_id" value="{$employee->branch_id}">
            
            {if empty($timesheet->checkin)}
                <button type="submit" class="btn btn-success timesheet-entry-post">Check In</button>
                <button type="button" class="btn btn-danger" style="opacity:0.5">Check Out</button>
                
            {elseif empty($timesheet->checkout)}
                <button type="button" class="btn btn-success" style="opacity:0.5">Check In</button>
                <button type="submit" class="btn btn-danger timesheet-entry-post">Check Out</button> 
                
            {elseif !empty($timesheet->checkin) && !empty($timesheet->checkout)}
                <button type="button" class="btn btn-success" style="opacity:0.5">Check In</button>
                <button type="button" class="btn btn-danger" style="opacity:0.5">Check Out</button>
                
            {/if}                
        </form>
        <br>
        {if !empty($timesheet->checkin)}
            <p><b>CheckIn Time :</b> {$timesheet->checkin}</p>
        {/if}

        {if !empty($timesheet->checkout)}
            <p><b>CheckOut Time :</b> {$timesheet->checkout}</p>
        {/if}  
    {else} 
        <form id="timesheet-entry" method="post">
            <input type="hidden" name="type" value="{$salery_type}">
            <input type="hidden" name="record_id" value="{(isset($timesheet->id)) ? $timesheet->id : null}">
            <input type="hidden" name="employee_id" value="{$employee->id}">
            <div class="row">
                <div class="col-md-4">
                    <input readonly placeholder="date" min="{$timesheet->date}" class="form-control" type="date" name="date" value="{date('Y-m-d')}">
                </div>
                <div class="col-md-4">
                    <input placeholder="Quantity" class="form-control" type="number" name="qty" value="{$timesheet->qty}">
                </div>
                <div class="col-md-4">
                    <input placeholder="Amount" class="form-control" type="number" name="amount" value="{(isset($timesheet->amount)) ? $timesheet->amount : $employee->salery_amt}">
                </div>  
                 <div class="col-md-12">
                     <label for="remarks">Remarks</label>
                     {if !empty($timesheet->remarks)}
                     <textarea class="form-control" placeholder="Remarks" name="remarks" id="remarks" rows="3">{$timesheet->remarks}</textarea>
                    {else}
                     <textarea class="form-control" placeholder="Remarks" name="remarks" id="remarks" rows="3"></textarea>
                     {/if}
                     
                </div>                            
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success mt-2 timesheet-entry-post">Submit</button>  
                </div>                             
            </div>                  
        </form>    
    {/if} 
</div>