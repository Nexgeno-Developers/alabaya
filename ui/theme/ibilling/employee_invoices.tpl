{include file="sections/header.tpl"}

<style>
.dataTables_wrapper .dt-buttons{ display:flex; gap:10px; }
.dataTables_wrapper { position: relative; clear: both; zoom: 1; }
.filter-row { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
.filter-row .form-group { margin-bottom:8px; }
</style>

<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5 style="margin-left:10px;" id="ei_total">{$_L['Total']} : 0</h5>
    </div>

    <!-- FILTERS (replaces radio buttons) -->
    <div class="panel-body" style="padding-bottom: 0;">
        <form id="ei_filter_form" class="filter-row">
            <!-- Invoice search (your old search box) -->
            <div class="form-group">
                <label for="ei_search" class="control-label">Search</label>
                <input type="text" id="ei_search" class="form-control" placeholder="Invoice / amount / status...">
            </div>

            <!-- Payment Status -->
            <div class="form-group">
                <label for="ei_payment_status" class="control-label">Salary Status</label>
                <select id="ei_payment_status" class="form-control">
                    <option value="">All</option>
                    <option value="1">Paid</option>
                    <option value="0">Unpaid</option>
                </select>
            </div>

            <!-- Order Status (Assigned / Complete) -->
            <div class="form-group">
                <label for="ei_order_status" class="control-label">Order Status</label>
                <select id="ei_order_status" class="form-control">
                    <option value="">All</option>
                    <option value="1">Complete</option>
                    <option value="0">Assigned</option>
                </select>
            </div>

            <!-- Date type -->
            <div class="form-group">
                <label for="ei_date_type" class="control-label">Date Filter</label>
                <select id="ei_date_type" class="form-control">
                    <option value="">-- Any --</option>
                    <option value="assign_date">Assign Date</option>
                    <option value="completed_date">Completed Date</option>
                    <option value="paid_date">Paid Date</option>
                </select>
            </div>

            <!-- Date from -->
            <div class="form-group">
                <label for="ei_date_from" class="control-label">From</label>
                <input type="date" id="ei_date_from" class="form-control">
            </div>

            <!-- Date to -->
            <div class="form-group">
                <label for="ei_date_to" class="control-label">To</label>
                <input type="date" id="ei_date_to" class="form-control">
            </div>

            <div class="form-group">
                <button type="button" id="ei_apply_filters" class="btn btn-primary">Apply</button>
                <button type="button" id="ei_reset_filters" class="btn btn-default">Reset</button>
            </div>
        </form>
    </div>

    <div class="ibox-content">
        <div class="table-responsive">
            <table id="employeeInvoicesTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Invoice ID / #</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Earn Amount</th>
                        <th>Order Status</th>
                        <th>Salary Status</th>
                        <th>Assign Date</th>
                        <th>Completed Date</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="10" id="ei_footer"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
