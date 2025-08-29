<script src="ui/lib/dp/dist/datepicker.min.js"></script>
<link href="ui/lib/dp/dist/datepicker.min.css" rel="stylesheet">
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
   <h3>Edit Timesheet</h3>
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
               {if $salery_type == 'per_hour' && ($timesheet_allocation == '' || $timesheet_allocation == null)}
               <form class="form-horizontal" id="edit-sale-form">
                <input type="hidden" name="type" value="{$salery_type}">
                <input type="hidden" value="{$timesheet->id}" name="timesheet_id">
                  <div class="row">
                     
                     <div class="col-md-6">
                        <div class="">
                            <label class="" for="checkin">Check In</label>
                            <input {if $is_today}disabled{/if} min="" max="" type="datetime-local" name="checkin" class="form-control" value="{$timesheet->checkin}">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="">
                            <label class="" for="checkout">Check Out</label>
                            <input {if $is_today}disabled{/if} min="" max="" type="datetime-local" name="checkout" class="form-control" value="{$timesheet->checkout}">
                        </div>
                     </div>
                  </div>
               </form>
               {else}
               <form id="edit-sale-form" method="post">
                   <input type="hidden" name="type" value="{$salery_type}">
                    <input type="hidden" value="{$timesheet->id}" name="timesheet_id">
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
                             <textarea class="form-control" name="remarks" id="remarks" rows="3">{$timesheet->remarks}</textarea>
                        </div>
                                                    
                    </div>                  
                </form>  
               {/if}
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal-footer">
    {if $is_today && !$timesheet_allocation}
        <p>You cannot edit today's entry as it will be updated automatically by the system. Please check back tomorrow.</p>
    {/if}
    <button {if $is_today && !$timesheet_allocation} disabled {/if} id="edit_timesheet" class="btn btn-primary">Update</button>
    <button type="button" data-dismiss="modal" class="btn">Close</button>
</div>