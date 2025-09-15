{include file="sections/header.tpl"}

<style>
.dataTables_wrapper .dt-buttons{
    display:flex;
    gap:10px;
}
</style>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Records']}</h5>
            </div>
            <div class="ibox-content">  
                <form id="filterForm" class="row" style="margin-bottom: 20px;align-items: center;display: flex;">
                    <div class="form-group col-md-2">
                        <label>Date From</label>
                        <input type="date" class="form-control" name="date_from">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Date To</label>
                        <input type="date" class="form-control" name="date_to">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Branch</label>
                        <select class="form-control" name="branch_id">
                            <option value="">All</option>
                            {foreach $branches as $branch}
                                <option value="{$branch.id}">{$branch.alias}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Type</label>
                        <select class="form-control" name="type">
                            <option value="">All</option>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Method</label>
                        <input type="text" class="form-control" name="method">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Category</label>
                        <input type="text" class="form-control" name="category">
                    </div>
                    <div class="col-md-2" style="display: flex;justify-content: flex-end; gap:10px;">
                        <button type="submit" class="btn btn-primary btn-block" style="margin-top:5px;">
                            <i class="fa fa-search" aria-hidden="true"></i> Filter
                        </button>
                        <button type="button" id="resetFilters" class="btn btn-default btn-block" style="margin-top:5px;">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Reset
                        </button>
                    </div>
                </form>

                <table id="transactionTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{$_L['Date']}</th>
                            <th>{$_L['Account']}</th>
                            <th>{$_L['Type']}</th>
                            <th>{$_L['Description']}</th>
                            <th>{$_L['Method']}</th>
                            <th>{$_L['Category']}</th>
                            <th class="text-right">{$_L['Amount']}</th>
                            <th>{$_L['Manage']}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Totals</th>
                            <th colspan="2" id="totals"></th>
                        </tr>
                    </tfoot>
                </table>            
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
