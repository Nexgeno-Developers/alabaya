{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Invoices']}</h5>
                <div class="ibox-tools">
                    <a href="{$_url}invoices/add/" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> {$_L['Add Invoice']}</a>
                </div>
            </div>

            <div class="ibox-content">

                <!-- Filters row -->
                <form id="invoiceFilters" style="margin-bottom:15px;">
                    <div class="row">
                    <div id="dateError" style="display:none; position: relative;margin: 30px 0 10px;background-color: #904141;color: #fff;padding: 10px;"></div>
                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="filter_branch">Branch</label>
                                <select name="branch_id" id="filter_branch" class="form-control">
                                    {if $user->roleid eq 0}
                                        <option value="">All</option>
                                        {foreach $branches as $branch}
                                            <option value="{$branch.id}" {if $branch.id eq $user->branch_id}selected{/if}>
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

                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="type">Date Filter By</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">All</option>
                                    <option value="invoice_date">Invoice Date</option>
                                    <option value="delivery_date">Delivery Date</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="date_from">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="date_to">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="invoice_no">Invoice Number</label>
                                <input type="text" name="invoice_no" id="invoice_no" class="form-control" placeholder="Invoice Number">
                            </div>
                        </div>

                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <input type="text" name="customer" id="customer" class="form-control" placeholder="Customer">
                            </div>
                        </div>

                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="payment_status">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-control">
                                    <option value="">All</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Unpaid">Unpaid</option>
                                    <option value="Partially Paid">Partially Paid</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 invoice-main-div-dropdown">
                            <div class="form-group">
                                <label for="delivery_status">Invoice Status</label>
                                <select name="delivery_status" id="delivery_status" class="form-control">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <div class="form-group">
                                <button id="btnInvoiceFilter" class="btn btn-primary">Filter</button>
                                <button id="btnInvoiceReset" type="button" class="btn btn-default">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="table-responsive">
                    <table id="invoice-datatable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>{$_L['Customer']}</th>
                                <th>{$_L['Phone']}</th>
                                <th>{$_L['Amount']}</th>
                                <th>{$_L['Invoice Date']}</th>
                                <th>Delivery date</th>
                                <th>Reminder Date</th>
                                <th>Payment Status</th>
                                <th>Invoice Status</th>
                                <th>Created By</th>
                                <th>Updated At</th>
                                <th>Created At</th>
                                <th class="text-right">{$_L['Manage']}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}

{literal}
<script>
$(function(){

    // On page load, if ?delivery_status is present, apply it to filter dropdown
    const urlParams = new URLSearchParams(window.location.search);
    const deliveryStatus = urlParams.get('delivery_status');
    var $select = $('#delivery_status');

    if (deliveryStatus) {
        toastr.info('Showing ' + deliveryStatus + ' invoices');
        if ($select.find('option[value="' + deliveryStatus + '"]').length > 0) {
            $select.val(deliveryStatus);
        }
    }
        
    const branchIdParam = urlParams.get('branch_id');
    var $branchSelect = $('#filter_branch');

    if (branchIdParam) {
        if ($branchSelect.find('option[value="' + branchIdParam + '"]').length > 0) {
            $branchSelect.val(branchIdParam);
        }else if(branchIdParam === '' || branchIdParam === 'all'){
            $branchSelect.val('');
        }        
    }

    // helper: serialize form to object
    $.fn.serializeObject = function(){
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    // Initialize DataTable
    var table = $('#invoice-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "invoices/list-datatable",
            type: 'POST',
            data: function(d){
                return $.extend({}, d, $('#invoiceFilters').serializeObject());
            }
        },
        dom: 'Bfrtip',
        buttons: [
            //{ extend: 'csv', text: 'CSV' },
            'pageLength'
        ],
        lengthMenu: [
            [10,25,50,100,-1],
            [10,25,50,100,'All']
        ],
        order: [[11, 'desc']],
        columnDefs: [
            { orderable: false, targets: [12] }
        ],
        drawCallback: function(settings){
            // Initialize AutoNumeric for amount column
            $('.amount').autoNumeric('init', {
                dGroup: 3,
                aPad: 2,
                pSign: '$',
                aDec: '.',
                aSep: ','
            });

            // AJAX Delete
            $('.cdelete').off('click').on('click', function(e) {
                e.preventDefault();
                var id = $(this).attr('id').replace('iid','');
                var csrf_token = $('#csrf_token').val();

                bootbox.confirm("Are you sure?", function(result){
                    if(result){
                        $.post(base_url + "invoices/ajax-delete/" + id, {_token: csrf_token}, function(data){
                            if(data.success){
                                table.ajax.reload(null, false); // reload datatable without resetting pagination
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message);
                            }
                        }, 'json');
                    }
                });
            });


        }
    });

    // if (branchIdParam || deliveryStatus) {
    //     table.ajax.reload();
    // }

    // Filter button
    $('#btnInvoiceFilter').on('click', function(e){
        e.preventDefault();
        if (validateDates()) {
            table.ajax.reload();
        }
    });

    // Reset filters
    $('#btnInvoiceReset').on('click', function(){
        $('#invoiceFilters')[0].reset();
        table.ajax.reload();
    });

    // Quick search on Enter
    $('#invoiceFilters input').on('keypress', function(e){
        if (e.which == 13) {
            e.preventDefault();
            if (validateDates()) {
                table.ajax.reload();
            }
        }
    });

    function validateDates() {
        const from = document.getElementById("date_from").value;
        const to = document.getElementById("date_to").value;

        // Use existing error div
        const errorDiv = document.getElementById("dateError");
        errorDiv.textContent = "";       // Clear previous error
        errorDiv.style.display = "none"; // Hide by default

        // Case 1: both empty → valid
        if (!from && !to) {
            return true;
        }

        // Case 2: one filled, one empty → invalid
        if ((from && !to) || (!from && to)) {
            errorDiv.textContent = "Please select both dates or leave both empty.";
            errorDiv.style.display = "block"; // Show error
            return false;
        }

        // Case 3: both filled but 'to' < 'from' → invalid
        if (new Date(to) < new Date(from)) {
            errorDiv.textContent = "‘Date To’ cannot be earlier than ‘Date From’.";
            errorDiv.style.display = "block"; // Show error
            return false;
        }

        // Case 4: valid
        return true;
    }

});
</script>
{/literal}

