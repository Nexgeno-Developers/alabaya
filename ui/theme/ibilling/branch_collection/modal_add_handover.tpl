<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Add Handover for Collection #{$collection.id}</h3>
</div>

<div class="modal-body">
    {if !$can_add}
        <div class="alert alert-info" role="alert" style="margin-bottom:0">
            <strong>Nothing pending.</strong><br>
            Requested: <b>₹{$cap_total|number_format:2:'.':''}</b>
            (Cash: ₹{$cap_cash|number_format:2:'.':''}, QR: ₹{$cap_qr|number_format:2:'.':''})<br>
            Handed over: <b>₹{$paid_all|number_format:2:'.':''}</b>
            (Cash: ₹{$paid_cash|number_format:2:'.':''}, QR: ₹{$paid_qr|number_format:2:'.':''})<br>
            Remaining: <b>₹{$remain_total|number_format:2:'.':''}</b>
            (Cash: ₹{$remain_cash|number_format:2:'.':''}, QR: ₹{$remain_qr|number_format:2:'.':''})
        </div>
    {else}
        <div class="well" style="padding:10px; margin-bottom:12px;">
            <div style="display:flex; gap:18px; flex-wrap:wrap; font-size:13px;">
                <div>
                    Requested: <b>₹{$cap_total|number_format:2:'.':''}</b><br>
                    <small class="text-muted">(Cash: ₹{$cap_cash|number_format:2:'.':''} | QR: ₹{$cap_qr|number_format:2:'.':''})</small>
                </div>
                <div>
                    Already handed over: <b>₹{$paid_all|number_format:2:'.':''}</b><br>
                    <small class="text-muted">(Cash: ₹{$paid_cash|number_format:2:'.':''} | QR: ₹{$paid_qr|number_format:2:'.':''})</small>
                </div>
                <div>
                    Remaining: <b id="remaining_total">₹{$remain_total|number_format:2:'.':''}</b><br>
                    <small class="text-muted">
                        (Cash: <b id="remain_cash">₹{$remain_cash|number_format:2:'.':''}</b> |
                        QR: <b id="remain_qr">₹{$remain_qr|number_format:2:'.':''}</b>)
                    </small>
                </div>
            </div>
        </div>

        <form class="form-horizontal" id="ib_modal_form" action="{$smarty.server.REQUEST_URI}">
            <div class="form-group">
                <label class="col-lg-4 control-label">Type <small class="red">*</small></label>
                <div class="col-lg-8">
                    <select name="payment_type" id="payment_type" class="form-control" required>
                        <option value="Cash" {if $remain_cash <= 0}disabled{/if}>Cash {if $remain_cash <= 0}(No balance){/if}</option>
                        <option value="QR"   {if $remain_qr   <= 0}disabled{/if}>QR {if $remain_qr   <= 0}(No balance){/if}</option>
                    </select>
                    <small class="text-muted">Choose where this handover belongs.</small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Amount Paid <small class="red">*</small></label>
                <div class="col-lg-8">
                    <input type="number"
                           name="amount_paid"
                           id="amount_paid"
                           class="form-control"
                           step="0.01"
                           min="0.01"
                           required
                           data-remain-cash="{$remain_cash}"
                           data-remain-qr="{$remain_qr}">
                    <small id="amount_hint" class="help-block" style="margin:4px 0 0;">
                        Max for <b id="type_label">Cash</b>:
                        <b id="type_max">₹{$remain_cash|number_format:2:'.':''}</b>.
                        Balance after this entry: <b id="after_balance">₹{$remain_cash|number_format:2:'.':''}</b>
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Paid Date <small class="red">*</small></label>
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

        {literal}
        <script>
        (function(){
          function toN(v){ var n=parseFloat(v); return isNaN(n)?0:n; }
          function fmt(n){ return (Math.round(n*100)/100).toFixed(2); }

          var sel  = document.getElementById('payment_type');
          var amt  = document.getElementById('amount_paid');
          var lab  = document.getElementById('type_label');
          var maxEl= document.getElementById('type_max');
          var after= document.getElementById('after_balance');

          if(!sel || !amt) return;

          function currentMax(){
            var rc = toN(amt.getAttribute('data-remain-cash'));
            var rq = toN(amt.getAttribute('data-remain-qr'));
            return (sel.value === 'QR') ? rq : rc;
          }

          function updateHint(){
            var cap = currentMax();
            lab.textContent = sel.value;
            maxEl.textContent = '₹' + fmt(cap);

            var val = toN(amt.value);
            if (val > cap && amt.value !== '') {
              // soft clamp on UI (server still hard-validates)
              amt.value = fmt(cap);
              val = cap;
            }
            after.textContent = '₹' + fmt(Math.max(0, cap - val));
          }

          sel.addEventListener('change', updateHint);
          amt.addEventListener('input', updateHint);

          // initial
          updateHint();
        })();
        </script>
        {/literal}
    {/if}
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    {if $can_add}
        <button type="submit" class="btn btn-primary modal_submit"><i class="fa fa-check"></i> Save</button>
    {/if}
</div>
