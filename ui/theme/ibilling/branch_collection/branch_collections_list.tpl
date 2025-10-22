{include file="sections/header.tpl"}

<style>
.dataTables_wrapper .dt-buttons { display:flex; gap:10px; }
.status-badge{ padding:4px 10px; border-radius:12px; font-size:12px; color:#fff; }
.status-pending { background-color:#f0ad4e; } /* orange */
.status-partial { background-color:#5bc0de; } /* blue */
.status-confirmed { background-color:#5cb85c; } /* green */
.action-btns .btn { margin-right:5px; }
tr.needs-approval > td {
  background: #fff8e6;
  border-left: 4px solid #f0ad4e;
}
.label.label-warning {
  display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px;
}
</style>


<div class="row">
  <div class="col-lg-12">
    <div class="ibox float-e-margins">
      <div class="ibox-title">
        <h5>Branch Collection Records</h5>
        <div class="ibox-tools">
        {*if $user->roleid == 0*}
          <button class="btn btn-xs btn-primary add_collection" data-href="{$_url}branch_collections/modal_add_collection/">
            <i class="fa fa-plus"></i> Add Collection
          </button>
          {*/if*}
        </div>
      </div>

      <div class="ibox-content">
        <!-- Filters -->
        <form id="filterForm" class="form-inline" style="margin-bottom:10px;">
            
          <div class="form-group">
            <label>Branch</label>
            <select name="f_branch" id="f_branch" class="form-control" {if $user->roleid neq 0} readonly disabled {/if} >
                {if $user->roleid eq 0}
                    <option value="">All</option>
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

          <div class="form-group">
            <label>Date From</label>
            <input type="date" id="date_from" class="form-control">
          </div>

          <div class="form-group">
            <label>Date To</label>
            <input type="date" id="date_to" class="form-control">
          </div>

          <div class="form-group">
            <label>Status</label>
            <select id="f_status" class="form-control">
              <option value="">All</option>
              <option value="Pending for approval">Pending</option>
              <option value="Partially Paid">Partially Paid</option>
              <option value="Confirmed">Confirmed</option>
            </select>
          </div>
{*
          <div class="form-group">
            <input type="text" id="f_search" class="form-control" placeholder="Search">
          </div>
*}

          <button type="submit" id="btnFilter" class="btn btn-primary">
              <i class="fa fa-search" aria-hidden="true"></i> Filter
          </button>
          <button type="button" id="btnReset" class="btn btn-default">
              <i class="fa fa-refresh" aria-hidden="true"></i> Reset
          </button>
        </form>

        <div class="table-responsive">
          <table id="branchCollectionTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Branch Name</th>
                <th>Collection Date</th>
                <th>Amount</th>
                <th>Handover Amount</th>
                <th>Status</th>
                <th>Owner Remark</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th></th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
              <tr>
                <th colspan="3" class="text-right">Total Collected</th>
                <th id="total_collected">₹0.00</th>
                <th id="total_handover">₹0.00</th>
                <th colspan="6"></th>
              </tr>
            </tfoot>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

{include file="sections/footer.tpl"}
<script>
  window.IS_OWNER = {if $user->roleid == 0}true{else}false{/if};
</script>
{literal}
<script>
$(function(){

    var _url = $("#_url").val();
    var $modal = $('#ajax-modal');

    var table = $('#branchCollectionTable').DataTable({
      searching: false,
      processing: true,
        ajax: {
        url: _url + 'branch_collections/ajax_list/',
        data: function(d){
            d.branch_id = $('#f_branch').val();
            d.date_from = $('#date_from').val();
            d.date_to = $('#date_to').val();
            d.status = $('#f_status').val();
            d.search = $('#f_search').val();
        },
        dataSrc: 'data'
        },
        columns: [
        { data: 0 },
        { data: 1 },
        { data: 2 },
        { data: 3, className: 'text-right' },
        { data: 4, className: 'text-right' },
        { data: 5 },
        { data: 6 },
        { data: 7 },
        { data: 8 },
        { data: 9, visible: false },
        { data: 10, orderable: false }
        ],
        dom: 'frtip',
        order: [[2,'desc']],
        createdRow: function(row, data){
        var pendingCount = parseInt(data[9] || 0, 10);
        var isOwner = !!window.IS_OWNER; // only nudge owner
        if (isOwner && pendingCount > 0) {
            $(row).addClass('needs-approval');
        }
        },
        drawCallback: function(settings) {
        var api = this.api();
        var data = api.rows({page:'current'}).data();
        var total_collected = 0;
        var total_handover = 0;
        for (var i=0;i<data.length;i++) {
            var col = data[i];
            // col[3] and col[4] are amount strings like "100.00" or "₹100.00" depending on server output
            var collected = parseFloat((col[3]||'0').toString().replace(/[^0-9\.\-]/g,'')) || 0;
            var handover = parseFloat((col[4]||'0').toString().replace(/[^0-9\.\-]/g,'')) || 0;
            total_collected += collected;
            total_handover += handover;
        }
        $('#total_collected').text('₹' + total_collected.toFixed(2));
        $('#total_handover').text('₹' + total_handover.toFixed(2));
        }
    });

    // On filter submit, reload table
    $("#filterForm").on("submit", function (e) {
        e.preventDefault();
        validateDates();
        table.ajax.reload();
    });

    // Reset filters
    $("#btnReset").on("click", function () {
        $("#filterForm")[0].reset();
        table.ajax.reload();
    });

    function validateDates() {
        // alert("validateDates called");
        const from = document.getElementById("date_from").value;
        const to = document.getElementById("date_to").value;

        // Create or reuse error div
        let errorDiv = document.getElementById("dateError");
        if (!errorDiv) {
            errorDiv = document.createElement("div");
            errorDiv.id = "dateError";
            errorDiv.style.color = "red";
            errorDiv.style.marginTop = "5px";
            document.querySelector("#filterForm").appendChild(errorDiv);
        }

        errorDiv.textContent = ""; // Clear previous error

        // ✅ Case 1: both empty → valid
        if (!from && !to) {
            return true;
        }

        // ✅ Case 2: one filled, one empty → invalid
        if ((from && !to) || (!from && to)) {
            errorDiv.textContent = "Please select both dates or leave both empty.";
            return false;
        }

        // ✅ Case 3: both filled but 'to' < 'from' → invalid
        if (new Date(to) < new Date(from)) {
            errorDiv.textContent = "‘Date To’ cannot be earlier than ‘Date From’.";
            return false;
        }

        // ✅ Case 4: valid
        return true;
    }

    // Add collection (modal)
    $(document).on('click', '.add_collection', function(e){
        e.preventDefault();
        var href = $(this).data('href') || $(this).attr('href');
        $('body').modalmanager('loading');
        $modal.load(href, '', function(){ $modal.modal(); });
    });

    // Add handover modal
    $(document).on('click', '.add_handover', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('body').modalmanager('loading');
        $modal.load(href, '', function(){ $modal.modal(); });
    });

    // View handovers modal
    $(document).on('click', '.view_handovers', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('body').modalmanager('loading');
        $modal.load(href, '', function(){ $modal.modal(); });
    });

    // Submit modal (collection or handover)
    $modal.on('click', '.modal_submit', function(e){
        e.preventDefault();
        $modal.modal('loading');

        var form = $("#ib_modal_form");
        var action = form.attr('action');
        if(!action){
        toastr.error('No form action set');
        $modal.modal('loading');
        return;
        }

        $.post(action, form.serialize(), function(resp){
        if (resp && resp.success) {
            $modal.modal('hide');
            toastr.success('Saved');
            table.ajax.reload(null, false);
        } else {
            $modal.modal('loading');
            toastr.error((resp && resp.message) ? resp.message : 'Something went wrong');
        }
        }, 'json').fail(function(){
        $modal.modal('loading');
        toastr.error('Request failed');
        });
    });

    // Delete collection (AJAX + confirm)
    $(document).on('click', '.cdelete', function(e){
      e.preventDefault();
      var id = $(this).data('id');
      var $btn = $(this);

      bootbox.confirm('Delete this collection and all its handovers?', function(ok){
        if(!ok) return;
        $btn.prop('disabled', true);

        $.post(_url + 'branch_collections/delete_collection/', { id: id }, function(resp){
          if (resp && resp.success) {
            toastr.success('Collection deleted');
            table.ajax.reload(null, false);
          } else {
            toastr.error((resp && resp.message) ? resp.message : 'Delete failed');
            $btn.prop('disabled', false);
          }
        }, 'json').fail(function(){
          toastr.error('Request failed');
          $btn.prop('disabled', false);
        });
      });
    });



    // Delete handover (AJAX)
    $(document).on('click', '.handover_delete', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var $btn = $(this);
        bootbox.confirm('Are you sure you want to delete this handover?', function(ans){
            if (!ans) return;
            $btn.prop('disabled', true);
            $.post(_url + 'branch_collections/handover_delete/', { id: id }, function(resp){
            if (resp && resp.success) {
                $('#handover_row_' + id).fadeOut(function(){ $(this).remove(); });
                
                // Refresh main collection table to update handover totals / status
                table.ajax.reload(null, false);
            } else {
                toastr.error((resp && resp.message) ? resp.message : 'Delete failed');
                $btn.prop('disabled', false);
            }
            }, 'json').fail(function(){
            toastr.error('Request failed');
            $btn.prop('disabled', false);
            });
        });
    });

    // Approve handover (new)
    $(document).on('click', '.handover_approve', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var $btn = $(this);
        bootbox.confirm('Are you sure you want to approve this handover?', function(ans){
            if (!ans) return;
            $btn.prop('disabled', true);
            $.post(_url + 'branch_collections/handover_approve/', { id: id }, function(resp){
                if(resp.success){
                    // Update modal row status & hide approve button
                    $('#handover_row_' + id + ' .handover_approve').remove();
                    $('#handover_row_' + id + ' td.status-cell').text('Approved');

                    // Refresh main collection table to reflect new total/status
                    table.ajax.reload(null, false);
                    toastr.success('Handover approved');
                } else {
                    toastr.error(resp.message || 'Approve failed');
                    $btn.prop('disabled', false);
                }
            }, 'json').fail(function(){
                toastr.error('Request failed');
                $btn.prop('disabled', false);
            });
        });
    });
});
</script>
{/literal}
