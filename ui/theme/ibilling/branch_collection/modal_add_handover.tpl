<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Add Handover for Collection #{$collection.id}</h3>
</div>

<div class="modal-body">
    {if !$can_add}
        <div class="alert alert-info" role="alert" style="margin-bottom:0">
            <strong>Nothing pending.</strong>
            Requested: <b>₹{$cap|number_format:2:'.':''}</b>,
            Already handed over: <b>₹{$paid_all|number_format:2:'.':''}</b>,
            Remaining: <b>₹{$remaining|number_format:2:'.':''}</b>.
        </div>
    {else}
    <div class="well" style="padding:10px; margin-bottom:12px;">
        <div style="display:flex; gap:18px; flex-wrap:wrap; font-size:13px;">
            <div>Requested: <b>₹{$cap|number_format:2:'.':''}</b></div>
            <div>Already handed over: <b>₹{$paid_all|number_format:2:'.':''}</b></div>
            <div>Remaining: <b id="remaining_now">₹{$remaining|number_format:2:'.':''}</b></div>
        </div>
    </div>
    <form class="form-horizontal" id="ib_modal_form" action="{$smarty.server.REQUEST_URI}">
        <div class="form-group">
            <label class="col-lg-4 control-label">Amount Paid<small class="red">*</small></label>
            <div class="col-lg-8">
                <input type="number" name="amount_paid" class="form-control" step="0.01" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">Paid Date<small class="red">*</small></label>
            <div class="col-lg-8">
                <input type="date" name="paid_date" class="form-control" value="{$today}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">Note</label>
            <div class="col-lg-8">
                <textarea name="note" class="form-control" rows="2"></textarea>
            </div>
        </div>

        <input type="hidden" name="collection_id" value="{$collection.id}">
    </form>
    {/if}
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
    {if $can_add}
    <button type="submit" class="btn btn-primary modal_submit"><i class="fa fa-check"></i> Save</button>
    {/if}
</div>
