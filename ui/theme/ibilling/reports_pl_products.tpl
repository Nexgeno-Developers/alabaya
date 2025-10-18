{include file="sections/header.tpl"}

<style>
.dataTables_wrapper .dt-buttons{ display:flex; gap:10px; }
#totals .line { margin: 2px 0; }
#totals .label { font-weight:600; }
</style>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <div class="ibox float-e-margins">
      <div class="ibox-title">
        <h5>Profit / Loss • Products (Branch-wise)</h5>
      </div>

      <div class="ibox-content">
        <form id="plpFilterForm" class="row" style="margin:10px 0; align-items:center; display:flex; flex-wrap:wrap; gap:12px;">

          <div class="form-group">
            <label>Branch</label>
            <select name="branch_id" id="plp_branch" class="form-control">
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

          <div class="form-group" style="min-width:260px;">
            <label>Product</label>
            <input type="text" class="form-control" name="product_query" placeholder="Search by name or ID">
          </div>

          <div class="date-range" style="display:flex; align-items:center; gap:8px;">
            <div class="form-group">
              <label>From</label>
              <input type="date" class="form-control" name="date_from" id="plp_date_from">
            </div>
            <div class="form-group">
              <label>To</label>
              <input type="date" class="form-control" name="date_to" id="plp_date_to">
            </div>
          </div>
          <div id="plp_date_error" style="color:red; margin-top:5px;"></div>

          <div style="display:flex; gap:10px; margin-top:5px;">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
            <button type="button" id="plp_reset" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</button>
          </div>
        </form>

        <div class="table-responsive">
          <table id="plpTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Branch</th>
                    <th>Product</th>
                    <th class="text-right">Qty Sold</th>
                    <th class="text-right">Avg SP</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">COGS</th>
                    <th class="text-right">Profit</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">Totals</th>
                    <th colspan="3" id="totals"></th>
                </tr>
            </tfoot>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

{include file="sections/footer.tpl"}
