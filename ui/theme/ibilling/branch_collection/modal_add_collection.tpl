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
        <select name="branch_id" id="branch_id" class="form-control" required>
          {if $user->roleid eq 0}
              {foreach $branches as $branch}
                  <option value="{$branch.id}">
                      {$branch.alias|default:$branch.account}
                  </option>
              {/foreach}
          {else}
              {foreach $branches as $branch}
                  {if $branch.id eq $user->branch_id}
                      <option value="{$branch.id}" selected>
                          {$branch.alias|default:$branch.account}
                      </option>
                  {/if}
              {/foreach}
          {/if}
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
      <label class="col-lg-4 control-label">Cash Amount (₹) <small class="red">*</small></label>
      <div class="col-lg-8">
        <input type="number" step="0.01" min="0" name="cash_amount" id="cash_amount"
               class="form-control"
               value="{$collection.cash_amount|default:'0.00'}" required>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-4 control-label">QR Amount (₹) <small class="red">*</small></label>
      <div class="col-lg-8">
        <input type="number" step="0.01" min="0" name="qr_amount" id="qr_amount"
               class="form-control"
               value="{$collection.qr_amount|default:'0.00'}" required>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-4 control-label">Amount (₹) <small class="red">*</small></label>
      <div class="col-lg-8">
        <input type="number" step="0.01" name="amount" id="amount_total" class="form-control" value="{$collection.collected_amount|default:''}" required readonly>
        <small class="help-block">Auto-calculated: Cash + QR</small>
        <small id="tx_hint" class="help-block" style="display:none;">Fetching transactions…</small>
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

{literal}
<script>
(function(){
  function toNum(v){ var n=parseFloat(v); return isNaN(n)?0:n; }
  function fmt(n){ return (Math.round(n*100)/100).toFixed(2); }

  var $branch = $('select[name="branch_id"]');
  var $date   = $('input[name="collection_date"]');
  var $cash   = $('#cash_amount');
  var $qr     = $('#qr_amount');
  var $total  = $('#amount_total');
  var $hint   = $('#tx_hint');

  // lock fields
  $cash.prop('readonly', true);
  $qr.prop('readonly', true);

  function setLoading(on){
    if(!$hint.length) return;
    $hint.toggle(on).text(on ? 'Fetching transactions…' : '');
  }

  function fill(cash, qr){
    $cash.val(fmt(cash));
    $qr.val(fmt(qr));
    $total.val(fmt(cash + qr));
  }

  function fetchTotals(){
    var b = $branch.val();
    var d = $date.val();
    if(!b || !d){ fill(0,0); return; }

    // IMPORTANT: no query string here
    var base = $('#_url').val();               // e.g. http://localhost/abaya-desginer/?ng=
    var url  = base + 'branch_collections/ajax_tx_totals/';

    setLoading(true);
    $.post(url, { branch_id: b, collection_date: d }, function(resp){
      setLoading(false);
      if (resp && resp.success) {
        var data = resp.data || {};
        fill(toNum(data.cash), toNum(data.qr));
      } else {
        fill(0,0);
        toastr.warning((resp && resp.message) ? resp.message : 'Could not fetch transactions');
      }
    }, 'json').fail(function(){
      setLoading(false);
      fill(0,0);
      toastr.error('Failed to fetch transactions');
    });
  }

  $branch.on('change', fetchTotals);
  $date.on('change', fetchTotals);
  fetchTotals(); // initial
})();
</script>

{/literal}