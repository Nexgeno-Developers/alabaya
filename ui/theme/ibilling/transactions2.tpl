{include file="sections/header.tpl"}

<style>
.dataTables_wrapper .dt-buttons{
    display:flex;
    gap:10px;
}
#totals .line { margin: 2px 0; }
#totals .group { margin-top:4px; }
#totals .label { font-weight:600; }
</style>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{$_L['Records']}</h5>
            </div>
            <div class="ibox-content">  
                <form id="filterForm" class="row" style="margin: 10px 0;align-items: center;display: flex; flex-wrap: wrap;">

                    <div class="date-range">
                        <div class="date-range" style="display: flex; align-items: center;">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from">
                            </div>
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to">
                            </div>
                        </div>
                        <div id="dateError" style="color:red;margin-top:5px;"></div>
                    </div>

                    <div class="form-group">
                        <label>Branch</label>
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
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" name="type">
                            <option value="">All</option>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Method</label>
                        <input type="text" class="form-control" name="method">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" class="form-control" name="category">
                    </div>
                    <div class="" style="display: flex; gap:10px; margin-top: 5px;">
                        <button type="submit" class="btn btn-primary btn-block" style="margin-top:5px;">
                            <i class="fa fa-search" aria-hidden="true"></i> Filter
                        </button>
                        <button type="button" id="resetFilters" class="btn btn-default btn-block" style="margin-top:5px;">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Reset
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
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
</div>

{include file="sections/footer.tpl"}
