<style>
.modal-footer .btn+.btn {
    margin-bottom: 5px;
    margin-left: 5px;
}
</style>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h3>{if $collection}Edit Collection #{ $collection.id }{else}Add Collection{/if}</h3>
</div>

<div class="modal-body">
  <form class="form-horizontal" id="ib_modal_form" action="{$_url}branch_collections/add_collection_post/">
    <div class="form-group">
      <label class="col-lg-4 control-label">Branch <small class="red">*</small></label>
      <div class="col-lg-8">
        <select name="branch_id" class="form-control" required>
          {foreach $branches as $b}
            <option value="{$b.id}" {if $collection && $collection.branch_id == $b.id}selected{/if}>{$b.alias|default:$b.account}</option>
          {/foreach}
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-4 control-label">Collection Date <small class="red">*</small></label>
      <div class="col-lg-8">
        <input type="date" name="collection_date" class="form-control" value="{$collection.collection_date|default:$today}" required>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-4 control-label">Amount (₹) <small class="red">*</small></label>
      <div class="col-lg-8">
        <input type="number" step="0.01" name="amount" class="form-control" value="{$collection.collected_amount|default:''}" required>
      </div>
    </div>
{*
    <div class="form-group">
      <label class="col-lg-4 control-label">Reference No.</label>
      <div class="col-lg-8">
        <input type="text" name="reference_no" class="form-control" value="{$collection.reference_no|default:''}">
      </div>
    </div>
*}
    <div class="form-group">
      <label class="col-lg-4 control-label">Notes</label>
      <div class="col-lg-8">
        <textarea name="note" class="form-control" rows="2">{$collection.owner_remark|default:''}</textarea>
      </div>
    </div>

    <input type="hidden" name="collection_id" value="{$collection.id|default:''}">
  </form>
</div>

<div class="modal-footer">
  <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
  <button class="btn btn-primary modal_submit"><i class="fa fa-check"></i> Save</button>
</div>
