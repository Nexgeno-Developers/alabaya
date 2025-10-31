{include file="sections/header.tpl"}

<style>
.dataTables_wrapper .dt-buttons{ display:flex; gap:10px; }
.dataTables_wrapper {
    position: relative;
    clear: both;
    zoom: 1;
}
</style>

<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5 style="margin-left:10px;" id="ei_total">{$_L['Total']} : 0</h5>
    </div>
    <!-- FILTERS -->
    <div class="panel-body" style="padding-bottom: 0; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <div class="radio-button">
            <input type="radio" name="payment_status" data-val="" id="group_all" checked>
            <label for="group_all">All</label>
        </div>
        <div class="radio-button">
            <input type="radio" name="payment_status" data-val="1" id="group_paid">
            <label for="group_paid">Paid</label>
        </div>
        <div class="radio-button">
            <input type="radio" name="payment_status" data-val="0" id="group_unpaid">
            <label for="group_unpaid">Unpaid</label>
        </div>
        
        <div class="form-group" style="margin-left:20px;">
            <input type="text" id="ei_search" class="form-control" placeholder="Search invoice / amount / status..." style="min-width:220px;">
        </div>
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
