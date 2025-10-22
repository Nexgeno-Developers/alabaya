<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h3>Handover Entries for Collection #{$collection.id}</h3>
</div>

<div class="modal-body">
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Paid Date</th>
        <th>Amount</th>
        <th>Type</th>
        <th>Paid By</th>
        <th>Note</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      {foreach $handovers as $h name=handloop}
        <tr id="handover_row_{$h.id}">
          <td>{$smarty.foreach.handloop.index+1}</td>
          <td>{$h.paid_date}</td>
          <td>₹{$h.amount_paid}</td>
          <td>{$h.payment_type|default:'-'}</td>
          <td>{$h.paid_by_name}</td>
          <td>{$h.note|default:'-'}</td>
          <td class="status-cell">{$h.status}</td>
          <td>
            {if $user->roleid eq 0 && $h.status eq 'Pending'}
                <button class="btn btn-xs btn-success handover_approve" data-id="{$h.id}">
                    <i class="fa fa-check"></i> Approve
                </button>
            {/if}
            {if $user->roleid neq 0 && $h.status neq 'Approved'}
            <button class="btn btn-xs btn-danger handover_delete" data-id="{$h.id}"><i class="fa fa-trash"></i></button>
            {/if}
            {if $user->roleid eq 0}
            <button class="btn btn-xs btn-danger handover_delete" data-id="{$h.id}"><i class="fa fa-trash"></i></button>
            {/if}
          </td>
        </tr>
      {foreachelse}
        <tr><td colspan="7" class="text-center">No handovers found.</td></tr>
      {/foreach}
    </tbody>
  </table>
</div>

<div class="modal-footer">
  <button class="btn btn-default" data-dismiss="modal">Close</button>
</div>
